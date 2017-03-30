# slimjwtauth

Demo: uploaded to heroku: 
- https://desolate-lake-91813.herokuapp.com

Slim auth with route middleware using JWT for dummies

	clone https://github.com/roselykm/slimjwtauth.git

put in http server - for example xampp (on windows)

let say your app is myapp (c:\xampp\htdocs\myapp)
	- create folder name api in myapp
	- copy all the files into API folder
	- dos prompt > go to c:\xampp\htdocs\myapp\api
	- execute: 
	
	composer install
	
What included in the composer package:

    	"slim/slim": "^3.7.0", //Slim Framework
    	"illuminate/database": "^5.4", //Eloquent ORM for database access
    	"firebase/php-jwt": "^4.0.0", //JWT
        "fzaninotto/faker": "^1.6", //Faker
        "vlucas/phpdotenv": "^2.4" //dot Env	

If you are using Auth0, put your Auth0 secret key in the .env file and use the token from Auth0 instead. Otherwise this API will generated its own token using Firebase JWT and the secret key in the .env file. Make sure you change that to your own secret key.

pinging the api:
    
	http://localhost/myapp/api

To get the JWT token (if not using Auth0 token). Remember, in real app, token generation will only given to user who do succesful login with username-password to a user database.

	GET http://localhost/myapp/api/token 

sample output:

	{
	   token: "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJyYmsubmV0IiwiaWF0IjoxNDkwNzE1NjIxLCJleHAiOjE0OTA3MTY4MjF9.OfNnti8pmmtikxjCTYxbhjcnoM4STG0HBHHe0TyYtm8"
	}

JWT authenticated route middleware. The middleware will return 401 status if there is no Authorization token in the header or the token is expired or had been tempered.

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


to add middleware to secured/authenticated-access-only route:
 
	$app->get('/testtoken', function (Request $request, Response $response) {
	   return $response->withJson($data, 200)
			   ->withHeader('Content-type', 'application/json');      
	})->add($jwtauth);

To access secured route using jquery and authorization token:

    //invalid token redirect
    $.ajaxSetup({
       statusCode: {
          401: function(){
	     //clear session data, jwt token etc
	     localStorage.clear();
	     
             // Redirec the to the login page here            
          }
       }
    });
  
     //set authorization token in header
     //sessionStorage.token is set either from Auth0 login
     //or from API login with username/password returning a token (SSL)
     //
     //if you are testing the heroku demo, GET a token and put the token in the header, replacing sessionStorage.token
     //for example:
     //- var tokenFromHeroku = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJyYmsubmV0IiwiaWF0IjoxNDkwNzE1NjIxLCJleHAiOjE0OTA3MTY4MjF9.OfNnti8pmmtikxjCTYxbhjcnoM4STG0HBHHe0TyYtm8";
     //- sessionStorage.token = tokenFromHeroku;
     //
     // set the jquery ajax global header
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
   
   As simple as A, B, C :P
