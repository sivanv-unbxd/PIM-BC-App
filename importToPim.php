<?php
error_reporting(0);

include("db_config.php");
require 'vendor/autoload.php';
use Bigcommerce\Api\Client as Bigcommerce;
 $Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
$api_check = $_GET['api_check'];

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

if($result==0)
{
	$result_return=array("result"=>"Please provide Valid API Key");
	header('Content-Type: application/json');
	echo json_encode($result_return); 
	
}else {

 $Store_Hash =$checkres['bc_store_hash'];


 $Zip_url =  $Site_url."/api/v1/ImportToPim.zip";

$AllData=array();
 /*$sql = "SELECT auth.* ,  bc_api_data.* ,
pim_users.* FROM   auth , bc_api_data ,pim_users  WHERE  auth.context=bc_api_data.store_hash and auth.context = pim_users.bc_store_hash ";*/
 $sql = "SELECT  bc_api_data.* ,
pim_users.* FROM   bc_api_data ,pim_users  WHERE  bc_api_data.store_hash = '". $Store_Hash ."' and pim_users.bc_store_hash = '". $Store_Hash ."' ";


$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row[] = $result->fetch_assoc()) {
	 $store_hash = $row[0]['store_hash'];
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
/*get bigcommerce categories */
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/37kiq2b0u6/v3/catalog/categories",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "content-type: application/json",
    "x-auth-client: $client_id",
    "x-auth-token: $oauth_token"
  ),
));

$responsecats = curl_exec($curl);
curl_close($curl);
$responsecats = json_decode($responsecats);
$responsecats = json_decode(json_encode($responsecats->data), true);
foreach ($responsecats as $responsecatsval){
	$CatId = $responsecatsval['id'];
	$BigcatNmes[$CatId] = $responsecatsval['name'];
}
/*echo "<pre>";
print_r ($BigcatNmes);
echo "</pre>";
die();*/
	/*Get Brand Name*/
$BrandID = $val['brand_id'];
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$Store_Hash."/v3/catalog/brands",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "content-type: application/json",
   "x-auth-client: $client_id",
    "x-auth-token: $oauth_token"
  ),
));
$responsebrand = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
$responsebrand = json_decode($responsebrand);
$responsebrand = json_decode(json_encode($responsebrand->data), true);
foreach ($responsebrand as $responsebrandVal) {
	$brandName[] = $responsebrandVal['name'];
}
 
/*get bigcommerce products */
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/products?include=variants%2Cimages%2Ccustom_fields",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "content-type: application/json",
   "x-auth-client: $client_id",
    "x-auth-token: $oauth_token"
  ),
));

$response = curl_exec($curl);
curl_close($ch);
$all_product_data = json_decode($response,true);


