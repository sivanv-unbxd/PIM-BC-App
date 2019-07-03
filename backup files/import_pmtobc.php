<?php  include("db_config.php");
include("header.php"); 
error_reporting(0);
$url = 'https://pim-app-dev.unbxd.io/api/v1/stores/products/';
 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                     'Authorization: 5ccbeb6ebc1413682ea46c3c',
					 //"Authorization: $org_key",
					 'Content-Type: application/json',
                 'Cache-Control: Cache-Control' )
   );
   $body = '{ "page": 1,
   "count": 5}'; 
 curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
   $ResponsePim = curl_exec($ch);
  curl_close($ch);
$ResultPim = json_decode($ResponsePim);

 $totalcount = $ResultPim->data->total;
 $totalcount=(int)$totalcount;
//$totalcount=110;

$totalno=($totalcount/100);

	$fixedvalue=100;
 $checkfloat=is_float($totalno);

if($checkfloat==1)
{
	$checktotal_nu=explode(".",$totalno);
 $checktotal_nu[0];

	$lastvalue=$checktotal_nu[1]*10;
	
	$totalno=$checktotal_nu[0]+1;
}else {
	
	$totalno=$totalno;
}
$store_hash=$_GET['id'];
 $curntdate = date('Y-m-d H:i:s');
 $ProductName="Import_".$curntdate;
 $Insertimportdata= "INSERT INTO import_products (	user_id,name,created)
VALUES ('$store_hash','$ProductName','$curntdate')";
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
   $key=$_GET['id'];
   
   $curl = curl_init();
   
curl_setopt_array($curl, array(
  CURLOPT_URL => "http://myvirtualpartner.net/bigcomerce/product.php?id=".$_GET['id']."&total=".$productno."&page=".$x."&lastid=".$last_id."",
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
//echo $reponse;
//print_r($response);
//$err = curl_error($curl);
   
   
  //$output = shell_exec("/usr/local/bin/php -q product.php  $productno $x $key 2>&1 &"); 
  //echo "<pre>$output</pre>";
    $x++;
	
 
} 

$Donedata= "Select * from products where status='Done' and import_id=".$last_id;
$Faildata= "Select * from products where status='Fail' and import_id=".$last_id;

 $Donedata=$conn->query($Donedata);
$Donedata=$Donedata->num_rows;

  $Faildata= $conn->query($Faildata);
 $Faildata=$Faildata->num_rows;
  

$conn->close();

  include("footer.php"); 
 
 if($Donedata>0 || $Faildata>0)
 {

if($import==1)
{
	
	$result_return=array("result"=>"Product imported Successfully");
	
	echo json_encode($result_return); 
	
} else {
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
             </div>
        <div class="modal-footer">
           <a  href="import.php?id=<?php echo $store_hash;?>" class="btn btn-default" id="close" data-dismiss="modal">Close</a>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
</div>

 <?php }}else {
	 
	 if($import==1)
{
	
	$result_return=array("result"=>"Sorry no product available for import");
	
	echo json_encode($result_return); 
	
} else {
	 
	?>
	<div id="opn-pop">
<div class="container" id="model-popup">
  <div class="modal fade in" id="myModal" role="dialog">
    <div class="modal">
      
      <div class="modal-content">
        <div class="modal-header">
       <h2> No Product available </h2>
      
        </div>
        <div class="modal-body">
		 <p>Sorry no product available for import</p>
		 <p>Created : <b><?= $Donedata;?></b>  </p>
		 <p>Failed : <b><?= $Faildata;?></b></b>  
             </div>
        <div class="modal-footer">
           <a  href="import.php?id=<?php echo $store_hash;?>" class="btn btn-default" id="close" data-dismiss="modal">Close</a>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
</div>
	
<?php	
}
}




//echo (5 % 3)."\n";   

/*$output = shell_exec('ls -lart');
echo "<pre>$output</pre>";*/

//$output = shell_exec("/usr/bin/oneuser create test10 test10 2>&1");

	
?>