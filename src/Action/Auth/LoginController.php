<?php
namespace App\Action\Auth;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as DB;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Log\LoggerInterface;

class LoginController
{
    private $db;
    private $jwt_secret = 'your_jwt_secret';

    public function __construct(DB $db)
    {
        $this->db = $db;
        // $this->jwt_secret = $jwt_secret;
    }

    public function login($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $email = $data['email'];
        $password = $data['password'];

        $user = $this->db->table('users')->where('email', $email)->first();

        if ($user && password_verify($password, $user->password)) {
            $issuedAt = time();
            $expirationTime = $issuedAt + 3600; // jwt valid for 1 hour
            $payload = [
                'iat' => $issuedAt,
                'exp' => $expirationTime,
                'userId' => $user->id,
            ];

            $token = JWT::encode($payload, $this->jwt_secret, 'HS256');

            $response->getBody()->write(json_encode(['token' => $token, 'user' => $user]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } else {
            $response->getBody()->write(json_encode(['message' => 'Invalid credentials']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }
}
