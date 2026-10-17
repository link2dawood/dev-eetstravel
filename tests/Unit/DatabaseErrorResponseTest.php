<?php

namespace Tests\Unit;

use App\Exceptions\Handler;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use PDOException;
use Tests\TestCase;

class DatabaseErrorResponseTest extends TestCase
{
    /** @dataProvider failedRequestMethods */
    public function test_missing_database_table_does_not_redirect($method)
    {
        $previous = new PDOException('Missing users table');
        $previous->errorInfo = ['42S02', 1146, 'Missing users table'];
        $exception = new QueryException('select * from users', [], $previous);
        $request = Request::create('/login', $method);
        $request->headers->set('referer', 'http://localhost/login');

        $response = (new Handler($this->app))->render($request, $exception);

        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($response->headers->has('Location'));
        $this->assertStringContainsString('Database unavailable', $response->getContent());
        $this->assertStringNotContainsString('select * from users', $response->getContent());
    }

    public function failedRequestMethods()
    {
        return [['GET'], ['HEAD'], ['POST']];
    }

    public function test_database_is_isolated_from_application_data()
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }
}
