<?php

include "inc.php"; 

MISuploadlogger("Entering in Account Group Page");

header("Content-Type: application/json");
$parameterdata = file_get_contents('php://input');
$parameterdata = str_replace("null","\"\"",$parameterdata);
MISuploadlogger($parameterdata);
$dataToExport = json_decode($parameterdata);

$accountName = strtoupper($dataToExport->AccountName);
$groupId = $dataToExport->GroupId;
$status = $dataToExport->Status;

class clsDataTable
{

  public $Id;
  public $AccountName;
  public $GroupId;
  public $OpeningBalance;
  public $CurrentBalance;
  public $Status;

}

$arrayDataRows = array();

// ////---------------------- Extraction from DataBase --------------------------------

$DataEntryQuery = "SELECT * FROM accounts.\"accountMaster\" WHERE true _status _accGrp _name";

$DataEntryQuery = str_replace("_accGrp",$groupId!=''?" and \"GROUP_ID\"='".$groupId."' ":"",$DataEntryQuery );

$DataEntryQuery = str_replace("_name",$accountName!=''?" and UPPER(\"ACCOUNT_NAME\") LIKE '%".$accountName."%' ":"",$DataEntryQuery );

$DataEntryQuery = str_replace("_status",$status!=''?" and \"STATUS\"='".$status."' ":"",$DataEntryQuery );


MISuploadlogger("Query to extract the records-----\n".$DataEntryQuery);    

$getDatafromData = pg_query(OpenCon(), $DataEntryQuery);

  $i=1;
  while ($dataList =  pg_fetch_assoc($getDatafromData)){

     $objDataTable = new clsDataTable();

     $objDataTable->Id = $dataList['ACCOUNT_ID'];
     $objDataTable->AccountName = $dataList['ACCOUNT_NAME'];
     $objDataTable->GroupId = $dataList['GROUP_ID'];
     $objDataTable->OpeningBalance = $dataList['OPENING_BALANCE'];
     $objDataTable->CurrentBalance = $dataList['CURRENT_BALANCE'];
     $objDataTable->Status = $dataList['STATUS'];
     
     $a = array_push($arrayDataRows,$objDataTable);

     $i++;

  }



echo json_encode(['status'=>0,'AccountData'=>$arrayDataRows],JSON_PRETTY_PRINT);

?>