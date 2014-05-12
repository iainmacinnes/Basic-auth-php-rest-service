<?php
class crmWebService {
		
	var $contact = array();
	var $connection;
	var $error = array();
	
	function __construct(){
		
		$this->connection = new mysql_connection;
		$this->headers = apache_request_headers();
		
	}
	
	function authenticate($headers){
	
		#$_SERVER['X-Forwarded-For']
		if(! isset($_SERVER['HTTP_FRONT_END_HTTPS'])){
			header('HTTP/1.1 401 Unauthorized');
			return false;
		}
		
		if($_SERVER['HTTP_FRONT_END_HTTPS'] == "On"){
			$userpwd = $headers['user-id'].":".$headers['password'];
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $path.'login/');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, $userpwd);
			$login = curl_exec($ch) or die(curl_error($ch));
			curl_close($ch); 		
			if(trim($login) == 'SUCCESS'){
				header('HTTP/1.1 200 OK');
				return true;
			}else{
				header('HTTP/1.1 401 Unauthorized');
				return false;
			}

		}else{
			header('HTTP/1.1 401 Unauthorized');
			return false;
		}
		
	}

	function contest($attributes){
	
		$contact['format'] = (isset($attributes["format"])?$attributes["format"]:'xml');	
		$contact['firstname'] = (isset($attributes["firstname"])?$attributes["firstname"]:'');
		$contact['lastname'] = (isset($attributes["lastname"])?$attributes["lastname"]:'');
		$contact['Address']['PostalZip'] = (isset($attributes["postalzip"])?$attributes["postalzip"]:'');
		$contact['Email'] = (isset($attributes["email"])?$attributes["email"]:'');
		$contact['Phone'] = (isset($attributes["phone"])?$attributes["phone"]:'');
		$contact['Language'] = (isset($attributes["Language"])?$attributes["language"]:'English');
		$contact['mailinglist'] = (isset($attributes["subscribe"])?$attributes["subscribe"]:'Y');
		$contact['ErrorCheck']=True;
		$contact['contest'] = (isset($attributes["contest"])?$attributes["contest"]:'');
		$contact['LEAD_SOURCE'] = 'Promotions/Contest';
	
		if(empty($contact['firstname']) || empty($contact['lastname'])){
			$error['lastname'] = "First name and last name are required.";
		}
		
		if(empty($contact['Email'])){
			$error['Email'] = "Please include an email.";
		}
		if(empty($contact['contest'])){
			$error['contest'] = "Please include a contest name.";
		}

		if(isset($error)){
			$this->error = $error;
		}
		
		$this->contact = $contact;
	
	}

	
	function leadgen($attributes){
	
		$contact['format'] = (isset($attributes["format"])?$attributes["format"]:'xml');	
		$contact['firstname'] = (isset($attributes["firstname"])?$attributes["firstname"]:'');	
		$contact['lastname'] = (isset($attributes["lastname"])?$attributes["lastname"]:'');
		$contact['Address']['ProvinceState'] = (isset($attributes["provincestate"])?$attributes["provincestate"]:'');
		$contact['Address']['City'] = (isset($attributes["city"])?$attributes["city"]:'');
		$contact['Email'] = (isset($attributes["email"])?$attributes["email"]:'');
		$contact['Language'] = (isset($attributes["Language"])?$attributes["Language"]:'English');
		$contact['mailinglist'] = (isset($attributes["subscribe"])?$attributes["subscribe"]:'Y');
		$contact['ErrorCheck']=True;
		$contact['LEAD_SOURCE'] = 'Lead Generation';
		$contact['SOURCE_CODE'] = (isset($attributes["source_code"])?$attributes["source_code"]:'');
		
		if(isset($attributes["date"])){
			$datum = strtotime($attributes['date']);
			$date = date("m/d/Y", $datum);
			$contact['DATE'] = $date;
		}else{
			$contact['DATE'] = '';
		}
	
		if(empty($contact['firstname']) || empty($contact['lastname'])){
			$error['lastname'] = "First name and last name are required.";
		}

		if(empty($contact['Email'])){
			$error['Email'] = "Please include an email.";
		}
		
		if($contact['mailinglist'] == 'Y' && empty($contact['Email'])){
			$error['mailinglist'] = "An email is required when signing up for the newsletter.";
		}

		if(isset($error)){
			$this->error = $error;
		}
		
		$this->contact = $contact;
	
	}
	
	function test($attributes){
		$authenticity = $this->authenticate($this->headers);
		if($authenticity){
			echo 'Test Complete';
		}
	}
	
}

