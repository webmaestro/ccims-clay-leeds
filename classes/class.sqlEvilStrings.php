<?php 
require_once('class.serverVarsUtility.php');
/******************************************************************************
Just a list of sql commands or words typically used in SQL injsection attacks.
Usually, these would not be in any of our query parameters.  So we can check to
see if they exist in a submitted query string and bail out if one of them does.
*******************************************************************************/
class sqlEvilStrings{
	protected $evilArray;//array of words used in sqlInjection attacks
	protected $evilUserAgentArray;//user agent strings in common scanning apps
	protected $allowedIpArray;//
	protected $uri;//request string
	protected $queryString;
	protected $userAgent;
	protected $ipAddress;//sent by requestor
	protected $referrer;
	protected $timestamp;
	protected $lastEvilQuery;//last string that was found to contain evil str 
	protected $lastEvilString;//the evil string in the query
	protected $foundEvilAgent;//the evil user agent making the request
	protected $maxStrLength;//the maximum length allowed for parameters
	//used when calling logHascker
	protected $fileEffected;
	protected $lineNumberEffected;
	//for sending email notificatiuons
	protected $mailReplyToAddress;
	protected $mailToAddresses;
	protected $mailFromAddress;
	protected $mailSubject;
	protected $mailBody;
	protected $msg;//success or failure msg on email send
	protected $sessionCount;
	protected $currentSessionId;
	
	
	
	
	function __construct(){
		$this->init();
	}

	protected function init(){
		$myServerVarsUtility = new serverVarsUtility();
		$visitorIp = $myServerVarsUtility->getIpAddress();
		$this->maxStrLength = 100;
		//email settings, will be used if loghacker is called with sendEmail param
		$this->setEmailAddresses("whoever@whatever.com");
		$this->mailReplyToAddress = "whoever@whatever.com";
		$this->mailFromAddress = "whoever@whatever.com";
		$this->mailSubject = "possible hack attempt";
		//info about request
		$this->uri = isset($_SERVER["REQUEST_URI"])?$_SERVER["REQUEST_URI"]:"NO_URI";
		$this->queryString = isset($_SERVER['QUERY_STRING'])?$_SERVER['QUERY_STRING']:"NO_QS";
		$this->userAgent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:"NO_UA";
		$this->ipAddress = $visitorIp?$visitorIp:"NO_RA";
		$this->referrer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:"NO_REF";
		$this->timestamp = date ('Y-m-d H:i:s');
		$this->allowedUserIpArray = array(
		"127.0.0.1",//comma delim string of ip addresses allowed
		);
		//commonly used in sql injection attacks
		$this->evilArray = array(
		"declare",
		"char(",
		"char%28",
		"cast",
		"char(4000);set",
		"char%28%E4%80%80%29%3Bset",
		"char(4000));exec(@S);",
		"char%28%E4%80%80%29%29%3Bexec%28%40S%29%3B",
		"character_sets",
		"CHR%28",
		"CHR(",
		"exec(",
		"%22exec%28%22",
		"UNION SELECT",
		"UNION ALL SELECT",
		"INTO OUTFILE",
		"SHELL_COMMAND",
		"load_file",
		"SHOW TABLES",
		"SELECT VERSION",
		"SELECT+VERSION",
		"BENCHMARK",
		"CONCAT",
		"SUBSTRING",
		"load data infile",
		"outfile",
		"1=1",
		"1%3D1%",
		"+AND+",
		"%01%3D%01",
		"0=0",
		"%00%3D%00",
		"'x'='x",
		"%27x%27%3D%27x",
		"MD5(",
		"CMD5%28",
		"%2F%2A",
		"1/0",
		"%01%2F%00",
		"||",
		"%7C%7C",
		"ASCII(",
		"ASCII%28",
		"CAST(",
		"CAST%28",
		"0x31303235343830303536",
		"CONVERT(",
		"CONVERT%28",
		"mysql",
		"SHA1(",
		"SHA1%28",
		"PASSWORD(",
		"PASSWORD%28",
		"ENCODE(",
		"ENCODE%28",
		"COMPRESS(",
		"COMPRESS%28",
		"ROW_COUNT(",
		"ROW_COUNT%28",
		"SCHEMA(",
		"SCHEMA%28",
		"information_schema",
		"VERSION(",
		"VERSION%28",
		"@@version",
		"%40%40version",
		"WAITFOR",
		"ORDER+BY",
		"<",
		">",
		"%3E",
		"%3C",
		":expr",
		"%3aexpr",
		"0x3c",
		"<script>",
		"%3Cscript%3",
		"&echo",
		"%26echo",
		"|echo",
		"%7Cecho",
		"&ping",
		"%26ping",
		"|ping",
		"%7Cping",
		"casper",
		"/*",
		"*/",
		"--"
		);
		//these are just common vulnerability scanners.  There may be times when we want to do our own scans
		//and hackers can alter the user agent, so banning these will just keep out the amatures
		//we should be able to allow scans from certain IP addresses
		$this->evilUserAgentArray = array(
		"Havij",
		"netsparker",
		"acunetix",
		"libwww-perl",
		"SiteBot",
		"Python-urllib",
		"pangolin",
		"Indy Library",
		"Zeus",
		"casper"
		);
	}
	//this just tells us if one of the "evil" words is in the string
	//Omniture uses "||" to retreive image beacons, with a param s_kwcid
	//so we have to make an exception for that
	//gclid is a Google AdWords param that uses a double-dash
	function isEvilString($str){
	$str = strtolower($str);
	//set common space delineaters to a space
	$lstr = str_replace("%20"," ",$str);
	$lstr = str_ireplace("+AND+","*AND*",$lstr);//we're going to want to save this one
	$lstr = str_replace("+"," ",$lstr);
	$lstr = str_ireplace("*AND*","+AND+",$lstr);//get it back
	//$strArray = split(" ",$lstr);
		foreach($this->evilArray as $evilStr){
			if(stristr($lstr,$evilStr)){
				if(//exceptions
				!(stristr($lstr,"s_kwcid")&& ($evilStr == "||" || $evilStr == "%7C%7C"))&&
				!(stristr($lstr,"gclid") && ($evilStr == "--")) //&&
				//!(stristr($lstr,"eid") && ($evilStr == ">" || $evilStr == "%3C" || $evilStr == "%3E" || $evilStr == "<"))//worldwide_events/eventInfo.php
				){//do this if not exception
					$badguy = substr($lstr,stripos($lstr,$evilStr),strlen($evilStr));
					$this->lastEvilQuery =  $str;
					$this->lastEvilString = $badguy;
					return(true);
				}
			}
			if(stristr($lstr,"SELECT")&& stristr($lstr,"FROM")){
				$this->lastEvilQuery =  $str;
				$this->lastEvilString = "'SELECT' and 'FROM' both in querystring";
				return (true);
			}
			if(stristr($lstr,"SELECT")&& stristr($lstr,"UNION")){
				$this->lastEvilQuery =  $str;
				$this->lastEvilString = "'SELECT' and 'UNION' both in querystring";
				return (true);
			}
			if(stristr($lstr,"INTO")&& stristr($lstr,"OUTFILE")){
				$this->lastEvilQuery =  $str;
				$this->lastEvilString = "'INTO' and 'OUTFILE' both in querystring";
				return (true);
			}
			if(stristr($lstr,"SHOW")&& stristr($lstr,"TABLES")){
				$this->lastEvilQuery =  $str;
				$this->lastEvilString = "'SHOW' and 'TABLES' both in querystring";
				return (true);
			}
			//product selector tool uses view=download in get and also "data"
			if(stristr($lstr,"LOAD")&& stristr($lstr,"DATA")&& !stristr($lstr,"view=download")){
				$this->lastEvilQuery =  $str;
				$this->lastEvilString = "'LOAD' and 'DATA' both in querystring";
				return (true);
			}
			//can't do this due to Google strings etc
			/*if(strlen($str)> $this->maxStrLength){
				echo "too long, baby";
				$this->lastEvilQuery =  $str;
				$this->lastEvilString = "Querystring over ".$this->maxStrLength." characters";
				return true;
			}*/
		}
		return false;
	}
	
