<?php

namespace App\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Message;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GroupController
{
    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (!isset($data['name'], $data['creator_id'])) {
            $response->getBody()->write(json_encode([
                'error' => 'name and creator_id are required'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $group = Group::create([
            'name' => $data['name'],
            'creator_id' => $data['creator_id'],
        ]);

        $response->getBody()->write(json_encode($group));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function join(Request $request, Response $response, array $args): Response
{
    $groupId = $args['id'];
    $data = $request->getParsedBody();

    if (!isset($data['user_id'])) {
        $response->getBody()->write(json_encode([
            'error' => 'user_id is required'
        ]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $exists = GroupMember::where('group_id', $groupId)
                         ->where('user_id', $data['user_id'])
                         ->first();

    if ($exists) {
        $response->getBody()->write(json_encode([
            'message' => 'Already joined'
        ]));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    $member = GroupMember::create([
        'group_id' => $groupId,
        'user_id' => $data['user_id'],
    ]);

    $response->getBody()->write(json_encode($member));
    return $response->withHeader('Content-Type', 'application/json');
}

public function sendMessage(Request $request, Response $response, array $args): Response
{
    $groupId = $args['id'];
    $data = $request->getParsedBody();

    if (!isset($data['user_id'], $data['message'])) {
        $response->getBody()->write(json_encode([
            'error' => 'user_id and message are required'
        ]));
        return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $message = Message::create([
        'group_id' => $groupId,
        'user_id' => $data['user_id'],
        'message' => $data['message']
    ]);

    $response->getBody()->write(json_encode($message));
    return $response->withHeader('Content-Type', 'application/json');
}

public function getMessages(Request $request, Response $response, array $args): Response
{
    $groupId = $args['id'];
    $queryParams = $request->getQueryParams();

    $messagesQuery = Message::where('group_id', $groupId)->orderBy('created_at', 'asc');

    if (isset($queryParams['since'])) {
        $messagesQuery->where('created_at', '>', $queryParams['since']);
    }

    $messages = $messagesQuery->get();

    $response->getBody()->write($messages->toJson());
    return $response->withHeader('Content-Type', 'application/json');
}

public function getAll(Request $request, Response $response): Response
{
    $groups = \App\Models\Group::all();

    $response->getBody()->write($groups->toJson());
    return $response->withHeader('Content-Type', 'application/json');
}
}