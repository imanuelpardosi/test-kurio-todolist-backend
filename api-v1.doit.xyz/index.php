
<?php
 
require_once 'include/DbHandler.php';
require_once 'include/PassHash.php';
require 'libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/**
 * ----------- METHODS WITHOUT AUTHENTICATION ---------------------------------
 */

//5779ed6b

 $app->get('/check', function (){
	$response = array();
	
	$app = new \Slim\Slim();
	$api_key = $app->request->headers->get('API-KEY');
	$db = new DbHandler();
	//print_r($api_key);	
	echo 'Current PHP version: ' . phpversion();

	if(checkIfAPIKeyVerified($api_key)){
		$response["status"] = "OK";		
		$response["message"] = "api-up";
	}else{
		$response["status"] = "error";	
		$response["message"] = "invalid api-key";
		$response["api_key"] = "kVBkcfp3ivy3GqzSn62Dn2sW27X408VN";
		//$response["access_token"] = "$ 2y$ 13$ ejbtXH8SgXKwnAEZDFFctuzjy8Vgmz/UlhWSuYKVuZpBY9CBNFGEW";
	}
	//echo 'api-key : '.$headers;
	echoRespnse(200, $response);
});


$app->post('/login', function(){
	$app = new \Slim\Slim();
	$response = array();	
	$tmp = array();
	
	$api_key = $app->request->headers->get('API-KEY');
	$db = new DbHandler();
	
	if($api_key == null){
		$response["status"] = "ERROR";	
		$response["message"] = "tidak punya hak akses";
	}
	else{
		if(!array_key_exists('username', $app->request()->post()) | 
		!array_key_exists('password', $app->request()->post())){
			$response["status"] = "ERROR";	
			$response["message"] = "isi field yang dibutuhkan";			
		}
		else{
			if(!checkIfAPIKeyVerified($api_key)){
				$response["status"] = "ERROR";	
				$response["message"] = "invalid api-key";
				$response["api_key"] = "kVBkcfp3ivy3GqzSn62Dn2sW27X408VN";	
			}
			else{
				$username = $app->request()->post()['username'];
				$password = $app->request()->post()['password'];
				
				$result = $db->login($username, $password);
				
				while ($strData = $result->fetch_assoc()) {
					$tmp["id"] = $strData["id"];
					$tmp["username"] = $username;
					$tmp["last_login"] = utf8_encode($strData["last_login"]);
					$tmp["access_token"] = utf8_encode($strData["access_token"]);
								
				}
				
				$response["status"] = "OK";
				
				if($tmp == null){
					$response["message"] = "login gagal";
				}
				else{
					$response["message"] = "login berhasil";
					$response["response"] = $tmp;
				}
			}
		}	
	}	
	echoRespnse(200, $response);
});
 

//REGISTER
$app->post('/register', function(){
	$app = new \Slim\Slim();
	$response = array();	
	$tmp = array();
	
	$api_key = $app->request->headers->get('API-KEY');
	$db = new DbHandler();
	
	if($api_key == null){
		$response["status"] = "ERROR";	
		$response["message"] = "tidak punya hak akses";
	}
	else{
		if(!array_key_exists('username', $app->request()->post()) | 
		!array_key_exists('first_name', $app->request()->post()) | 
		!array_key_exists('last_name', $app->request()->post()) |
		!array_key_exists('email', $app->request()->post()) |
		!array_key_exists('password', $app->request()->post())){
			$response["status"] = "ERROR";	
			$response["message"] = "isi field yang dibutuhkan";			
		}
		else{	
			if(!checkIfAPIKeyVerified($api_key)){
				$response["status"] = "error";	
				$response["message"] = "invalid api-key";
				$response["api_key"] = "kVBkcfp3ivy3GqzSn62Dn2sW27X408VN";	
			}
			else{
				$username = $app->request()->post()['username'];
				$first_name = $app->request()->post()['first_name'];
				$last_name = $app->request()->post()['last_name'];
				$email = $app->request()->post()['email'];
				$password = $app->request()->post()['password'];
				$tmp_name = $_FILES["img"]["tmp_name"];

		        $path="ProfileImages/$username.png";
		        move_uploaded_file($tmp_name, "$path");

				$result = $db->register($username, $first_name, $last_name, $email, $password, $path);
				if ($result==-99) {
					$response["status"] = "ERROR";
					$response["message"] = "username sudah digunakan";
					$response["response"] = $result;
				}else if($result==-999){
					$response["status"] = "ERROR";
					$response["message"] = "email sudah digunakan";
					$response["response"] = $result;
				}
				else{
					$response["status"] = "OK";
					$response["message"] = "register berhasil";
					$response["response"] = $result;
				}
			}
		}
	}
	echoRespnse(200, $response);
});

