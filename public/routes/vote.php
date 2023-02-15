
<?php

use config\DB\DB;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app->post('/casting/vote', function (Request $request, Response $response) {
  $data = (array) json_decode($request->getBody()->getContents());
  $vusername = $data['vusername'];
  $partyname = $data['partyname'];

  try {
    $db = new DB;
    $conn = $db->connect();

    #region Check if voter is a registered voter
    $sql = "SELECT username FROM voters WHERE username='$vusername'";
        $stmt = $conn->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
      throw new Error("voter has not been registered");
    }
    #endregion

    #region Check if voter has previously voted
    $sql = "SELECT vusername FROM vote WHERE vusername='$vusername'";
    $stmt = $conn->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $vusername = $data['vusername'];

    if ($result != Null) {
      throw new Error("This user has previously voted.");
    }
    #endregion

    $sql = "INSERT INTO vote(vusername, partyname)VALUES (:vusername, :partyname)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':vusername', $vusername);
    $stmt->bindParam(':partyname', $partyname);
    // $stmt->bindParam(':id', $id);

    $result = $stmt->execute();
    $response->getBody()->write(json_encode($data));

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




// })