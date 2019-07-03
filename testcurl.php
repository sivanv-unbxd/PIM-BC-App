<?php
error_reporting(0);
include("db_config.php");
require 'vendor/autoload.php';
use Bigcommerce\Api\Client as Bigcommerce;
$Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
$api_check = $_GET['api_check'];
$AllData=array();
 //Check request is coming from API or Bigcommerce Panel
if($api_check==1)
	
{
include("header.php"); 
 $key= $_REQUEST['api_key']; 
}else {
 $data = json_decode(file_get_contents('php://input'), true);

$key=$data['api_key'];
}



//getting the relavent key using the id  
$sql = "SELECT  * from pim_users where api_key='".$key."'";
$result = $conn->query($sql);
$total=$result->num_rows;
 $checkres=$result->fetch_assoc();
 
//check if any key exist. if not then allert oe responce will be generated
if($result==0)
{
	$result_return=array("result"=>"Please provide Valid API Key");
	header('Content-Type: application/json');
	echo json_encode($result_return); 
	
}else {

 $store_hash =$checkres['bc_store_hash'];


 $Zip_url =  $Site_url."/api/v1/ImportToPim.zip";

$sql = "SELECT  bc_api_data.* ,
pim_users.* FROM   bc_api_data ,pim_users  WHERE  bc_api_data.store_hash = '". $store_hash ."' and pim_users.bc_store_hash = '". $store_hash ."' ";


$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row[] = $result->fetch_assoc()) {
	echo  $store_hash = $row[0]['store_hash'];
	$user_email = $row[0]['user_email'];
	$api_key_pw = $row[0]['api_key_pw'];
	$client_id = $row[0]['client_id'];
	$client_secret = $row[0]['client_secret'];
	$oauth_token = $row[0]['oauth_token'];
	$path = $row[0]['path'];         
	 $org_key = $row[0]['org_key']; 
	 $api_key = $row[0]['api_key']; 

 
   Bigcommerce::configure(array(
        'store_url' => '"'.$path.'"',
        'username' => '"'.$user_email.'"',
        'api_key' => '"'.$api_key_pw.'"'
    ));
	}
}


/*get bigcommerce products */
$curl = curl_init();
   curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/products?page=1&limit=250",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "content-type: application/json",
   "x-auth-client: $	",
    "x-auth-token: $oauth_token"
  ),
));

$response = curl_exec($curl);
$all_product_data = json_decode($response,true);



//$totalproduct_check=$all_product_data['meta']['pagination']['total'];
$totalproduct_check=$all_product_data['meta']['pagination']['total'];
echo $total_pages=$all_product_data['meta']['pagination']['total_pages'];

$total_pages=$all_product_data['meta']['pagination']['total_pages'];


