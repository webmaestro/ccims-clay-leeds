<?php 
/*
 . . . $__SERVER['REMOTE_ADDR'] has been returning a proxy address
instead of the actual remote address.  This class attempts to get the actual remote address
and would be a place where we can add other $_SERVER vars type functions if needed. 

Usage:
Instead of $visitorIp = $__SERVER['REMOTE_ADDR'] to get the visitor's ip address, do this

require_once($_SERVER['DOCUMENT_ROOT'].'/<path to this file>/class.serverVarsUtility.php');
$myServerVarsUtility = new serverVarsUtility();
$visitorIp = $myServerVarsUtility->getIpAddress();

*/

class serverVarsUtility{
	
	function __construct(){
		
	}
	public function getIpAddress() {	
		$ip = "";	
		if($_SERVER) {
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			}elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			}else{
				$ip = $_SERVER['REMOTE_ADDR'];
			}
		} else {
			if(getenv('HTTP_X_FORWARDED_FOR')){
				$ip = getenv('HTTP_X_FORWARDED_FOR');
			}elseif(getenv('HTTP_CLIENT_IP')){
				$ip = getenv('HTTP_CLIENT_IP');
			}else{
				$ip = getenv('REMOTE_ADDR');
			}
		}
		//With some servers, we're getting ip twice with comma in between.  wtf?
		$ipArray = explode(",",$ip);
		$finalIp = trim($ipArray[0]);
		return $finalIp;
	}
}
?>