	public function isEvilUserAgent(){
		if(!in_array($this->ipAddress,$this->allowedUserIpArray)){
			foreach($this->evilUserAgentArray as $evilUa){
				if(stristr($this->userAgent,$evilUa)){
					$this->foundEvilAgent = $evilUa;
					return true;
				}
			}
		}
		return false;
	}
	
	/*************************************************************************************
	Call this at the top of any page and if there are evil strings, the page will exit;
	BUT DON'T BE COMPLACENT!  THERE ARE THINGS THAT EVILSTRINGS MIGHT NOT CATCH, SO CHECK
	ALL EXPECTED PARAMETERS ANYWAY !!!!!!!!!!!!!!!
	*************************************************************************************/
	function bailOnEvilQuerystring($mailit=false){
		if($this->isEvilUserAgent()){
			$note = "Request from evil user agent: ".$this->foundEvilAgent;
			//Sitebot and other robots go through every url, so too much email and fills up logs
			//$logAttempt = $this->logHacker(__FILE__,__LINE__,$note,$mailit);
			exit;
		}
		if($this->isEvilString($this->queryString)){
			//if you have a test server, or some other environment from shich you do not want to send mail, 
			//set $mailit = false here
			if($_SERVER['<whatever server var you want to test'] == "<whatever value>"){
				$mailit = false;
			}
			$logAttempt = $this->logHacker(__FILE__,__LINE__,$this->queryString,$mailit);
			//it's bad, so exit
			exit;
		}
	}

	
	