if ($all_product_data['status'] == "409" || $all_product_data['status'] == "400" || $all_product_data['status'] == "401" || $all_product_data['status'] == "403" || $all_product_data['status'] == "404" || $all_product_data['status'] == "405" || $all_product_data['status'] == "406" || $all_product_data['status'] == "409" || 	$all_product_data['status'] == "413" || $all_product_data['status'] == "415" || $all_product_data['status'] == "422" || $all_product_data['status'] == "429" || $all_product_data['status'] == "301" || $all_product_data['status'] == "304" || $all_product_data['status'] == "503" || $all_product_data['status'] == "507" || $all_product_data['status'] == "501" || $all_product_data['status'] == "500") {

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
else {
	
	
	
//echo $php_array['meta']['pagination']->total;

if($totalproduct_check==0)
{	
echo 'sss';
if($api_check == 1 ) {
?>
 <div id="opn-pop">
<div class="container" id="model-popup">
  <div class="modal fade in" id="myModal" role="dialog">
    <div class="modal">
      
      <div class="modal-content">
        <div class="modal-header">
       <h2> Product Not Availabile !!! </h2>
      
        </div>
        <div class="modal-body">
		 <p>Sorry no product available In bigcommerce for import</p>
		
             </div>
        <div class="modal-footer">
           <a  href="export.php?id=<?php echo $store_hash;?>" class="btn btn-default" id="close" data-dismiss="modal">Close</a>
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

if($totalproduct_check>0)
{
// $curntdate = date('Y-m-d H:i:s');
 // $ProductName="Import_".$curntdate;
// echo $Insertimportdata= "INSERT INTO import_products (user_id,name,created)
// VALUES ('$store_Hash','$ProductName','$curntdate')";
// if ($conn->query($Insertimportdata) === TRUE) {
   // echo   $last_id = $conn->insert_id;
}
	// echo '>>>>>';
echo $newpages=($total_pages/4);

 $checkpage_float=is_float($newpages);
 if($checkpage_float==1)
 {
	 $checktotal_nu=explode(".",$newpages);
	 print_r($checktotal_nu);
     $total_pages_final=$checktotal_nu[0]+1;
	 
 }
$total_pages_final;
 // die('>>>');
// $totalcount=(int)$totalproduct_check;
// //$totalcount= 150; 

// echo $totalno=($totalcount/250);

	// $fixedvalue=250;
 // $checkfloat=is_float($totalno);

// if($checkfloat==1)
// {
	// $checktotal_nu=explode(".",$totalno);
 // $checktotal_nu[0];
// if($totalcount<=250)
// {
	// $lastvalue=$totalcount;
// }else {
	// $lastvalue=$checktotal_nu[1]*10;
// }
	// $totalno=$checktotal_nu[0]+1;
// }else {
	
	// $totalno=$totalno;
// }


// $curl_arr = array();



// $master = curl_multi_init();

// echo '>';
// echo $totalno;
// //$x=$total_pages;
// echo $newpages=($total_pages/4);

 // $checkpage_float=is_float($newpages);
// if($checkpage_float==1)
 // {
	 // $checktotal_nu=explode(".",$newpages);
	 // print_r($checktotal_nu);
     // $total_pages_final=$checktotal_nu[0]+1;
	 
 // }
 
 // $i=1;
  // $handle=curl_init($Site_url."/api/v1/curl2.php?api_key=".$_REQUEST['api_key']."&total=50&page=".$i."&lastid=".$last_id."&api_check=".$api_check."&total_pages=".$total_pages);
// curl_setopt($handle, CURLOPT_VERBOSE, true);
// curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
// curl_setopt($ch, CURLOPT_TIMEOUT, 20);
 // $content = curl_exec($handle);
 // die('dddd');
// $x=1;
//$total_pages_final=1;
$node_count=$total_pages_final;
$last_id=1;
//$node_count = count($nodes);

$curl_arr = array();
$master = curl_multi_init();
$i=1;
while($i <= $total_pages_final) {
	
	 $total=$i;
  echo  $lastindex=$i*4;
 echo  $afterminus=$lastindex-3;
	
echo $url = $Site_url."/api/v1/curl2.php?api_key=".$_REQUEST['api_key']."&total=100&page=".$i."&lastid=".$last_id."&api_check=".$api_check."&total_pages=".$total_page."&statrt=".$afterminus."&lastto=".$lastindex;

die('dd');
    //$url =$nodes[$i];
      //$url =$nodes[$i];
    $curl_arr[$i] = curl_init($url);
    curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($master, $curl_arr[$i]);
	   curl_setopt($curl_arr[$i], CURLOPT_FORBID_REUSE, true);
    curl_setopt($curl_arr[$i], CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curl_arr[$i], CURLOPT_DNS_CACHE_TIMEOUT, 30); 
	$i++;
	
}

do {
    curl_multi_exec($master,$running);
} while($running > 0);

echo "results: ";
for($i = 1; $i < $node_count; $i++)
{
    $results = curl_multi_getcontent  ( $curl_arr[$i]  );
    //echo( $i . "\n" . $results . "\n");
}






die('ddd');
while($x <= $total_pages) {
   
   
   // $total=$x;
   // $lastindex=$x*2;
   // $afterminus=$checkpage-3;
   

   // if($x==$totalno)
   // {
	   
	 // $productno=$lastvalue; 
	   
   // } else {
	   // $productno=$fixedvalue; 
	   
   // }
      // $key=$store_Hash;
	  

echo $url = $Site_url."/api/v1/curl2.php?api_key=".$_REQUEST['api_key']."&total=250&page=".$x."&lastid=".$last_id."&api_check=".$api_check."&total_pages=".$total_pages;
//$url = $Site_url."/api/v1/product.php?id=".$key."&total=".$productno."&page=".$x."&lastid=".$last_id."&api_check=".$api_check;
  //$url =$nodes[$i];
    $curl_arr[$x] = curl_init($url);
    //curl_setopt($curl_arr[$x], CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl_arr[$x], CURLOPT_TIMEOUT, 50); 
    curl_setopt($curl_arr[$x], CURLOPT_HEADER, 0);
    curl_setopt($curl_arr[$x],  CURLOPT_RETURNTRANSFER, false);
    curl_setopt($curl_arr[$x], CURLOPT_FORBID_REUSE, true);
    curl_setopt($curl_arr[$x], CURLOPT_CONNECTTIMEOUT, 50);
    curl_setopt($curl_arr[$x], CURLOPT_DNS_CACHE_TIMEOUT, 50); 

    curl_setopt($curl_arr[$x], CURLOPT_FRESH_CONNECT, true);
    curl_multi_add_handle($master, $curl_arr[$x]);
$x++;
}

do {
    curl_multi_exec($master,$running);
} while($running > 0);


for($x = 0; $x < $node_count; $x++)
{
    $results[] = curl_multi_getcontent  ( $curl_arr[$x]  );
}
	
	
 }
	
}

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
		 <p>Your produts have Imported to Pim</p>
             </div>
        <div class="modal-footer">
           <a  href="export.php?id=<?php echo $store_hash;?>" class="btn btn-default" id="close" data-dismiss="modal">Close</a>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php 
	}
	else{
		$result_return=array("result"=>"Your produts have Imported to Pim Successfully");
		header('Content-Type: application/json');
		echo json_encode($result_return);
	}	










	
?>