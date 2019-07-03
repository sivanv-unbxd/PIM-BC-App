<?php  
include("db_config.php"); 
error_reporting(0);


$api_check= $_GET['api_check'];
if($api_check==1)
{
include("header.php"); 
 $key= $_REQUEST['api_key']; 
}else {
 $data = json_decode(file_get_contents('php://input'), true);

$key=$data['api_key'];
}


  $Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";

$sql = "SELECT  * from pim_users where api_key='".$key."'";



$result = $conn->query($sql);
$total=$result->num_rows;
 $checkres=$result->fetch_assoc();

 
 $Store_Hash =$checkres['bc_store_hash'];
 $api_key =$checkres['api_key'];
  
if(empty($key) ||  $total==0)
{
	if($api_check == 1 ) {
?>
 <div id="opn-pop">
<div class="container" id="model-popup">
  <div class="modal fade in" id="myModal" role="dialog">
    <div class="modal">
      
      <div class="modal-content">
        <div class="modal-header">
       <h2> Invaild API Key!!! </h2>
      
        </div>
        <div class="modal-body">
		 <p>Please provide Valid API Key</p>
		
             </div>
        <div class="modal-footer">
           <a  href="import.php?id=<?php echo $Store_Hash;?>" class="btn btn-default" id="close" data-dismiss="modal">Close</a>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
</div>

<?php
	}
	else{
		$result_return=array("result"=>"Please provide Valid API Key");
		header('Content-Type: application/json');
	echo json_encode($result_return); 
	}
	
	
}
else {

$url = 'https://pim-app-dev.unbxd.io/pim/v1/products';
 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Authorization: $api_key",
					 'Content-Type: application/json',
                 'Cache-Control: no-cache' )
   );
   $body = '{ "page": 1,
   "count": 5}'; 
 curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
   $ResponsePim = curl_exec($ch);
  curl_close($ch);
$ResultPim = json_decode($ResponsePim);

//print_r($ResultPim);


$totalcount = $ResultPim->data->total;
$totalcount=(int)$totalcount;
//$totalcount=7; 

$totalno=($totalcount/100);

	$fixedvalue=100;
 $checkfloat=is_float($totalno);

if($checkfloat==1)
{
	$checktotal_nu=explode(".",$totalno);
 $checktotal_nu[0];
if($totalcount<=100)
{
	$lastvalue=$totalcount;
}else {
	$lastvalue=$checktotal_nu[1]*10;
}
	$totalno=$checktotal_nu[0]+1;
}else {
	
	$totalno=$totalno;
}

//echo $totalno;


//$store_hash=$_GET['id'];
 $curntdate = date('Y-m-d H:i:s');
 $ProductName="Import_".$curntdate;
$Insertimportdata= "INSERT INTO import_products (user_id,name,created)
VALUES ('$Store_Hash','$ProductName','$curntdate')";
if ($conn->query($Insertimportdata) === TRUE) {
     $last_id = $conn->insert_id;
}


$x=1;
while($x <= $totalno) {
   
  // echo $x;
   if($x==$totalno)
   {
	   
	 $productno=$lastvalue; 
	   
   } else {
	   $productno=$fixedvalue; 
	   
   }
   $key=$Store_Hash;
   
    $curl = curl_init();
 curl_setopt_array($curl, array(
   CURLOPT_URL => $Site_url."/api/v1/product_api.php?id=".$key."&total=".$productno."&page=".$x."&lastid=".$last_id."&api_check=".$api_check ,
   CURLOPT_RETURNTRANSFER => true,
   CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10, 
   CURLOPT_TIMEOUT => 30,
   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
   CURLOPT_CUSTOMREQUEST => "GET",
   CURLOPT_HTTPHEADER => array(
     "accept: application/json",
    "content-type: application/json"
   ),
 ));
 $response = curl_exec($curl); 
  curl_close($ch);

   /*$curl_handle=curl_init();
  curl_setopt($curl_handle,CURLOPT_URL,$Site_url."/api/v1/product.php?id=".$key."&total=".$productno."&page=".$x."&lastid=".$last_id."&api_check=".$api_check);
  curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
  curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
  $buffer = curl_exec($curl_handle);
  curl_close($curl_handle);
  if (empty($buffer)){
      print "Nothing returned from url.<p>";
  }
  else{
      print $buffer;
  }
  */



   
   
  //$output = shell_exec("/usr/local/bin/php -q product.php  $productno $x $key 2>&1 &"); 
  //echo "<pre>$output</pre>";
    $x++;
	
 
} 

 $Donedata= "Select * from products where status='Done' and import_id=".$last_id;
  $Faildata= "Select * from products where status='Fail' and import_id=".$last_id;
       $updated= "Select * from products where status='Updated' and import_id=".$last_id;
	   
	   
 $Donedata=$conn->query($Donedata);
 $Donedata=$Donedata->num_rows;

  $Faildata= $conn->query($Faildata);
$Faildata=$Faildata->num_rows;
  
   $updated=$conn->query($updated);
$updated=$updated->num_rows;

$conn->close();

 
 if($Donedata>0 || $updated>0)
 {
	if($api_check == 1 ) {
?>
 <div id="opn-pop">
<div class="container" id="model-popup">
  <div class="modal fade in" id="myModal" role="dialog">
    <div class="modal">
      
      <div class="modal-content">
        <div class="modal-header">
       <h2> Congrats!!! </h2>
      
        </div>
        <div class="modal-body">
		 <p>Your produts have processed to bigcommerce</p>
		 <p>Created : <b><?= $Donedata;?></b>  </p>
		 <p>Failed : <b><?= $Faildata;?></b></b>
         <p>Updated : <b><?= $updated;?></b></b>		 
             </div>
        <div class="modal-footer">
           <a  href="import.php?id=<?php echo $Store_Hash;?>" class="btn btn-default" id="close" data-dismiss="modal">Close</a>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
</div>

<?php
	}
	else{
		$result_return=array("result"=>"Product imported Successfully");
		
header('Content-Type: application/json');
echo json_encode($result_return);
	}
}else {
	 	if($api_check == 1 ) {
?>
 <div id="opn-pop">
<div class="container" id="model-popup">
  <div class="modal fade in" id="myModal" role="dialog">
    <div class="modal">
      
      <div class="modal-content">
        <div class="modal-header">
       <h2> Products Not Available or issue with the API keys </h2>
      
        </div>
        <div class="modal-body">
		 <p>Sorry no product available for import or issue with the BigCommerce Api keys</p>
		             </div>
        <div class="modal-footer">
		<a  href="update_api.php?id=<?php echo $Store_Hash;?>" class="btn btn-default" id="close" data-dismiss="modal">Update API</a> OR
           <a  href="import.php?id=<?php echo $Store_Hash;?>" class="btn btn-default" id="close" data-dismiss="modal">Close</a>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
</div>

<?php
	}
	else{
		$result_return=array("result"=>"Sorry no product available for import");
		header('Content-Type: application/json');
	echo json_encode($result_return); 
	}
	
	

}
}

	
?>