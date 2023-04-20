<?php
include "inc.php";

header("Content-Type: application/json");
$parameterdata = file_get_contents('php://input');
$parameterdata = str_replace("null","\"\"",$parameterdata);
//MISuploadlogger($parameterdata);

// {   
//  "VoucherNo": "",
//   "VoucherDate": "2023-03-02",
//   "Note": "test",
//   "Type": "JV",
//   "AddedBy": "125",
//   "ListOfTransaction": [
//     {
//       "Debit": "0",
//       "Credit": "125.00",
//       "AccountCode":"SLB0025"
//       "Narration": "Being Commission for Tinfc online processed"
//     },
//     {
//       "Debit": "125.00",
//       "Credit": "0",
//       "AccountCode":"SLB0024"
//       "Narration": "Being Commission for Tinfc online processed"
//     }
//   ]
// }
$var ='';
$dataToShare = json_decode($parameterdata);

$VoucherNo = $dataToShare->VoucherNo;
$VoucherDate = $dataToShare->VoucherDate." ".date('H:i:s',time());
$DateAddedWithTime = date('Y-m-d H:i:s');
$DateAdded = date('Y-m-d');
$Note = $dataToShare->Note;
$Type = $dataToShare->Type;
$AddedBy = $dataToShare->UserId;
$ListOfJson = json_encode($dataToShare->ListOfTransaction);
// $voucherNo = $Type."-".date('dmy')."-".GetVoucherSequenceOftheDay();
$AutoVoucherNo = $Type."-".time();

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

  foreach ($dataToShare->ListOfTransaction as $value) {

   $lastIdQuery = "SELECT \"Id\" FROM accounts.\"ledgerMaster\" ORDER BY \"Id\" DESC";
    $DataQuery = pg_query(OpenCon(), $lastIdQuery);
  
    $LastId = pg_fetch_assoc($DataQuery);
    $TablelastId = $LastId['Id']+1;
  
  $sql_name = '"Id","AccountId","VoucherNo","Narration","DateAdded","AddedBy","Debit","Credit","Type","Balance"';

  $sql_val = "'".$TablelastId."','".$value->AccountCode."','".$AutoVoucherNo."','".$value->Narration."','".$DateAddedWithTime."','".$AddedBy."','".$value->Debit."','".$value->Credit."','".$Type."','0'";

$query = ' Insert into accounts."ledgerMaster" ('.$sql_name.') Values ('.$sql_val.') ;';

MISuploadlogger($query);

$misinsert = pg_query(OpenCon(),$query);

  
}

  if($VoucherNo!=''){

    $listSql = "SELECT \"VoucherNo\" FROM accounts.\"voucherEntry\" where true _vno";
	  $listSql = str_replace("_vno", $VoucherNo!=''?" and \"VoucherNo\"='".$VoucherNo."' ":"",$listSql );

    // $listSql = str_replace("_gname",$GroupName!=''?" and UPPER(\"GROUP_NAME\") LIKE '%".$GroupName."%' ":"",$listSql );

    MISuploadlogger($listSql);	
    $DataQuery = pg_query(OpenCon(), $listSql);
    
    $Count = pg_num_rows($DataQuery);
	
    if($Count > 0){

      $updateDetailQuery =" Update accounts.\"voucherEntry\" set \"Note\" = '_note',\"JsonData\"='_jdata',\"Type\"='_type' where true _vno;";

      $updateDetailQuery = str_replace("_vno"," and \"VoucherNo\" = '".$VoucherNo."' ",$updateDetailQuery);
      $updateDetailQuery = str_replace("_type",$Type,$updateDetailQuery);
      $updateDetailQuery = str_replace("_jdata",$ListOfJson,$updateDetailQuery);
      $updateDetailQuery = str_replace("_note",$Note,$updateDetailQuery);

      $result = pg_query(OpenCon(),$updateDetailQuery);
      $effectiveRow = pg_affected_rows($result);

      if($effectiveRow > 0){
        $Message = "Updated Successfully";
      }else{
        $Message = "Not Updated";
      }

    }

  }else{
    $lastIdQuery = "SELECT \"VID\" FROM accounts.\"voucherEntry\" ORDER BY \"VID\" DESC";
  	MISuploadlogger($lastIdQuery);
    $DataQuery = pg_query(OpenCon(), $lastIdQuery);
	
    $LastId = pg_fetch_assoc($DataQuery);
    $TablelastId = $LastId['VID']+1;

    $sql_name = '"VID","VoucherNo","DateAdded","JsonData","VoucherDate","AddedBy","Type","Note"';

    $sql_val = "'".$TablelastId."','".$AutoVoucherNo."','".$DateAdded."','".$ListOfJson."','".$VoucherDate."','".$AddedBy."','".$Type."','".$Note."'";

    $InsertQuery ='Insert into accounts."voucherEntry" ('.$sql_name.') Values ('.$sql_val.');';

    MISuploadlogger($InsertQuery);

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