<?php

namespace Tests\Feature;

use App\User;
use App\Http\Controllers\ChatController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ChatNotificationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
        });

        Schema::create('chats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type');
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('chat_user', function (Blueprint $table) {
            $table->integer('chat_id');
            $table->integer('user_id');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chat_id');
            $table->integer('user_id');
            $table->text('message')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        DB::table('users')->insert([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
            ['id' => 4],
        ]);

        $user = new User();
        $user->setAttribute('id', 1);
        Auth::login($user);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_user');
        Schema::dropIfExists('chats');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_notifications_keep_user_order_and_count_with_three_queries(): void
    {
        $recent = now()->subSeconds(5)->toDateTimeString();
        $old = now()->subSeconds(30)->toDateTimeString();

        DB::table('chats')->insert([
            ['id' => 1, 'type' => 1],
            ['id' => 2, 'type' => 3],
            ['id' => 3, 'type' => 3],
            ['id' => 4, 'type' => 3],
            ['id' => 5, 'type' => 2],
        ]);
        DB::table('chat_user')->insert([
            ['chat_id' => 2, 'user_id' => 1],
            ['chat_id' => 2, 'user_id' => 2],
            ['chat_id' => 3, 'user_id' => 1],
            ['chat_id' => 3, 'user_id' => 3],
            ['chat_id' => 4, 'user_id' => 1],
            ['chat_id' => 4, 'user_id' => 3],
            ['chat_id' => 4, 'user_id' => 4],
            ['chat_id' => 5, 'user_id' => 1],
            ['chat_id' => 5, 'user_id' => 4],
        ]);
        DB::table('chat_messages')->insert([
            ['chat_id' => 1, 'user_id' => 2, 'message' => 'Main', 'created_at' => $recent],
            ['chat_id' => 2, 'user_id' => 2, 'message' => 'Recent', 'created_at' => $recent],
            ['chat_id' => 2, 'user_id' => 1, 'message' => 'Sent by current user', 'created_at' => $recent],
            ['chat_id' => 2, 'user_id' => 2, 'message' => 'Old', 'created_at' => $old],
            ['chat_id' => 4, 'user_id' => 3, 'message' => 'Group direct chat', 'created_at' => $recent],
            ['chat_id' => 5, 'user_id' => 4, 'message' => 'Custom chat', 'created_at' => $recent],
        ]);

        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = $query->sql;
        });

        $controller = (new \ReflectionClass(ChatController::class))->newInstanceWithoutConstructor();
        $response = $controller->getChatNotifications(Request::create(
            '/chat/getChatNotifications',
            'GET',
            ['user' => 'probe']
        ));
        $payload = json_decode($response, true);

        $this->assertSame([1, 0, 0], $payload['direct_chats']);
        $this->assertSame(1, $payload['main_chat']);
        $this->assertSame('probe', $payload['sendUser']);
        $this->assertCount(3, $queries);
    }
}
