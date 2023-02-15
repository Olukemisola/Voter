<?php

namespace Middlewares;

use config\DB\DB;
use Error;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class SampleMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {

            $token = $request->getHeader("Authorization")[0] ?? null;
            if (!$token) {
                throw new Error("Authentication failed 0");
            }

            $splittedToken = explode(" ", $token);
            $tokenType = $splittedToken[0] ?? null;
            $accessToken = $splittedToken[1] ?? null; 

            if (!$tokenType || !$accessToken) {
                throw new Error("Authentication failed 1");
            }

            if ($tokenType === "bearer") {
                //verify token
                $key = $_ENV["AUTH_KEY"];
                $payload = JWT::decode($accessToken, new Key($key, "HS512"));

                if (!$payload) {
                    throw new Error("Authentication failed 2");
                }

                $id = $payload->id;
                $username = $payload->username ;

                $db = new DB;
                $conn = $db->connect();
                $sql = "SELECT id FROM voters WHERE id=$id AND username='$username'";
                $stmt = $conn->query($sql);
                $data = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$data) {
                    throw new Error("Authenticaion failed 3");
                }
            } else {
                throw new Error("Authentication failed 4");
            }

            // "bearer ;JDJF;LKJADS;FLJ;LVSN;LVPON";

            $response = $handler->handle($request);


            return $response;
        } catch (\Throwable | PDOException $th) {
            $error = [
                "message" => $th->getMessage()
            ];
 
            $resp = new Response();
            $resp->getBody()->write(json_encode($error));

            return $resp
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        }
    }
}
