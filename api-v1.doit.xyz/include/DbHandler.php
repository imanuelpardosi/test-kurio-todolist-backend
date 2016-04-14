<?php

class DbHandler {
 
    private $conn;
 
    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
 
 	//LOGIN
	public function login($username_or_email, $password){
		$passHash = $this->passHash($password);

		//get the pwd from db
		$query = "SELECT password FROM mst_users WHERE (username = '". $username_or_email
		."' OR email = '". $username_or_email ."') LIMIT 1";
				
		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		
		$tasks = $stmt->get_result();
		
		$strData = $tasks->fetch_assoc();

		//verify the password
		if(password_verify($password, $strData['password'])){
			//if verified pass access_token

			date_default_timezone_set("Asia/Jakarta"); 
			$last_login = date('Y-m-d H:i:s');
			$query2 = "UPDATE mst_users SET last_login='".$last_login."' WHERE (username = '". $username_or_email
			."' OR email = '". $username_or_email ."') limit 1";
			$stmt2 = $this->conn->prepare($query2);
			$stmt2->execute();

			$query = "SELECT id, last_login, access_token FROM mst_users WHERE (username = '". $username_or_email
			."' OR email = '". $username_or_email ."') LIMIT 1";
			
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			
			$tasks = $stmt->get_result();
			$stmt->close();
			
			return $tasks;		
		}
		else{
			//if not verified send trash
			$query = "SELECT access_token FROM mst_users WHERE (username = '". $username_or_email
			."' OR email = '". $username_or_email ."') AND password = '".$passHash."' LIMIT 1";
			
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			
			$tasks = $stmt->get_result();
			$stmt->close();

			return $tasks;
		}
	}

	//ADD TASK
	public function addTask($access_token, $category_id, $title, $description, $due_date, $created_time, $last_updated, $task_status){
		
		$user_id = $this->matchAccessToken($access_token);

		if($user_id!=-99){
			$query = "INSERT INTO trx_tasks (user_id, category_id, title, description, due_date, created_time, last_updated, task_status)".
					"VALUES('".$user_id."', '".$category_id."', '".$title."', '".$description."', '".$due_date."', '".$created_time."', 
					'".$last_updated."', '".$task_status."')";
					
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			$stmt->close();
		
			return true;					
		}
		else{
			return false;
		}
	}

	//GET TASKS
 	public function getTask($access_token){
		$user_id = $this->matchAccessToken($access_token);

		//echo ("user id : ".$user_id);

		if($user_id!=-99){
			//WHERE created_at >= (SELECT last_login FROM mst_users WHERE id=$user_id) 
			$query= "SELECT * FROM trx_tasks WHERE user_id=".$user_id." ORDER BY due_date ASC "; 
			$stmt = $this->conn->prepare($query);
			$stmt->execute();

			$tasks = $stmt->get_result();
			$stmt->close();
		
			return $tasks;
		}
		else{
			return false;
		}
	}


	//GET TASKS
 	public function getTaskByID($access_token, $id){
		$user_id = $this->matchAccessToken($access_token);

		//echo ("user id : ".$user_id);

		if($user_id!=-99){
			//WHERE created_at >= (SELECT last_login FROM mst_users WHERE id=$user_id) 
			$query= "SELECT * FROM trx_tasks WHERE id=".$id."  ORDER BY due_date ASC "; 
			$stmt = $this->conn->prepare($query);
			$stmt->execute();

			$tasks = $stmt->get_result();
			$stmt->close();
		
			return $tasks;
		}
		else{
			return false;
		}
	}
	//UPDATE TASK
	public function editTask($access_token, $id, $title, $category_id, $description, $due_date, $last_updated){
		$user_id = $this->matchAccessToken($access_token);
		//echo $id;

		if($user_id!=-99){
			$query = "UPDATE trx_tasks SET title='".$title."', category_id='".$category_id."', description='".$description."', 
			due_date='".$due_date."', last_updated='".$last_updated."' WHERE id=".$id;
			//echo $query;
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			$stmt->close();		
				
			return true;
		}
		else{
			return false;
		}
	}


	//CHANGE connection_status(oid)
	public function changeStatus($access_token, $id, $task_status, $last_updated){
		$user_id = $this->matchAccessToken($access_token);
		//echo $id;

		if($user_id!=-99){
			$query = "UPDATE trx_tasks SET task_status='".$task_status."', last_updated='".$last_updated."' WHERE id=".$id;
			//echo $query;
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			$stmt->close();		
				
			return true;
		}
		else{
			return false;
		}
	}


	//DELETE TASK
	public function deleteTask($access_token, $id){
		$user_id = $this->matchAccessToken($access_token);
		
		if($user_id!=-99){
			$query = "DELETE FROM trx_tasks WHERE id='".$id."' AND user_id='".$user_id."'";
			
			$stmt = $this->conn->prepare($query);
			$stmt->execute();
			$stmt->close();		
				
			return true;
		}
		else{
			return false;
		}
	}

	private function createAccessToken($username, $password){
		return $this->passHash($username.$password."maHCUSt0m54LT1");
	}
	
	//this function to has the password
	//round = 13
	private static function passHash($string_password){
		$options = array('cost' => 13);
		return password_hash($string_password, PASSWORD_BCRYPT, $options);
	}

	//function to match the access token of users
	private function matchAccessToken($access_token){
		$query = "SELECT id FROM mst_users WHERE access_token = '".$access_token."' LIMIT 1";

		$stmt = $this->conn->prepare($query);
		$stmt->execute();
		
		$tasks = $stmt->get_result()->fetch_assoc();
		$stmt->close();
		
		if($tasks['id']!=null){
			return $tasks['id'];
		}
		else{
			return -99;
		}
	}
}
 
?>