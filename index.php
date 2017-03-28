<?php
	ini_set("date.timezone", "Asia/Kuala_Lumpur");

	header('Access-Control-Allow-Origin: *');	

	//*
   // Allow from any origin
   if (isset($_SERVER['HTTP_ORIGIN'])) {
      // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
      // you want to allow, and if so:
      header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
      header('Access-Control-Allow-Credentials: true');
      header('Access-Control-Max-Age: 86400');    // cache for 1 day
   }

   // Access-Control headers are received during OPTIONS requests
   if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

      if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
         header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");         

     	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
         header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

     	exit(0);
   }
   //*/

	require("vendor/autoload.php");
	 
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	//load environment variable
	$dotenv = new Dotenv\Dotenv(__DIR__);
	$dotenv->load();
	 
	$app = new \Slim\App;	

	//JWT
	use \Firebase\JWT\JWT;

	//JWT authentication middleware
	$jwtauth = function ($request, $response, $next) {
	   $token_array = $request->getHeader('HTTP_AUTHORIZATION');

	   if (count($token_array) == 0) {
		   $data = Array(
				"jwt_status" => "token_not_exist"
			);	

	   	return $response->withJson($data, 401)
                         ->withHeader('Content-type', 'application/json');  				   	
	   }

		$token = $token_array[0];
    	try
    	{
    		$tokenDecoded = JWT::decode($token, getenv('JWT_SECRET'), array('HS256'));
	   	$response = $next($request, $response);
	   	return $response;    		
		}
		catch(Exception $e)
		{
		   $data = Array(
				"jwt_status" => "token_invalid"
			);	

	   	return $response->withJson($data, 401)
                         ->withHeader('Content-type', 'application/json');
		}		
	};

	$app->get('/', function (Request $request, Response $response) {	
	   $response->getBody()->write("Welcome to Slim based API");

	   return $response->withStatus(200)
                      ->withHeader('Content-type', 'text/plain');
	});	

	$app->get('/token', function (Request $request, Response $response) {	
  		//create JWT token
  		$date = date_create();
		$jwtIAT = date_timestamp_get($date);
		$jwtExp = $jwtIAT + (20 * 60); //expire after 20 minutes

		$jwtToken = array(
				"iss" => "rbk.net", //client key
				"iat" => $jwtIAT, //issued at time
				"exp" => $jwtExp, //expire
		);
		$token = JWT::encode($jwtToken, getenv('JWT_SECRET'));

		$data = array('token' => $token);
	   return $response->withJson($data, 200)
                      ->withHeader('Content-type', 'application/json');		
	});

	//debugging
	$app->get('/headers', function (Request $request, Response $response) {	

		/*
		$headers = $request->getHeaders();
			foreach ($headers as $name => $values) {
    			$response->getBody()->write($name . ": " . implode(", ", $values) . "<br />");
		}
		*/

		$token_array = $request->getHeader('HTTP_AUTHORIZATION');
		$response->getBody()->write(count($token_array));

	   return $response->withStatus(200)
                      ->withHeader('Content-type', 'text/plain');
   });             	

	$app->get('/testtoken', function (Request $request, Response $response) {	
		//$response->getBody()->write("Secure access");

	   //return $response->withStatus(200)
      //                ->withHeader('Content-type', 'text/plain');

	   $data = Array(
			"jwt_status" => "token aunthenticated succesfully and valid"
		);	

   	return $response->withJson($data, 200)
                      ->withHeader('Content-type', 'application/json');      
	})->add($jwtauth);

	$app->run();