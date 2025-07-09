<?php

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;

class GroupTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();

        $app = AppFactory::create();

        $app->addBodyParsingMiddleware();
        $app->addRoutingMiddleware();
        $app->addErrorMiddleware(true, true, true);

        // test routes
        $app->post('/groups', [App\Controllers\GroupController::class, 'create']);

        $this->app = $app;

        // cleaning database before test
        \App\Models\Message::truncate();
        \App\Models\GroupMember::truncate();
        \App\Models\Group::truncate();
        \App\Models\User::truncate();
    }

    public function testCreateGroupReturns201()
    {
        $requestFactory = new ServerRequestFactory();
        $request = $requestFactory->createServerRequest('POST', '/groups');
        $request = $request->withHeader('Content-Type', 'application/json');

        $streamFactory = new StreamFactory();
        $body = $streamFactory->createStream(json_encode([
            'name' => 'Test Group',
            'creator_id' => 999
        ]));

        $request = $request->withBody($body);

        $response = $this->app->handle($request);
        $this->assertEquals(200, $response->getStatusCode());

        $payload = json_decode((string) $response->getBody(), true);
        $this->assertEquals('Test Group', $payload['name']);
        $this->assertEquals(999, $payload['creator_id']);
    }

    public function testJoinGroup()
{
    $group = \App\Models\Group::create([
        'name' => 'Test Join Group',
        'creator_id' => 999,
    ]);

    $requestFactory = new ServerRequestFactory();
    $request = $requestFactory->createServerRequest('POST', "/groups/{$group->id}/join")
        ->withHeader('Content-Type', 'application/json');

    $stream = (new StreamFactory())->createStream(json_encode([
        'user_id' => 1234,
    ]));

    $request = $request->withBody($stream);

    $this->app->post('/groups/{id}/join', [App\Controllers\GroupController::class, 'join']);

    $response = $this->app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());

    $payload = json_decode((string)$response->getBody(), true);
    $this->assertEquals($group->id, $payload['group_id']);
    $this->assertEquals(1234, $payload['user_id']);
}

public function testSendMessage()
{
    $group = \App\Models\Group::create([
        'name' => 'Message Test',
        'creator_id' => 777,
    ]);

    $request = (new ServerRequestFactory())
        ->createServerRequest('POST', "/groups/{$group->id}/messages")
        ->withHeader('Content-Type', 'application/json');

    $body = (new StreamFactory())->createStream(json_encode([
        'user_id' => 42,
        'message' => 'Test message',
    ]));

    $request = $request->withBody($body);

    $this->app->post('/groups/{id}/messages', [App\Controllers\GroupController::class, 'sendMessage']);
    $response = $this->app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());

    $json = json_decode((string) $response->getBody(), true);
    $this->assertEquals('Test message', $json['message']);
    $this->assertEquals(42, $json['user_id']);
}

public function testGetMessages()
{
    $group = \App\Models\Group::create([
        'name' => 'Listing Test',
        'creator_id' => 333,
    ]);

    \App\Models\Message::create([
        'group_id' => $group->id,
        'user_id' => 55,
        'message' => 'Hiiiii Bunq!',
    ]);

    $request = (new ServerRequestFactory())
        ->createServerRequest('GET', "/groups/{$group->id}/messages");

    $this->app->get('/groups/{id}/messages', [App\Controllers\GroupController::class, 'getMessages']);
    $response = $this->app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());

    $data = json_decode((string) $response->getBody(), true);
    $this->assertIsArray($data);
    $this->assertCount(1, $data);
    $this->assertEquals('Hiiiii Bunq!', $data[0]['message']);
}

public function testGetAllGroups()
{
    \App\Models\Group::create([
        'name' => 'Bunq teaaaam',
        'creator_id' => 1
    ]);

    \App\Models\Group::create([
        'name' => 'Netherlands-Turkey teaam',
        'creator_id' => 2
    ]);

    $request = (new \Slim\Psr7\Factory\ServerRequestFactory())
        ->createServerRequest('GET', '/groups');

    $this->app->get('/groups', [App\Controllers\GroupController::class, 'getAll']);
    $response = $this->app->handle($request);

    $this->assertEquals(200, $response->getStatusCode());

    $json = json_decode((string) $response->getBody(), true);
    $this->assertIsArray($json);
    $this->assertGreaterThanOrEqual(2, count($json));
    $this->assertEquals('Bunq teaaaam', $json[0]['name']);
}
}