<?php 
function OpenCon()
{
 $dbhost = "103.1.114.30";
 $port = 5432;
 $db = "apparele_accounts";
 $dbuser = "apparele_global_account";
 $dbpass = "accounts@123*#";
 $constring="host=103.1.114.30 port=5432 dbname=apparele_accounts user=apparele_global_account  password=accounts@123*#";
 $conn = pg_connect($constring);
 return $conn;
 
}

// echo "Inside Connection File"
OpenCon();
?>