//GET PROFILE 
$app->get('/getProfile', function(){
	$app = new \Slim\Slim();
	$response = array();	

	$access_token = $app->request->headers->get('ACCESS-TOKEN');

	$db = new DbHandler();
	$res = $db->getProfile($access_token);

	if($access_token == null || !$res){
		$response["status"] = "ERROR";	
		$response["message"] = "tidak punya hak akses";
	}
	else{
		$response["status"] = "OK";	
		$response["message"] = "get profile success";
		
		$response["response"] = array();
			
		$tmp = array();
		while ($strData = $res->fetch_assoc()) {
			$tmp["username"] = utf8_encode($strData["username"]);
			$tmp["first_name"] = utf8_encode($strData["first_name"]);
			$tmp["last_name"] = utf8_encode($strData["last_name"]);
			$tmp["email"] = utf8_encode($strData["email"]);
			$tmp["password"] = utf8_encode($strData["password"]);
			$tmp["img"] = utf8_encode($strData["img"]);
			
			array_push($response["response"], $tmp);
		}
		$response["response"] = $tmp;	
	}
	echoRespnse(200, $response);
});

 
//GET TASKS 
$app->get('/getTask', function(){
	$app = new \Slim\Slim();
	$response = array();	

	$access_token = $app->request->headers->get('ACCESS-TOKEN');

	$db = new DbHandler();
	$res = $db->getTask($access_token);

	if($access_token == null || !$res){
		$response["status"] = "ERROR";	
		$response["message"] = "tidak punya hak akses";
	}
	else{
		$response["status"] = "OK";	
		$response["message"] = "get post success";
		
		$response["response"] = array();
			
		while ($strData = $res->fetch_assoc()) {
			$tmp = array();	

			$tmp["id"] = $strData["id"];
			$tmp["user_id"] = ($strData["user_id"]);
			$tmp["category_id"] = ($strData["category_id"]);
			$tmp["title"] = utf8_encode($strData["title"]);
			$tmp["description"] = utf8_encode($strData["description"]);
			$tmp["due_date"] = utf8_encode($strData["due_date"]);
			$tmp["created_time"] = utf8_encode($strData["created_time"]);
			$tmp["last_updated"] = utf8_encode($strData["last_updated"]);
			$tmp["task_status"] = utf8_encode($strData["task_status"]);

			
			array_push($response["response"], $tmp);
		}
	}
	echoRespnse(200, $response);
});

//POST TASKS BY ID
$app->post('/getTaskByID', function(){
	$app = new \Slim\Slim();
	$response = array();	

	$access_token = $app->request->headers->get('ACCESS-TOKEN');
	$id = $app->request()->post()['id'];
	$db = new DbHandler();
	$res = $db->getTaskByID($access_token, $id);

	if($access_token == null || !$res){
		$response["status"] = "ERROR";	
		$response["message"] = "tidak punya hak akses";
	}
	else{
		$response["status"] = "OK";	
		$response["message"] = "get post success";
		
		$response["response"] = array();
			
		while ($strData = $res->fetch_assoc()) {
			$tmp = array();	

			$tmp["id"] = $strData["id"];
			$tmp["user_id"] = ($strData["user_id"]);
			$tmp["category_id"] = ($strData["category_id"]);
			$tmp["title"] = utf8_encode($strData["title"]);
			$tmp["description"] = utf8_encode($strData["description"]);
			$tmp["due_date"] = utf8_encode($strData["due_date"]);
			$tmp["created_time"] = utf8_encode($strData["created_time"]);
			$tmp["last_updated"] = utf8_encode($strData["last_updated"]);
			$tmp["task_status"] = utf8_encode($strData["task_status"]);

			
			array_push($response["response"], $tmp);
		}
	}
	echoRespnse(200, $response);
});



//ADD TASK
$app->post('/addTask', function(){
	$app = new \Slim\Slim();
	$response = array();	
	$tmp = array();
	
	$access_token = $app->request->headers->get('ACCESS-TOKEN');
	$db = new DbHandler();
	
	if($access_token == null){
		$response["status"] = "ERROR";	
		$response["message"] = "tidak punya hak akses";
	}
	else{
		if(!array_key_exists('title', $app->request()->post())){
			$response["status"] = "ERROR";	
			$response["message"] = "Please field title";			
		}
		else{	
			$category_id = $app->request()->post()['category_id'];			
			$title = $app->request()->post()['title'];			
			$description = $app->request()->post()['description'];
			$due_date = $app->request()->post()['due_date'];
			$created_time = $app->request()->post()['created_time'];
			$last_updated = $app->request()->post()['last_updated'];
			$task_status = $app->request()->post()['task_status'];

			$result = $db->addTask($access_token, $category_id, $title, $description, $due_date, $created_time, $last_updated, $task_status);
			
			if($result){
				$response["status"] = "OK";
				$response["message"] = "add task success";
			}
			else{
				$response["status"] = "ERROR";	
				$response["message"] = "invalid access-token";	
			}
		}
	}
	echoRespnse(200, $response);
});


