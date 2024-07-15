<?php
namespace App\Action\Game;

use Illuminate\Database\Capsule\Manager as DB;

class GameController
{
    private $db;

    public function __construct(DB $db)
    {
        $this->db = $db;
    }

    public function __invoke($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $type = $data['type'];
        $numberOfPlayers = $data['numberOfPlayers'];
        $region = $data['region'];
        $date = $data['date'];
        $comments = $data['comments'];
        $userId = $data['userId'];

        $game = $this->db->table('games')->insert([
            "created_by" => $userId,
            "type" => $type,
            "numberOfPlayers" => $numberOfPlayers,
            "region" => $region,
            "date" => date('Y-m-d', strtotime($date)),
            "comments" => $comments
        ]);

        if ($game) {
            $response->getBody()->write(json_encode(['message' => 'Game created successfully']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } else {
            $response->getBody()->write(json_encode(['message' => 'Game failed']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }

    }
}
