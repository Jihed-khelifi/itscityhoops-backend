<?php
namespace App\Action\Auth;

use Illuminate\Database\Capsule\Manager as DB;
use Psr\Log\LoggerInterface;

final class RegisterController
{
    private $db;
    private LoggerInterface $logger;

    public function __construct(DB $db, LoggerInterface $logger)
    {
        $this->db = $db;
        // $this->jwt_secret = $jwt_secret;
        $this->logger = $logger;
    }

    public function register($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $email = $data['email'];
        $fullname = $data['fullname'];
        $gender = $data['gender'];
        $dob = $data['dob'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $user = $this->db->table('users')->insert([
            "fullname" => $fullname,
            "gender" => $gender,
            "dob" => date('Y-m-d', strtotime($dob)),
            'email' => $email,
            'password' => $password,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        if ($user) {
            $response->getBody()->write(json_encode(['message' => 'User registered successfully']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } else {
            $response->getBody()->write(json_encode(['message' => 'Registration failed']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

}
