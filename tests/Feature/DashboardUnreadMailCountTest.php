<?php

namespace Tests\Feature;

use App\Helper\DashboardHelper;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardUnreadMailCountTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_login');
            $table->integer('email_server');
            $table->boolean('is_seen')->nullable();
        });

        $user = new User();
        $user->setAttribute('email_login', 'agent@example.com');
        $user->setAttribute('email_server', 1);
        Auth::login($user);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('emails');

        parent::tearDown();
    }

    public function test_unread_mail_count_uses_an_aggregate_query_and_scopes_to_the_user_mailbox(): void
    {
        DB::table('emails')->insert([
            ['user_login' => 'agent@example.com', 'email_server' => 1, 'is_seen' => false],
            ['user_login' => 'agent@example.com', 'email_server' => 1, 'is_seen' => false],
            ['user_login' => 'agent@example.com', 'email_server' => 1, 'is_seen' => true],
            ['user_login' => 'agent@example.com', 'email_server' => 2, 'is_seen' => false],
            ['user_login' => 'other@example.com', 'email_server' => 1, 'is_seen' => false],
        ]);

        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = strtolower($query->sql);
        });

        $this->assertSame(2, DashboardHelper::getCountUnreadMailMessage());
        $this->assertCount(1, $queries);
        $this->assertStringContainsString('count(*) as aggregate', $queries[0]);
    }
}
