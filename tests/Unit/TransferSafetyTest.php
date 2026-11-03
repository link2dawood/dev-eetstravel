<?php

namespace Tests\Unit;

use App\Transfer;
use App\Http\Requests\StoreTransferRequest;
use App\Library\Services\DeleteModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class TransferSafetyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (['buses', 'drivers', 'tour_packages'] as $name) {
            Schema::create($name, function ($table) {
                $table->increments('id');
                $table->integer('transfer_id')->nullable();
                $table->integer('type')->nullable();
                $table->integer('reference')->nullable();
                $table->softDeletes();
            });
        }
        Schema::create('criteria_types', function ($table) {
            $table->increments('id');
            $table->string('service_type');
        });
        Schema::create('criterias', function ($table) {
            $table->increments('id');
            $table->integer('criteria_type');
        });
        DB::table('criteria_types')->insert([['id' => 1, 'service_type' => 'transfer'], ['id' => 2, 'service_type' => 'guide']]);
        DB::table('criterias')->insert([['id' => 1, 'criteria_type' => 1], ['id' => 2, 'criteria_type' => 2]]);
    }

    public function test_company_cannot_be_deleted_while_used_by_a_tour_or_fleet()
    {
        $company = new Transfer(['name' => 'Company']);
        $company->id = 42;
        $guard = new DeleteModel();
        $this->assertNull($guard->check($company, 'Transfer'));
        foreach (['buses', 'drivers', 'tour_packages'] as $table) {
            DB::table($table)->insert(['transfer_id' => 42, 'type' => 3, 'reference' => 42]);
            $this->assertStringContainsString('cannot be deleted', $guard->check($company, 'Transfer'));
            DB::table($table)->delete();
        }
    }

    public function test_mass_assignment_cannot_set_identity_or_timestamps()
    {
        $company = new Transfer(['id' => 999, 'created_at' => '2000-01-01', 'name' => "Müller's Reisen"]);
        $this->assertNull($company->id);
        $this->assertNull($company->created_at);
        $this->assertSame("Müller's Reisen", $company->name);
    }

    public function test_legacy_update_route_requires_transfer_edit_permission()
    {
        $route = app('router')->getRoutes()->match(\Illuminate\Http\Request::create('/transfer/42/update', 'POST'));
        $this->assertSame('transfer.edit', $route->getAction('permissions'));
    }

    public function test_criteria_and_attachments_are_validated()
    {
        $rules = (new StoreTransferRequest())->rules();
        $rules = array_intersect_key($rules, array_flip(['name', 'criterias', 'criterias.*', 'attach', 'attach.*']));
        $this->assertTrue(Validator::make(['name' => "Müller's Reisen", 'criterias' => [1]], $rules)->passes());
        foreach ([0, 2, 999999] as $id) {
            $this->assertTrue(Validator::make(['name' => 'Company', 'criterias' => [$id]], $rules)->fails());
        }
        $this->assertTrue(Validator::make(['name' => 'Company', 'attach' => [UploadedFile::fake()->createWithContent('evil.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>')]], $rules)->fails());
    }

    public function test_alternate_attachment_field_cannot_bypass_upload_validation()
    {
        foreach ([new StoreTransferRequest(), new \App\Http\Requests\UpdateTransferRequest()] as $request) {
            $rules = $request->rules();
            $this->assertTrue(Validator::make(['attach_bus' => [UploadedFile::fake()->create('evil.svg')]], ['attach_bus' => $rules['attach_bus']])->fails());
        }
    }

    public function test_criteria_normalization_preserves_multiple_selections()
    {
        $request = new class extends StoreTransferRequest {
            public function normalizeForTest() { $this->prepareForValidation(); }
        };
        foreach (['1,2', ['1', '2']] as $input) {
            $request->replace(['criterias' => $input]);
            $request->normalizeForTest();
            $this->assertSame(['1', '2'], $request->input('criterias'));
        }
    }

    public function test_blocked_json_delete_reports_failure_and_keeps_company()
    {
        $company = new Transfer(['name' => 'Company']);
        $company->id = 42;
        $repository = $this->createMock(\App\Repository\TransferRepository\EloquentTransferRepository::class);
        $repository->method('getById')->with(42)->willReturn($company);
        $repository->expects($this->never())->method('deleteById');
        DB::table('buses')->insert(['transfer_id' => 42]);
        $request = \Illuminate\Http\Request::create('/transfer/42/delete', 'POST', [], [], [], ['HTTP_ACCEPT' => 'application/json']);
        $request->setLaravelSession(app('session.store'));
        app()->instance('request', $request);
        $response = (new \App\Http\Controllers\TransferController($repository))->destroy(42, new DeleteModel());
        $this->assertSame(422, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        $this->assertStringContainsString('active buses or drivers', $response->getData(true)['message']);
    }

    public function test_nested_criteria_are_rejected_without_a_server_error()
    {
        $request = new class extends StoreTransferRequest {
            public function normalizeForTest() { $this->prepareForValidation(); }
        };
        $request->replace(['criterias' => [['1']]]);
        $request->normalizeForTest();
        $rules = array_intersect_key($request->rules(), array_flip(['criterias', 'criterias.*']));
        $this->assertTrue(Validator::make($request->all(), $rules)->fails());
    }

    public function test_failed_criteria_save_rolls_back_new_company()
    {
        Schema::create('transfers', function ($table) {
            $table->increments('id');
            $table->string('name');
        });
        $repository = $this->createMock(\App\Repository\TransferRepository\EloquentTransferRepository::class);
        $repository->method('create')->willReturnCallback(function ($data) {
            $id = DB::table('transfers')->insertGetId(['name' => $data['name']]);
            $company = new Transfer(['name' => $data['name']]);
            $company->id = $id;
            return $company;
        });
        $request = StoreTransferRequest::create('/', 'POST', ['name' => 'Company', 'criterias' => [1]]);
        try {
            // The absent criteria-link table simulates a database failure after company creation.
            (new \App\Http\Controllers\TransferController($repository))->store($request);
            $this->fail('Expected criteria save failure.');
        } catch (\Illuminate\Database\QueryException $exception) {
            $this->assertSame(0, DB::table('transfers')->count());
        }
    }
}
