# slimjwtauth
Slim auth using JWT for dummies

clone

put in http server - for example xampp (on windows)

let say your app is myapp (c:\xampp\htdocs\myapp)
- create folder in api in myapp
- copy all the files into API folder
- dos prompt > go to c:\xampp\htdocs\myapp\api
- execute: composer install

test api:
http://localhost/myapp/api

to get JWT token
GET http://localhost/myapp/api/token 

sample output:

{
token: "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJyYmsubmV0IiwiaWF0IjoxNDkwNzE1NjIxLCJleHAiOjE0OTA3MTY4MjF9.OfNnti8pmmtikxjCTYxbhjcnoM4STG0HBHHe0TyYtm8"
}

JWT authenticated route middleware:

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


 to add middleware to route:
 
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
