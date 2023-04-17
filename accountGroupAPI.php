<?php

include "inc.php"; 

// MISuploadlogger("Entering in Account Sub Group Page");

header("Content-Type: application/json");
$parameterdata = file_get_contents('php://input');
$parameterdata = str_replace("null","\"\"",$parameterdata);
// MISuploadlogger($parameterdata);
$dataToExport = json_decode($parameterdata);

$GroupName = strtoupper($dataToExport->GroupName);
$Id = $dataToExport->Id;
$ReportId = $dataToExport->ReportId;
$Side = $dataToExport->Side;

class clsDataTable
{

public $Id;
public $GroupName;
public $ReportId;
public $Side;
}

$arrayDataRows = array();

try {

$DataEntryQuery = "SELECT * FROM accounts.\"accountGroupMaster\" WHERE true _rid _side _gid _gname";

$DataEntryQuery = str_replace("_gid",$Id!=''?" and \"GROUP_ID\"='".$Id."' ":"",$DataEntryQuery );

$DataEntryQuery = str_replace("_gname",$GroupName!=''?" and UPPER(\"GROUP_NAME\") LIKE '%".$GroupName."%' ":"",$DataEntryQuery );

$DataEntryQuery = str_replace("_rid",$ReportId!=''?" and \"REPORT_ID\"='".$ReportId."' ":"",$DataEntryQuery );

$DataEntryQuery = str_replace("_side",$Side!=''?" and \"SIDE\"='".$Side."' ":"",$DataEntryQuery );

// MISuploadlogger("Query to extract the records-----\n".$DataEntryQuery);    

$getDatafromData = pg_query(OpenCon(), $DataEntryQuery);

$i=1;
while ($dataList =  pg_fetch_assoc($getDatafromData)){

   $objDataTable = new clsDataTable();

   $objDataTable->ReportId =$dataList['REPORT_ID'];
   $objDataTable->GroupName =$dataList['GROUP_NAME'];
   $objDataTable->Id =$dataList['GROUP_ID'];
   $objDataTable->Side =$dataList['SIDE'];
   
   $a = array_push($arrayDataRows,$objDataTable);

   $i++;

}  

} catch (Exception $ex) {

// echo $ex->getMessage();
echo json_encode(['Status'=>'-1','Message'=>'Failed'],JSON_PRETTY_PRINT);

} finally {

echo json_encode(['status'=>0,'AccountGroupData'=>$arrayDataRows],JSON_PRETTY_PRINT);  

}







?>