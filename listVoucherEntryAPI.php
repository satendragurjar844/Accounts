<?php

include "inc.php"; 

MISuploadlogger("Entering in Journal Master Page");

header("Content-Type: application/json");
$parameterdata = file_get_contents('php://input');
$parameterdata = str_replace("null","\"\"",$parameterdata);
MISuploadlogger($parameterdata);
$dataToExport = json_decode($parameterdata);

$transactionDate = $dataToExport->TransactionDate;
$Type = $dataToExport->Type;
$VoucherNo = strtoupper($dataToExport->VoucherNo);

class clsDataTable
{

  public $Number;
  public $Id;
  public $VoucherNo;
  public $TransactionDate;
  public $DateAdded;
  public $Type;
  public $AddedBy;
  public $ListOfArray;
}

$arrayDataRows = array();


// ////---------------------- Extraction from DataBase --------------------------------

$DataEntryQuery = "SELECT * FROM panprogres.\"voucherEntry\" WHERE true _tDate _type _voucher ORDER BY \"Id\" ASC";


$DataEntryQuery = str_replace("_tDate",$transactionDate!=''?" and \"transactionDate\"='".$transactionDate."' ":"",$DataEntryQuery );
$DataEntryQuery = str_replace("_voucher",$VoucherNo!=''?" and UPPER(\"voucherNo\") LIKE '%".$VoucherNo."%' ":"",$DataEntryQuery );
$DataEntryQuery = str_replace("_type",$Type!=''?" and \"Type\"='".$Type."' ":"",$DataEntryQuery);


MISuploadlogger("Query to extract the records-----\n".$DataEntryQuery);    

$getDatafromData = pg_query(OpenCon(), $DataEntryQuery);

$countrows = pg_num_rows($getDatafromData);
if($countrows > 0){
  $i=1;
  while ($dataList =  pg_fetch_assoc($getDatafromData)){


$ListOfArray= json_decode($dataList['listOfJson']);

     $objDataTable = new clsDataTable();

     $objDataTable->Number =$i;
     $objDataTable->Id =$dataList['Id'];
     $objDataTable->VoucherNo =$dataList['voucherNo'];
     $objDataTable->TransactionDate = $dataList['transactionDate'];
     $objDataTable->Type = $dataList['Type'];
     $objDataTable->AddedBy = $dataList['addedBy'];
     $objDataTable->DateAdded = $dataList['dateAdded'];
     $objDataTable->ListOfArray = $ListOfArray;

     $a = array_push($arrayDataRows,$objDataTable);

     $i++;

  }

}


echo json_encode(['status'=>0,'VoucherData'=>$arrayDataRows],JSON_PRETTY_PRINT);

?>