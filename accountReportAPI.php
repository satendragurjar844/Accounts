<?php

include "inc.php"; 

// MISuploadlogger("Entering in Account Report Page");

header("Content-Type: application/json");
$parameterdata = file_get_contents('php://input');
$parameterdata = str_replace("null","\"\"",$parameterdata);
// MISuploadlogger($parameterdata);
$dataToExport = json_decode($parameterdata);

$ReportName = strtoupper($dataToExport->ReportName);
$ReportId = $dataToExport->ReportId;

class clsDataTable
{

  public $ReportId;
  public $ReportName;
}

$arrayDataRows = array();

// ////---------------------- Extraction from DataBase --------------------------------

$DataEntryQuery = "SELECT * FROM accounts.\"accountReportMaster\" WHERE true _name _aid";

$DataEntryQuery = str_replace("_aid",$ReportId!=''?" and \"REPORT_ID\"='".$ReportId."' ":"",$DataEntryQuery );

$DataEntryQuery = str_replace("_name",$ReportName!=''?" and UPPER(\"REPORT_NAME\") LIKE '%".$ReportName."%' ":"",$DataEntryQuery );

$DataEntryQuery;
 MISuploadlogger("Query to extract the records-----\n".$DataEntryQuery);    

$getDatafromData = pg_query(OpenCon(), $DataEntryQuery);

  $i=1;
  while ($dataList =  pg_fetch_assoc($getDatafromData)){

     $objDataTable = new clsDataTable();

     $objDataTable->ReportId =$dataList['REPORT_ID'];
     $objDataTable->ReportName =$dataList['REPORT_NAME'];
     $a = array_push($arrayDataRows,$objDataTable);

     $i++;

  }

echo json_encode(['status'=>0,'AccountReportData'=>$arrayDataRows],JSON_PRETTY_PRINT);

?>