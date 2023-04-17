<?php
include "inc.php";

header("Content-Type: application/json");
$parameterdata = file_get_contents('php://input');
$parameterdata = str_replace("null","\"\"",$parameterdata);
MISuploadlogger($parameterdata);

$dataToShare = json_decode($parameterdata);

$Side=trim($dataToShare->Side);
$ReportId=trim($dataToShare->ReportId);
$GroupName=strtoupper(trim($dataToShare->GroupName));
$Id=trim($dataToShare->Id);
$AddedBy=trim($dataToShare->AddedBy);

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

    $listSql = "SELECT \"GROUP_ID\" FROM accounts.\"accountGroupMaster\" where true _gid";
	  $listSql = str_replace("_gid", $Id!=''?" and \"GROUP_ID\"=".$Id." ":"",$listSql );

    // $listSql = str_replace("_gname",$GroupName!=''?" and UPPER(\"GROUP_NAME\") LIKE '%".$GroupName."%' ":"",$listSql );

    MISuploadlogger($listSql);	
    $DataQuery = pg_query(OpenCon(), $listSql);
    
    $Count = pg_num_rows($DataQuery);
	
    if($Count > 0){

      $updateDetailQuery =" Update accounts.\"accountGroupMaster\" set \"REPORT_ID\" = '_rid',\"GROUP_NAME\"='_gname',\"SIDE\"='_side' where true _gid;";

      $updateDetailQuery = str_replace("_gid"," and \"GROUP_ID\" = '".$Id."' ",$updateDetailQuery);
      $updateDetailQuery = str_replace("_side",$Side,$updateDetailQuery);
      $updateDetailQuery = str_replace("_gname",$GroupName,$updateDetailQuery);
      $updateDetailQuery = str_replace("_rid",$ReportId,$updateDetailQuery);

      $result = pg_query(OpenCon(),$updateDetailQuery);
      $effectiveRow = pg_affected_rows($result);

      if($effectiveRow > 0){
        $Message = "Updated Successfully";
      }else{
        $Message = "Not Updated";
      }

    }

  }else{
    $lastIdQuery = "SELECT \"GROUP_ID\" FROM accounts.\"accountGroupMaster\" ORDER BY \"GROUP_ID\" DESC";
  	MISuploadlogger($lastIdQuery);
    $DataQuery = pg_query(OpenCon(), $lastIdQuery);
	
    $LastId = pg_fetch_assoc($DataQuery);
    $TablelastId = $LastId['GROUP_ID']+1;

    $sql_name = '"GROUP_NAME","DATE_ADDED","REPORT_ID","SIDE","GROUP_ID","ADDED_BY"';

    $sql_val = "'".$GroupName."','".date("Y-m-d h:i:s",time())."','".$ReportId."','".$Side."','".$TablelastId."','".$AddedBy."'";

    $InsertQuery ='Insert into accounts."accountGroupMaster" ('.$sql_name.') Values ('.$sql_val.');';

    $result = pg_query(OpenCon(),$InsertQuery);
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