	function setMaxStrLength($len){
		$this->maxStrLength = is_numeric($len)?$len:100;
	}
	//this replaces any "evil" word in the string with "x.evilWord.x"
	function neutralizeEvilStr($str){
	$lstr = str_replace("%20"," ",$str);
	$strArray = split(" ",$lstr);
	for($i=0;$i< count($strArray);$i++){
			$tempStr = strtolower($strArray[$i]);
			if(in_array($tempStr,$this->evilArray)){
				$strArray[$i] = "x".$strArray[$i]."x";
			}
		}
		$newListStr = join(" ",$strArray);
		return $newListStr;
	}
	
	/*
	Salesforce campaign IDs have 11 integers followed by a letter, followed by 3 alpha-numeric characters,
	so we can check for them.  A typical Salesforce  campign_id is 70180000000fcBw or 70180000000fc8Y
	*/
	function isSalesforceCmpid($cmpid){
		if(ereg('^[0-9]{11}[A-Za-z]{1}[0-9A-Za-z]{3}$',$cmpid)){
			return true;
		}
		return false;
	}
	
	function getAttackInfo(){
		$attackInfoStr = "
		<strong>URI :</strong> $this->uri <br />
		<strong>Query String : </strong> $this->queryString <br />
		<strong>User Agent :</strong> $this->userAgent <br />
		<strong>Referrer : </strong> $this->referrer <br />
		<strong>IP Address :</strong> $this->ipAddress <br />
		<strong>Request String :</strong> $this->lastEvilQuery;<br />
		<strong>Suspect String :</strong> $this->lastEvilString
		";
		return $attackInfoStr;
	}
	
	//function to log hacking attempts, when a param does not fit expected format
	//call this function like so:
	//logHacker(__FILE__, __LINE__,"put query here");
	function logHacker($fileName="",$lineNum="",$query="",$sendEmail=false){
		ini_set('log_errors',1);
		if(!$fileName){
			$fileName = __FILE__;
		}
		if(!$lineNum){
			$lineNum = __LINE__;
		}
		//This assumes a log directory 1 level above doc root
		//you may have to enter your own path here if log dir is somewhere else
		$customErrorDir = $this->getOutsideDir("logs");
$visitorInfo = "URI: $this->uri ,
QUERY STRING: $this->queryString ,
USER AGENT: $this->userAgent ,
IP ADDRESS: $this->ipAddress ,
REFERER: $this->referrer ";
		$time = date ('Y-m-d H:i:s');
	
		$errorMessage = "\n POSSIBLE HACKER $time -- \n in File: $fileName on Line: $lineNum --  \n Query: '$query' -- \n $visitorInfo\n";
		error_log($errorMessage,3,"$customErrorDir/customErrorLog.log");
		if($sendEmail){
			$this->mailBody = "POSSIBLE HACK ATTEMPT $time -- in File: $fileName on Line: $lineNum -- <br />Query: '$query'<br /><br />".$this->getAttackInfo();
			$this->sendEmail();
		}
	}
	
	public function setEmailAddresses($addressStr){
		$mailToAddresses = explode(",",$addressStr);
		$checkedEmailAddressArray = "";
		if(is_array($mailToAddresses)){
			foreach($mailToAddresses as $address){
				if($this->isEmail($address)){
					$checkedEmailAddressArray[] = $address;
				}else{
				}
			}
		}
		if(is_array($checkedEmailAddressArray)){
			$this->mailToAddresses = $checkedEmailAddressArray;
		}
	}
	
	protected function isEmail($email) {
		return preg_match('|^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]{2,})+$|i', $email);
	}
	
	protected function getOutsideDir($destDir){
		$docRoot = $_SERVER['DOCUMENT_ROOT'];
		if(substr($docRoot,-1,1)=="/"){
			$docRoot = substr($docRoot,0,-1);
		}
		$endPos = strrpos($docRoot,"/");
		$levelAbove = substr($docRoot,0,$endPos+1);
		$destinationDir = $levelAbove.$destDir;
		return $destinationDir;
	}
	
	
	
	protected function sendEmail(){
		require_once($_SERVER['DOCUMENT_ROOT'].'/<path to directory>/class.phpmailer.php');
		$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch
		$mail->CharSet="utf-8";
		$mail->IsSendmail(); // telling the class to use SendMail transport
		try {
		  $mail->AddReplyTo($this->mailReplyToAddress);
		  if(is_array($this->mailToAddresses)){
			  foreach($this->mailToAddresses as $toAddress){
				$mail->AddAddress($toAddress);
			  }
			  $mail->SetFrom($this->mailFromAddress);
			  $mail->Subject = $this->mailSubject;
			  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; 
			  //mailBody gets set in function logHacker;
			  $mail->MsgHTML($this->mailBody);
			  
			  $mail->Send();
			  $this->msg .= "Message Sent OK</p>\n";
		  }
		} catch (phpmailerException $e) {
		  $this->errorMsg .= $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
		  $this->errorMsg .=  $e->getMessage(); //Boring error messages from anything else!
		}
	}
}
?>