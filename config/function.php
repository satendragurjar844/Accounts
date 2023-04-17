<?php 

ob_start();
ini_set('post_max_size', '10M');
ini_set('upload_max_filesize', '10M');
ini_set("display_errors",0);
ini_set("log_errors",0);

function MISuploadlogger($errorlog)
{
	$newfile = 	'errorlog/Debuglog_'.date('dmy').'.txt';

	//rename('errorlog/miserrorlog.txt',$newfile);
   
	if(!file_exists($newfile))
	{
	  file_put_contents($newfile,'');
	}
	$logfile=fopen($newfile,'a');

	
	$ip = $_SERVER['REMOTE_ADDR'];
	date_default_timezone_set('Asia/Kolkata');
	$time = date('d-m-Y h:i:s A',time());
	//$contents = file_get_contents('errorlog/errorlog.txt');
	$contents = "$ip\t$time\t$errorlog\r";
	fwrite($logfile,$contents);
	//file_put_contents('errorlog/errorlog.txt',$contents);
}

function GetSequeceNoOftheDay()
{
	$sequenceNo=1;
	$returnval=0;
	MISuploadlogger("Inside Function...");
	if(file_exists('SequenceNo.txt'))
	{
		$contents = file_get_contents('SequenceNo.txt');
		MISuploadlogger("Content of function at opening".$contents);
		$libData = explode("^",$contents);
		
		$sequenceNo=intval($libData[1]);
		
			$sequenceNo++;

			$returnval=$sequenceNo;
	
	}
	$contents=date("dmY")."^".strval($sequenceNo);
	MISuploadlogger("Content of function".$contents);
	file_put_contents ('SequenceNo.txt',$contents);
	
	return $returnval;

}

?>