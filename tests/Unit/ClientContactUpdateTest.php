<?php

namespace Tests\Unit;

use App\Client;
use App\Http\Controllers\ClientController;
use App\Http\Requests\UpdateClientRequest;
use App\Repository\ClientRepository\EloquentClientRepository;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ClientContactUpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            foreach (['name', 'address', 'city', 'country', 'work_phone', 'contact_phone', 'work_email', 'contact_email', 'work_fax', 'password'] as $name) {
                $table->string($name)->nullable();
            }
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('cities', function (Blueprint $table) { $table->increments('id'); $table->string('name'); });
        Schema::create('client_contacts', function (Blueprint $table) {
            $table->increments('id'); $table->integer('client_id');
            foreach (['full_name', 'mobile_phone', 'work_phone', 'email'] as $name) $table->string($name)->nullable();
        });
        foreach ([9, 10] as $id) DB::table('clients')->insert($this->data() + ['id' => $id, 'password' => 'unchanged-password']);
        DB::table('client_contacts')->insert(['client_id' => 9, 'full_name' => 'Existing Contact', 'email' => 'existing@example.com']);
    }

    private function data(): array
    {
        return ['name' => 'Example Agency', 'address' => '123 Example Street', 'work_phone' => '+420 123456789',
            'work_email' => 'shared@example.com', 'contact_email' => 'contact@example.com'];
    }

    private function request(array $extra = [], array $files = []): UpdateClientRequest
    {
        $request = UpdateClientRequest::create('/clients/9', 'PUT', $extra + $this->data());
        $request->files->add($files);
        $route = new Route('PUT', 'clients/{client}', function () {});
        $route->bind($request);
        $request->setRouteResolver(function () use ($route) { return $route; });
        $request->setContainer($this->app)->setRedirector($this->app['redirect']);
        $request->headers->set('Accept', 'application/json');
        $request->validateResolved();
        return $request;
    }

    public function test_adding_contact_keeps_unchanged_duplicate_emails_and_existing_contact()
    {
        $request = $this->request(['contacts' => [
            1 => ['contact_full_name' => 'Existing Contact', 'contact_email' => 'existing@example.com'],
            2 => ['contact_full_name' => 'New Contact', 'contact_email' => 'new@example.com'],
        ]]);
        $response = (new ClientController(new EloquentClientRepository()))->update(9, $request);
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['Existing Contact', 'New Contact'], DB::table('client_contacts')->where('client_id', 9)->orderBy('id')->pluck('full_name')->all());
        $this->assertSame('shared@example.com', Client::findOrFail(9)->work_email);
        $this->assertSame('unchanged-password', Client::findOrFail(9)->password);
    }

    public function test_changed_email_still_cannot_duplicate_another_client()
    {
        DB::table('clients')->where('id', 10)->update(['work_email' => 'taken@example.com']);
        $this->expectException(ValidationException::class);
        $this->request(['work_email' => 'taken@example.com']);
    }

    public function test_changed_contact_email_still_cannot_duplicate_another_client()
    {
        DB::table('clients')->where('id', 10)->update(['contact_email' => 'taken@example.com']);
        $this->expectException(ValidationException::class);
        $this->request(['contact_email' => 'taken@example.com']);
    }

    public function test_new_unique_email_is_accepted()
    {
        $request = $this->request(['work_email' => 'unique@example.com']);
        $this->assertSame('unique@example.com', $request->validated()['work_email']);
    }

    /** @dataProvider unexpectedInputs */
    public function test_unexpected_input_returns_validation_errors_without_changing_data($input, $field)
    {
        $before = DB::table('client_contacts')->get()->toJson();
        try {
            $this->request($input);
            $this->fail('Unexpected input was accepted: ' . $field);
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey($field, $exception->errors());
        }
        $this->assertSame($before, DB::table('client_contacts')->get()->toJson());
        $this->assertSame('Example Agency', Client::findOrFail(9)->name);
    }

    public function unexpectedInputs(): array
    {
        return [
            'array client name' => [['name' => ['bad']], 'name'],
            'array work email' => [['work_email' => ['bad']], 'work_email'],
            'array address' => [['address' => ['bad']], 'address'],
            'array country' => [['country' => ['bad']], 'country'],
            'invalid email' => [['work_email' => 'not-an-email'], 'work_email'],
            'script company name' => [['name' => '<script>alert(1)</script>'], 'name'],
            'overlong address' => [['address' => str_repeat('a', 192)], 'address'],
            'string contacts' => [['contacts' => 'bad'], 'contacts'],
            'string contact row' => [['contacts' => ['bad']], 'contacts.0'],
            'blank contact name' => [['contacts' => [['contact_full_name' => '   ']]], 'contacts.0.contact_full_name'],
            'missing contact name' => [['contacts' => [['contact_email' => 'valid@example.com']]], 'contacts.0.contact_full_name'],
            'array contact name' => [['contacts' => [['contact_full_name' => ['bad']]]], 'contacts.0.contact_full_name'],
            'long contact name' => [['contacts' => [['contact_full_name' => str_repeat('a', 200)]]], 'contacts.0.contact_full_name'],
            'html contact name' => [['contacts' => [['contact_full_name' => '<img src=x onerror=alert(1)>']]], 'contacts.0.contact_full_name'],
            'invalid contact email' => [['contacts' => [['contact_full_name' => 'Valid Name', 'contact_email' => 'bad@@email']]], 'contacts.0.contact_email'],
            'array contact email' => [['contacts' => [['contact_full_name' => 'Valid Name', 'contact_email' => ['bad']]]], 'contacts.0.contact_email'],
            'invalid phone' => [['contacts' => [['contact_full_name' => 'Valid Name', 'contact_mobile_phone' => 'CALL ME']]], 'contacts.0.contact_mobile_phone'],
            'overlong optional field' => [['account_no' => str_repeat('a', 251)], 'account_no'],
            'unknown city' => [['city' => 999999], 'city'],
            'array password' => [['password' => ['bad']], 'password'],
            'invalid load marker' => [['contacts_loaded' => 'yes please'], 'contacts_loaded'],
            'too many contacts' => [['contacts' => array_fill(0, 101, ['contact_full_name' => 'Valid Name'])], 'contacts'],
            'punctuation company name' => [['name' => '---'], 'name'],
            'punctuation phone' => [['work_phone' => '---'], 'work_phone'],
            'unicode blank name' => [['contacts' => [['contact_full_name' => '　　']]], 'contacts.0.contact_full_name'],
            'punctuation contact name' => [['contacts' => [['contact_full_name' => '---']]], 'contacts.0.contact_full_name'],
            'punctuation contact phone' => [['contacts' => [['contact_full_name' => 'Valid Name', 'contact_work_phone' => '---']]], 'contacts.0.contact_work_phone'],
            'array fax' => [['work_fax' => ['bad']], 'work_fax'],
            'password exceeding encrypted storage' => [['password' => str_repeat('x', 32)], 'password'],
            'unicode password exceeding encrypted storage' => [['password' => str_repeat('é', 16)], 'password'],
        ];
    }

    /** @dataProvider unexpectedInputs */
    public function test_http_returns_422_for_unexpected_input($input, $field)
    {
        $this->withoutMiddleware();
        $before = DB::table('client_contacts')->get()->toJson();
        $response = $this->putJson('/clients/9', $input + $this->data());
        $response->assertStatus(422)->assertJsonValidationErrors($field);
        $this->assertSame($before, DB::table('client_contacts')->get()->toJson());
    }

    public function test_http_valid_contact_save_and_missing_client_response()
    {
        $this->withoutMiddleware();
        $response = $this->putJson('/clients/9', $this->data() + ['contacts' => [
            ['contact_full_name' => 'Valid Contact', 'contact_email' => 'valid@example.com'],
        ]]);
        $response->assertStatus(200)->assertJsonStructure(['route']);
        $this->assertSame('Valid Contact', DB::table('client_contacts')->where('client_id', 9)->value('full_name'));
        $this->putJson('/clients/999999', $this->data())->assertStatus(404);
    }

    public function test_missing_contact_payload_preserves_existing_contacts()
    {
        $before = DB::table('client_contacts')->get()->toJson();
        (new ClientController(new EloquentClientRepository()))->update(9, $this->request());
        $this->assertSame($before, DB::table('client_contacts')->get()->toJson());
    }

    public function test_explicit_removal_of_all_loaded_contacts_is_supported()
    {
        (new ClientController(new EloquentClientRepository()))->update(9, $this->request(['contacts_loaded' => 1]));
        $this->assertSame(0, DB::table('client_contacts')->where('client_id', 9)->count());
    }

    public function test_internal_attributes_cannot_be_changed_through_the_form()
    {
        $request = $this->request(['id' => 888, 'deleted_at' => '2020-01-01', 'created_at' => '2020-01-01']);
        (new ClientController(new EloquentClientRepository()))->update(9, $request);
        $this->assertNotNull(Client::find(9));
        $this->assertNull(Client::findOrFail(9)->deleted_at);
        $this->assertNull(DB::table('clients')->where('id', 888)->first());
    }

    public function test_contact_write_failure_rolls_back_client_and_existing_contacts()
    {
        $beforeClient = DB::table('clients')->where('id', 9)->first();
        $beforeContacts = DB::table('client_contacts')->get()->toJson();
        \App\ClientContacts::creating(function ($contact) {
            if ($contact->full_name === 'Fail Save') throw new \RuntimeException('Simulated write failure');
        });
        try {
            $request = $this->request(['name' => 'Changed Agency', 'contacts' => [
                ['contact_full_name' => 'Valid Contact'], ['contact_full_name' => 'Fail Save'],
            ]]);
            (new ClientController(new EloquentClientRepository()))->update(9, $request);
            $this->fail('Simulated write failure did not occur');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated write failure', $exception->getMessage());
        } finally {
            \App\ClientContacts::flushEventListeners();
        }
        $this->assertEquals($beforeClient, DB::table('clients')->where('id', 9)->first());
        $this->assertSame($beforeContacts, DB::table('client_contacts')->get()->toJson());
    }

    public function test_unicode_names_and_sparse_contact_indices_save_correctly()
    {
        $request = $this->request(['contacts' => [
            2 => ['contact_full_name' => '  Zoë O’Connor  '],
            99 => ['contact_full_name' => '李 明'],
        ]]);
        (new ClientController(new EloquentClientRepository()))->update(9, $request);
        $this->assertSame(['Zoë O’Connor', '李 明'], DB::table('client_contacts')->where('client_id', 9)->orderBy('id')->pluck('full_name')->all());
    }

    public function test_actual_attachment_field_rejects_executable_uploads_with_422()
    {
        $this->withoutMiddleware();
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('payload.php', '<?php echo "bad";');
        $response = $this->call('PUT', '/clients/9', $this->data(), [], ['attach' => [$file]], ['HTTP_ACCEPT' => 'application/json']);
        $response->assertStatus(422)->assertJsonValidationErrors('attach.0');
        $this->assertSame('Existing Contact', DB::table('client_contacts')->value('full_name'));
    }

    public function test_oversized_attachment_is_rejected()
    {
        $this->expectException(ValidationException::class);
        $this->request([], ['attach' => [\Illuminate\Http\UploadedFile::fake()->create('large.pdf', 5121, 'application/pdf')]]);
    }

    public function test_small_pdf_attachment_is_accepted()
    {
        $request = $this->request([], ['attach' => [\Illuminate\Http\UploadedFile::fake()->createWithContent('report.pdf', "%PDF-1.4\nQA sample")]]);
        $this->assertCount(1, $request->validated()['attach']);
    }

    public function test_password_at_storage_boundary_is_saved_without_truncation()
    {
        $password = str_repeat('x', 31);
        (new ClientController(new EloquentClientRepository()))->update(9, $this->request(['password' => $password]));
        $encrypted = Client::findOrFail(9)->password;
        $this->assertLessThanOrEqual(250, strlen($encrypted));
        $this->assertSame($password, \Illuminate\Support\Facades\Crypt::decryptString($encrypted));
        $this->assertSame(1, DB::table('client_contacts')->where('client_id', 9)->count());
    }
}
