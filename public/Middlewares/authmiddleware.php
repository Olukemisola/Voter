 <?php
// namespace Middlewares;

// use Config\DB\DB;
// use Error;
// use Exception;
// use Firebase\JWT\JWT;
// use PDO;
// use PDOException;
// use Psr\Http\Message\ServerRequestInterface;
// use Psr\Http\Message\ResponseInterface;
// use Psr\Http\Server\MiddlewareInterface;
// use Psr\Http\Server\RequestHandlerInterface;
// use Slim\Psr7\Response; 

// class authmiddleware implements MiddlewareInterface
// {
 
//     public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
//     {
//         //Codes here will execute before entry into the route

//         // //
//         $errorResponse = (new Response);
//         ->withHeader('content-type', 'application/json');
//         ->withStatus(401);
         
//         $authorizationValues= $request->getHeader(authorization);
//         $authorizationValue= $authorizationValues[0];
//         $authorizationValues= explode("", $authorizationvalue);
//         $authType= $authorizationValues[0];
       

//         if(isset($authorizationValues[0])) {
//             $errorResponse->getBody()
//             ->write(json_encode(["message" => "array key exist"]));
//         return $errorResponse;
//           }else{
//             $errorResponse->getBody()
//             ->write(json_encode(["message" => "invalid authentication method"]));
//         return $errorResponse;
//        }

//        $authToken= $authorizationValues[1];

//        if (Bearer!= $token) {
//         $errorResponse->getBody()
//         ->write(json_encode(["message" => "array key exist"]));
//     return $errorResponse;
//       }else{
//         $errorResponse->getBody()
//         ->write(json_encode(["message" => "invalid authentication method"]));
//     return $errorResponse;
//        }
//     }
// }