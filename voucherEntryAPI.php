<?php

include "inc.php"; 

MISuploadlogger("Entering in Voucher Entry Page");

header("Content-Type: application/json");
$parameterdata = file_get_contents('php://input');
$parameterdata = str_replace("null","\"\"",$parameterdata);
// MISuploadlogger($parameterdata);
$dataToExport = json_decode($parameterdata);

$DateAdded = $dataToExport->DateAdded;
$Type = $dataToExport->Type;
$VoucherNo = $dataToExport->VoucherNo;

class clsDataTable
{

public $Id;
public $Note;
public $DateAdded;
public $AddedBy;
public $VoucherDate;
public $JsonData;
public $VoucherNo;
public $Type;
}

$arrayDataRows = array();

try {

$DataEntryQuery = "SELECT * FROM accounts.\"voucherEntry\" WHERE true _vno _date _type";

$DataEntryQuery = str_replace("_date",$DateAdded!=''?" and \"DateAdded\"='".$DateAdded."' ":"",$DataEntryQuery );

$DataEntryQuery = str_replace("_vno",$VoucherNo!=''?" and \"VoucherNo\"='".$VoucherNo."' ":"",$DataEntryQuery );

$DataEntryQuery = str_replace("_type",$Type!=''?" and \"Type\"='".$Type."' ":"",$DataEntryQuery );

MISuploadlogger("Query to extract the records-----\n".$DataEntryQuery);    

$getDatafromData = pg_query(OpenCon(), $DataEntryQuery);

$dataList =  pg_fetch_assoc($getDatafromData);

while ($dataList =  pg_fetch_assoc($getDatafromData)){

   $objDataTable = new clsDataTable();

   $objDataTable->Id =$dataList['VID'];
   $objDataTable->VoucherDate =$dataList['VoucherDate'];
   $objDataTable->VoucherNo =$dataList['VoucherNo'];
   $objDataTable->Type =$dataList['Type'];
   $objDataTable->DateAdded =$dataList['DateAdded'];
   $objDataTable->JsonData =$dataList['JsonData'];
   $objDataTable->AddedBy =$dataList['AddedBy'];
   $objDataTable->Note =$dataList['Note'];
   
   $a = array_push($arrayDataRows,$objDataTable);

}  

} catch (Exception $ex) {

// echo $ex->getMessage();
echo json_encode(['Status'=>'-1','Message'=>'Failed'],JSON_PRETTY_PRINT);

} finally {

echo json_encode(['status'=>0,'VoucherData'=>$arrayDataRows],JSON_PRETTY_PRINT);  

}







?>