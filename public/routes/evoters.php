<?php

use config\DB\DB;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Middlewares\SampleMiddleware;
use Slim\Factory\AppFactory;


$app->group("", function ($voterGroup) {

    $voterGroup->put('/update/voter/{id}', function (Request $request, Response $response) {
        $id = $request->getAttribute('id');
        $data = (array)json_decode($request->getBody()->getContents());

        // $name = $data['name'];
        // $gender = $data['gender'];
        // $email= $data['email'];
        // $username = $data['username'];
        try {
            $db = new DB;
            $conn = $db->connect();

            $sql = "SELECT name, gender, email,username FROM voters WHERE id=$id";
            $stmt = $conn->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $name = $data['name'] ??
                $result['name'];
            $gender = $data['gender'] ??
                $result['gender'];
            $email = $data['email'] ??
                $result['email'];
            $username = $data['username'] ??
                $result['username'];


            $sql = "SELECT id, username FROM voters WHERE username= '$username'";
            $stmt = $conn->query($sql);
            $validation = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($validation != null && $validation["id"] != $id) {
                throw new Error("Username has been taken, try another username!");
            }


            $sql = "UPDATE voters SET name=:name, gender=:gender, email=:email, username=:username WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();

            if (!$result) {
                throw new Error("Error occurred while updating");
            }

            $sql = "SELECT id,name, gender, email,username FROM voters WHERE id=$id";
            $stmt = $conn->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $response->getBody()->write(json_encode($result));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } catch (PDOException $e) {
            $error = [
                "message" => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        } catch (Throwable $e) {
            $error = [
                "message" => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(400);
        }
    });

    $voterGroup->put('/update/password/{id}', function (Request $request, Response $response) {
        $id = $request->getAttribute('id');
        $data = (array) json_decode($request->getBody()->getContents());

        $old_Password = $data['oldPassword'];
        $new_Password = $data['newPassword'];

        try {
            $db = new DB;
            $conn = $db->connect();

            $sql = "SELECT password FROM voters WHERE id=$id";
            $stmt = $conn->query($sql);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $encrypted_existingPassword = $result['password'];

            if (!password_verify($old_Password, $encrypted_existingPassword)) {
                throw new Error("invalid password");
            }
            $encrypted_newPassword = password_hash($new_Password, null);

            $sql = "UPDATE voters SET password=:password WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':password', $encrypted_newPassword);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();

            if (!$result) {
                throw new Error("Error occurred while updating");
            }

            $response->getBody()->write(json_encode(["message" => "successful"]));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } catch (PDOException $e) {
            $error = [
                "message" => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        } catch (Throwable $e) {
            $error = [
                "message" => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(400);
        }
    });

    $voterGroup->get('/get/voter/{id}', function (Request $request, Response $response) {
        $id = $request->getAttribute('id');
        $sql = "SELECT id, name, gender, email,username FROM voters WHERE id=$id";
        try {
            $db = new DB;
            $conn = $db->connect();
            $stmt = $conn->query($sql);
            $evoters = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$evoters) {
                throw new Error("No users found");
            }
            $response->getBody()->write(json_encode($evoters));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } catch (PDOException $e) {
            [
                $error = "message" => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        } catch (Throwable $e) {
            $error = [
                "message" => $e->getMessage()
            ];
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(400);
        }
    });
})->addMiddleware(new SampleMiddleware());




$app->post('/register/voter', function (Request $request, Response $response, array $args) {
    $data = (array) json_decode($request->getBody()->getContents());

    $name = $data['name'];
    $gender = $data['gender'];
    $email = $data['email'];
    $username = $data['username'];
    $password = $data['password'];

    $password = password_hash($password, null);

    try {
        $db = new DB;
        $conn = $db->connect();
        $sql = "SELECT username FROM voters WHERE username='$username'";
        $stmt = $conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $username = $data['username'];

        if ($result != Null) {
            throw new Error("Username has been taken, try another username");
        }
        $sql = "INSERT INTO voters(name, gender, email, username, password) VALUES (:name,:gender, :email,:username,:password)";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        unset($data['password']);

        $result = $stmt->execute();

        $sql = "SELECT id,name,gender,email,username FROM voters WHERE username='$username'";
        $stmt = $conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($result));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = [
            "message" => $e->getMessage()
        ];
        $response->getBody()->write(json_encode($error));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    } catch (Throwable $e) {
        $error = [
            "message" => $e->getMessage()
        ];
        $response->getBody()->write(json_encode($error));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }
});
$app->post('/login/voter', function (Request $request, Response $response) {
    $data = (array) json_decode($request->getBody()->getContents());

    $username = $data['username'];
    $password = $data['password'];

    $oldPassword = password_hash($password, null);

    try {
        $db = new DB;
        $conn = $db->connect();

        #region Verify credentials
        $sql = "SELECT id,username,password FROM voters WHERE username='$username'";
        $stmt = $conn->query($sql);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            throw new Error("Invalid credentials");
        }

        $encrypted_password = $data['password'];
        if (!password_verify($password, $encrypted_password)) {
            throw new Error("Invalid credentials");
        }
        #endregion

        #region create token
        $payload = [
            "id" => $data['id'],
            "username" => $data['username']
        ];

        $key = $_ENV["AUTH_KEY"];

        $token = JWT::encode($payload, $key, "HS512");
        #endregion

        $response->getBody()->write(json_encode(["token" => $token]));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = [
            "message" => $e->getMessage()
        ];

        $response->getBody()->write(json_encode($error));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    } catch (Throwable $e) {
        $error = ["message" => $e->getMessage()];
        $response->getBody()->write(json_encode($error));

        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }
});