//UPDATE TASK
$app->post('/editTask', function(){
	$app = new \Slim\Slim();
	$response = array();	
	$tmp = array();
	
	$access_token = $app->request->headers->get('ACCESS-TOKEN');
	$db = new DbHandler();
	
	if($access_token == null){
		$response["status"] = "ERROR";	
		$response["message"] = "tidak punya hak akses";
	}
	else{
		if(!array_key_exists('title', $app->request()->post())){
			$response["status"] = "ERROR";	
			$response["message"] = "Please fill title";			
		}
		else{	
				date_default_timezone_set("Asia/Jakarta"); 
    			$last_updated = date('Y-m-d H:i:s');
				$id = $app->request()->post()['id'];
				$title = $app->request()->post()['title'];
				$description = $app->request()->post()['description'];
				$due_date = $app->request()->post()['due_date'];
				$category_id = $app->request()->post()['category_id'];
				$result = $db->editTask($access_token, $id, $title, $category_id, $description, $due_date, $last_updated);

				if($result){
					$response["status"] = "OK";
					$response["message"] = "edit task success";					
					$response["response"] = $result;
				}
				else{
					$response["status"] = "ERROR";
					$response["message"] = "edit task failed";													
				}
			}
	}
	echoRespnse(200, $response);
});

//CHANGE STATUS
$app->post('/changeStatus', function(){
	$app = new \Slim\Slim();
	$response = array();	
	$tmp = array();
	
	$access_token = $app->request->headers->get('ACCESS-TOKEN');
	$db = new DbHandler();
	
	if($access_token == null){
		$response["status"] = "ERROR";	
		$response["message"] = "tidak punya hak akses";
	}
	else{
		if(!array_key_exists('id', $app->request()->post())){
			$response["status"] = "ERROR";	
			$response["message"] = "Please fill id";			
		}
		else{	
				date_default_timezone_set("Asia/Jakarta"); 
    			$last_updated = date('Y-m-d H:i:s');
				$id = $app->request()->post()['id'];
				$task_status = $app->request()->post()['task_status'];
				$result = $db->changeStatus($access_token, $id, $task_status, $last_updated);

				if($result){
					$response["status"] = "OK";
					$response["message"] = "edit status task success";					
					$response["response"] = $result;
				}
				else{
					$response["status"] = "ERROR";
					$response["message"] = "edit status task failed";													
				}
			}
	}
	echoRespnse(200, $response);
});


//DELETE TASK
$app->post('/deleteTask', function(){
	$app = new \Slim\Slim();
	$response = array();	
	$tmp = array();
	
	$access_token = $app->request->headers->get('ACCESS-TOKEN');
	$db = new DbHandler();
	
	if($access_token == null){
		$response["status"] = "ERROR";	
		$response["message"] = "tidak punya hak akses";
	}
	else{
		if(!array_key_exists('id', $app->request()->post())){
			$response["status"] = "ERROR";	
			$response["message"] = "Please choose task";			
		}
		else{	
				$id = $app->request()->post()['id'];
				$result = $db->deleteTask($access_token, $id);

				if($result){
					$response["status"] = "OK";
					$response["message"] = "delete task success";					
					$response["response"] = $result;
				}
				else{
					$response["status"] = "ERROR";
					$response["message"] = "delete task failed";													
				}
			}
	}
	echoRespnse(200, $response);
});


/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 * Daftar response
 * 200	OK
 * 201	Created
 * 304	Not Modified
 * 400	Bad Request
 * 401	Unauthorized
 * 403	Forbidden
 * 404	Not Found
 * 422	Unprocessable Entity
 * 500	Internal Server Error
 */
function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

	//print_r($response);
    echo json_encode($response);
}

function checkIfAPIKeyVerified($api_key){
	$myAPIKey = 'kVBkcfp3ivy3GqzSn62Dn2sW27X408VN';
	if($api_key == $myAPIKey){
		return true;
	}
	else{
		return false;
	}
}


$app->run();
?>