if ($all_product_data['status'] == "409" || $all_product_data['status'] == "400" || $all_product_data['status'] == "401" || $all_product_data['status'] == "403" || $all_product_data['status'] == "404" || $all_product_data['status'] == "405" || $all_product_data['status'] == "406" || $all_product_data['status'] == "409" || 	$all_product_data['status'] == "413" || $all_product_data['status'] == "415" || $all_product_data['status'] == "422" || $all_product_data['status'] == "429" || $all_product_data['status'] == "301" || $all_product_data['status'] == "304" || $all_product_data['status'] == "503" || $all_product_data['status'] == "507" || $all_product_data['status'] == "501" || $all_product_data['status'] == "500" || empty($oauth_token) ) {

	if($api_check == 1 ) {
?>
 <div id="opn-pop">
<div class="container" id="model-popup">
  <div class="modal fade in" id="myModal" role="dialog">
    <div class="modal">
      <div class="modal-content">
        <div class="modal-header">
       <h2> Issue with the API keys </h2>
        </div> 
        <div class="modal-body">
		 <p>Sorry products not imported or issue with the BigCommerce Api keys</p>
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
$totalproduct_check=$all_product_data['meta']['pagination']['total'];
if($totalproduct_check==0)
{	if($api_check == 1 ) {
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
}else {
$err = curl_error($curl);
curl_close($curl);



$AllJsonData=array();
$myArray = json_decode(json_encode($all_product_data), true);
 $fp = fopen('results.json', 'w');
   foreach ($myArray['data'] as $val) {	 
   /* Big categories*/
   $BigCategory =  $val['categories'];
    $ProductId =  $val['id'];
/*get variants of products*/
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$Store_Hash."/v3/catalog/products/".$ProductId."/variants",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "content-type: application/json",
     "x-auth-client: $client_id",
    "x-auth-token: $oauth_token"
  ),
));
$variantresponse = curl_exec($curl);
curl_close($curl);
	$variantresponse = json_decode($variantresponse); 
$variantresponse = json_decode(json_encode($variantresponse), true);
$final=array();
$GetValArr=array();
//print_r($variantresponse['data']);
foreach ($variantresponse['data'] as $variantresponseVal) {
$optionValue = $variantresponseVal['option_values'];
//throw in another 'strawberry' to demonstrate that it removes multiple instances of the string
$array_without_strawberries = array_diff($variantresponseVal, $optionValue);
foreach ($optionValue as $optionValueVal) {
$arr = array_merge($optionValueVal,$array_without_strawberries);
//print_r($arr);
	}

$Variantsku=array("SKU"=>$variantresponseVal['sku']);
$productsku=array("parentId"=>$val['sku']);
//$VariantparentId=array("parentId"=>$variantresponseVal['product_id']);
 
 $options_values=$variantresponseVal['option_values'];
 if(empty($variantresponseVal['price']) || $variantresponseVal['price']=='' || $variantresponseVal['price']=='null')
	{
		
	} else{ $price = array("price"=>$variantresponseVal['price']);
	}
  

foreach ($options_values as $options_valuesval) {
	$input=array($options_valuesval);
unset($options_valuesval['id']);
$GetValArr=array_merge($Variantsku,$options_valuesval,$productsku,$price);
 } 
array_push($final,$GetValArr);
$GetValArr=array();
}
 
	/*Get Product All Images */

$responseImages = $val['images'];
$imagesALL1 = array();
foreach ($responseImages as $responseImagesVal) { 
$imagesALL1[]= $responseImagesVal['url_standard'];
}
$commaListofImages = implode(",", $imagesALL1);
$data['Item Type']=$val['open_graph_type'];
if(empty($val['id']) || $val['id']=='' || $$val['id']=='null')
	{} else {
		// $data['ParentID']=$val['id'];
		$data['Product ID']=$val['id'];
		// $data['Parent ID']=$data['Product ID'];
		}

//$data['Product ID']=$val['id'];
$data['Product Name']=$val['name'];

if(empty($val['physical']) || $val['physical']=='' || $val['physical']=='null')
	{ }else {$data['Product Type']=$val['physical'];}
$data['SKU']=$val['sku'];
$data['Brand Name']=$responsebrand['name'];
if(empty($val['option_set_id']) || $val['option_set_id']=='' || $val['option_set_id']=='null')
	{ }else {$data['Option Set']=$val['option_set_id'];}


//$data['Option Set']=$val['option_set_id'];
$data['Option Set Align']=$val['option_set_display'];
$data['Price']=$val['price'];
$data['Cost Price']=$val['cost_price'];
$data['Retail Price']=$val['retail_price'];
$data['Sale Price']=$val['sale_price'];
$data['Fixed Shipping Cost']=$val['fixed_cost_shipping_price'];
if(empty($val['is_free_shipping']) || $val['is_free_shipping']=='' || $val['is_free_shipping']=='null')
	{
		
	} else{ $data['Free Shipping']=$val['is_free_shipping'];
	}		
if(empty($val['warranty']) || $val['warranty']=='' || $val['warranty']=='null')
	{
		
	} else {
		$data['Product Warranty']=$val['warranty'];
	}



$data['Product Weight']=$val['weight'];
$data['Product Width']=$val['width'];
$data['Product Height']=$val['height'];
$data['Product Depth']=$val['depth'];

$data['Allow Purchases?']="Y";
$data['Product Visible?']=$val['is_visible'];
$data['Product Availability']=$val['availability'];
$data['Track Inventory']=$val['inventory_tracking'];
$data['Current Stock Level']=$val['inventory_level'];
$data['Low Stock Level']=$val['inventory_warning_level'];
$BigcatNmesVal = implode(",",$BigcatNmes);
$data['Category']=[$BigcatNmesVal];
$BigcatNmes = array();
$data['Search Keywords']=$val['search_keywords'];
$data['Page Title']=$val['page_title'];
if(empty($val['meta_keywords']) || $val['meta_keywords']=='' || $val['meta_keywords']=='null')
	{
		
	} else {
		$data['Meta Keywords']=$val['meta_keywords'];
	}

//$data['Meta Keywords']=$val['meta_keywords'];
if(empty($val['meta_description']) || $val['meta_description']=='' || $val['meta_description']=='null')
	{
		
	} else {
		$data['Meta Description']=$val['meta_description'];
	}

//checkempty($data['Meta Description'],$val['meta_description']);
//$data['Meta Description']=$val['meta_description'];
if(empty($val['is_condition_shown']) || $val['is_condition_shown']=='' || $val['is_condition_shown']=='null')
	{
		
	} else {
		$data['Show Product Condition?']=$val['is_condition_shown'];
	}
	
if(empty($val['condition']) || $val['condition']=='' || $val['condition']=='null')
	{
		
	} else {
		$data['Product Condition']=$val['condition'];
	}	


// $data['Event Date Required?']="";
// $data['Event Date Name']="";
// $data['Event Date Is Limited?']="";
// $data['Event Date Start Date']="";
// $data['Event Date End Date']="";
$data['Sort Order']=$val['sort_order'];
$data['Product Tax Class']=$val['tax_class_id'];
$data['Product UPC/EAN']=$val['upc'];
// $data['Stop Processing Rules']="";
$data['Product URL']=$val['custom_url']['url'];
// $data['Redirect Old URL?']=$val['custom_url']['is_customized'];
if(empty($val['warranty']) || $val['warranty']=='' || $val['warranty']=='null')
	{
		
	} else {
		$data['Product Warranty']=$val['warranty'];
	}
if(empty($val['gtin']) || $val['gtin']=='' || $val['gtin']=='null')
	{
		
	} else{ $data['GPS Global Trade Item Number']=$val['gtin'];
	}
	if(empty($val['mpn']) || $val['mpn']=='' || $val['mpn']=='null')
	{
		
	}else {
$data['GPS Manufacturer Part Number']=$val['mpn'];
	}	
	



// $data['GPS Gender']="";
// $data['GPS Age Group']="";
// $data['GPS Color']="";
// $data['GPS Material']="";
// $data['GPS Pattern']="";
// $data['GPS Item Group ID']="";
// $data['GPS Category']="";
// $data['GPS Enabled']="";
if(empty($val['product_tax_code']) || $val['product_tax_code']=='' || $val['product_tax_code']=='null')
	{

	} else { 			
$data['Avalara Product Tax Code']=$val['product_tax_code']; }

if(empty($commaListofImages) || $commaListofImages=='' || $commaListofImages=='null')
	{

	} else { 			
$data['image_url']=$commaListofImages; }

$responseCustomField =$val['custom_fields'];
foreach ($responseCustomField as $responseCustomval ) {
  $name =  $responseCustomval['name'];
	$value =  $responseCustomval['value'];
	if($name=='Product Code/SKU')
	{
	}else {
	$data[$name]=$value;
	}

			}
		
			
			$data=array($data);
$final = array_filter($final);  
if(!empty($final)){
	
	$newsku['SKU']=$val['sku'];
	
	//array_push($final,$newsku);
$data=array_merge($data,$final);  
}
$newsku=array();
array_push($AllJsonData,$data);
$data=array();		
      }

	  //$AllJsonData = array_values($AllJsonData);
	/*  echo "<pre>";
print_r ($data1);
  echo "</pre>";*/
//$emptyRemoved = array_filter($AllJsonData);

//echo $jsonarray=json_encode($emptyRemoved);

 $jsonarray=json_encode($AllJsonData);
  

$string = str_replace(array('[[',']]'),'',$jsonarray);
$string = str_replace(array('[',']'),'',$string);

  $final='['.$string.']';
 
	
 //$AllJsonData = substr($b, 1,-1);
 //echo $AllJsonData;
 //die('dd');
 //$aa =  str_replace(array('[', ']'), '', htmlspecialchars(json_encode($AllJsonData), ENT_NOQUOTES));
 fwrite($fp, $final);
	    
	
	fclose($fp); 
	
	
$zip = new ZipArchive;
if ($zip->open('ImportToPim.zip', ZipArchive::CREATE) === TRUE)
{
    // Add random.txt file to zip and rename it to newfile.txt
    $zip->addFile('results.json');
    // Add a file new.txt file to zip using the text specified
    // All files are added, so close the zip file.
    $zip->close();
}
// Close the file
/* Prduct Import Api To Pim */
//$url ='https://pim-app-dev.unbxd.io/pim/v1/imports';
$header = array('Authorization:'.$api_key.'','Content-Type: application/json', 
'cache-control: no-cache');

$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
 $body ='{
"url": "'.$Zip_url.'"
}';
 curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
