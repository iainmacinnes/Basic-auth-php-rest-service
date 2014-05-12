<?php
#ini_set('display_errors', 1);
#error_reporting(E_ALL);
#make your own error checking here.

if(! array_key_exists('method', $_REQUEST)){		
	header('HTTP/1.1 401 Unauthorized');
	exit;
}

include('class.crm_vX.php');	

include('class.ws_crm.php');	

$wsCRM = new crmWebService;

# request method comes from htaccess rewrite
$wsCRM->$_REQUEST['method']($_REQUEST);

$check = $wsCRM->error;
$contact = $wsCRM->contact;

if(empty($check)){		
	$crm = new crmContactTrip;
	$crm->contact = array_merge($crm->contact,$contact);
	$return = $crm->ContactBus($crm->contact);
}else{
	$return=$check;
}

$_REQUEST['format'] = (isset($_REQUEST['format'])?$_REQUEST['format']:'xml');

if(! empty($return)){
	if($_REQUEST['format'] == 'json') {
		returnJSON($return,$contact);
	}else{
		returnXML($return,$contact);
	}
	exit();
}else{
	if($_REQUEST['format'] == 'json') {
		returnJSON($return,$contact);
	}else{
		returnXML($return,$contact);
	}
	$crm->TripBus($crm->contact);
}

function returnXML($return,$contact){
	if(! isset($return)){
		$return = array();
	}
	header('Content-type: text/xml');
	echo '<RESPONSE>';
		echo '<ERROR>';
		foreach($return as $key => $value) {
			if(is_array($value)){
				echo '<'.strtolower($key).'>';
				foreach($value as $tag => $val) {
					echo '<',$tag,'>',htmlentities($val),'</',$tag,'>';
				}
				echo '</'.strtolower($key).'>';
			}else{
				echo '<'.strtolower($key).'>';
				echo htmlentities($value);
				echo '</'.strtolower($key).'>';
			}
		}
		echo '</ERROR>';
		echo '<VALUES>';
		foreach($contact as $key => $value) {
			if(is_array($value)){
				echo '<'.strtolower($key).'>';
				foreach($value as $tag => $val) {
					echo '<',$tag,'>',htmlentities($val),'</',$tag,'>';
				}
				echo '</'.strtolower($key).'>';
			}else{
				echo '<'.strtolower($key).'>';
				echo htmlentities($value);
				echo '</'.strtolower($key).'>';
			}
		}
		echo '</VALUES>';
	echo '</RESPONSE>';
}

function returnJSON($return,$contact){
	header('Content-type: application/json');
	$error = array_change_key_case($return);
	$values = array_change_key_case($contact);
	$response = array('ERROR'=>$error,'VALUES'=>$values);
	echo json_encode($response);
}

