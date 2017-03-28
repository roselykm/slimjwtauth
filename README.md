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


 