$retValue = curl_exec($ch);
$retValue = json_decode($retValue);
 curl_close($ch);
$statusKey = $retValue->data->import_id;
 /*Get products status From Pim api*/
$url = 'https://pim-app-dev.unbxd.io/pim/v1/imports/'.$statusKey.'/status';
 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
  curl_setopt($ch, CURLOPT_POSTFIELDS, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                     "Authorization: $api_key",
					 'Content-Type: application/json'
                 )
   );
 curl_setopt($ch, CURLOPT_POSTFIELDS,true);
   $ResponsePim = curl_exec($ch);
  curl_close($ch);
$ResultPim = json_decode($ResponsePim);
$curntdate = date('Y-m-d H:i:s');
if ($statusKey) {
/*if imported data insert into database*/
date_default_timezone_set('Asia/Kolkata');
 $curntdate = date('Y-m-d h:i:s');
 $Insertimportdata= "INSERT INTO export_to_pim (	user_id,status_key,created)
 VALUES ('$Store_Hash','$statusKey','$curntdate')";
 if ($conn->query($Insertimportdata) === TRUE) {
  }	
  /*
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
	}*/


	
	
}
else{ 
/*if($api_check == 1 ) {
 ?>
 <div id="opn-pop">
<div class="container" id="model-popup">
  <div class="modal fade in" id="myModal" role="dialog">
    <div class="modal">
      <div class="modal-content">
        <div class="modal-header">
       <h2> Products Not Imported !!! </h2>
        </div>
        <div class="modal-body">
		 <p>Your Produts Not Imported To Pim Something Went Wrong.</p>
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
		$result_return=array("result"=>"Your produts Not Imported to Pim");
		header('Content-Type: application/json');
		echo json_encode($result_return);
	}*/
	?>
<?php }



}

}
}
	
?>