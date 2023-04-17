<?php 
error_reporting(E_ALL & ~E_NOTICE);
include 'config/conn.php';
include 'config/function.php';

if($_SERVER['HTTP_HOST']=='localhost' || $_SERVER['HTTP_HOST']=='127.0.0.1'){
  $serverurl = "http://";
  $serverurl .= $_SERVER['HTTP_HOST'];
  $serverurl .= "/Accounts/"; 
}

else{
  if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
    $serverurl = "https://";
  }else{
    $serverurl = "http://";
  }

  $serverurl .= $_SERVER['HTTP_HOST'];
  // $serverurl .= "/UAT/";
}
?>