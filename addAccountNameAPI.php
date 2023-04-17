<?php
include "inc.php";

header("Content-Type: application/json");
$parameterdata = file_get_contents('php://input');
$parameterdata = str_replace("null","\"\"",$parameterdata);
MISuploadlogger($parameterdata);

$dataToShare = json_decode($parameterdata);

$Id=trim($dataToShare->Id);
$AccountName=trim($dataToShare->AccountName);
$GroupId=trim($dataToShare->GroupId);
$OpeningBal=trim($dataToShare->OpeningBalance);
$AddedBy=trim($dataToShare->AddedBy);
$Status=trim($dataToShare->Status);

class clsListData
{
  public $Status;
	public $Message;
	
}

$listArray=array();
$Message = "";
$Status = 0;
try
{

  if($Id!=''){
    $listSql = "SELECT \"ACCOUNT_ID\" FROM accounts.\"accountMaster\" where \"ACCOUNT_ID\"='_aid'";
    $listSql = str_replace("_aid",$Id,$listSql );

    //$listSql = str_replace("_gname",$GroupName!=''?" and UPPER(\"GROUP_NAME\") LIKE '%".$GroupName."%' ":"",$listSql );

    MISuploadlogger($listSql);	
    $DataQuery = pg_query(OpenCon(), $listSql);
    
    $Count = pg_num_rows($DataQuery);
    
    if($Count > 0){

      $updateDetailQuery =" Update accounts.\"accountMaster\" set \"GROUP_ID\" = '_gid',\"ACCOUNT_NAME\"='_aname',\"STATUS\"='_status',\"OPENING_BALANCE\"='_obal' where true _aid;";

      $updateDetailQuery = str_replace("_aid"," and \"ACCOUNT_ID\" = '".$Id."' ",$updateDetailQuery);
      $updateDetailQuery = str_replace("_obal",$OpeningBal,$updateDetailQuery);
      $updateDetailQuery = str_replace("_aname",$AccountName,$updateDetailQuery);
      $updateDetailQuery = str_replace("_gid",$GroupId,$updateDetailQuery);
      $updateDetailQuery = str_replace("_status",$Status,$updateDetailQuery);

      $result = pg_query(OpenCon(),$updateDetailQuery);
      MISuploadlogger($updateDetailQuery);
      $effectiveRow = pg_affected_rows($result);

      if($effectiveRow > 0){
        $Message = "Updated Successfully";
      }else{
        $Message = "Not Updated";
      }

    }
  }else{

    $lastIdQuery = "SELECT \"ACCOUNT_ID\" FROM accounts.\"accountMaster\" ORDER BY \"ACCOUNT_ID\" DESC";
  	MISuploadlogger($lastIdQuery);
    $DataQuery = pg_query(OpenCon(), $lastIdQuery);
	
    $LastId = pg_fetch_assoc($DataQuery);
    $TablelastId = $LastId['ACCOUNT_ID']+1;

    $sql_name = '"ACCOUNT_NAME","DATE_ADDED","GROUP_ID","ACCOUNT_ID","ADDED_BY","OPENING_BALANCE"';

    $sql_val = "'".$AccountName."','".date("Y-m-d h:i:s",time())."','".$GroupId."','".$TablelastId."','".$AddedBy."','".$OpeningBal."'";

    $InsertQuery ='Insert into accounts."accountMaster" ('.$sql_name.') Values ('.$sql_val.');';

    $result = pg_query(OpenCon(),$InsertQuery);
    MISuploadlogger($InsertQuery);
    $effectiveRow = pg_affected_rows($result);

    if($effectiveRow > 0){
    	$Message = "Added Successfully";
    }else{
    	$Message = "Not Added";
    }
  }
 

  $objListData = new clsListData();
	$objListData->Status = "0";
	$objListData->Message= $Message;


	echo json_encode($objListData,JSON_PRETTY_PRINT);

}

catch(Exception $e)
{
	echo json_encode(['Status'=>'-1','Message'=>'Failed'],JSON_PRETTY_PRINT);
}

?>