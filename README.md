# slimjwtauth
Slim auth using JWT for dummies

clone

put in http server - for example xampp (on windows)

let say your app is myapp (c:\xampp\htdocs\myapp)
- create folder name api in myapp
- copy all the files into API folder
- dos prompt > go to c:\xampp\htdocs\myapp\api
- execute: composer install

If you are using Auth0, put your Auth0 secret key in the .env file and use the token from Auth0 instead. Otherwise this API will generated its own token using Firebase JWT

test api:
http://localhost/myapp/api

to get JWT token (if not using Auth0 token)
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

To access secured route using jquery and authorization token:

    //invalid token redirect
    $.ajaxSetup({
      statusCode: {
         401: function(){
            // Redirec the to the login page here            
         }
      }
   });
  
     //set authorization token in header
     //sessionStorage.token is set either from Auth0 login
     //or from API login with username/password returning a token (SSL)
     $.ajaxPrefilter(function( options, oriOptions, jqXHR ) {
        jqXHR.setRequestHeader("Authorization", sessionStorage.token);
     }); 
   
     //access secured route using the token	
     $.ajax({
       type: "GET",
       url: 'localhost/myapp/api/testtoken',
       dataType: "json",
       success: function(data){
          //do something here with the json data from the API
       },
       error: function() {
       }
    });
   
   See, even a dummy can easily do JWT authententication :P
