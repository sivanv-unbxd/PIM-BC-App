<?php
error_reporting(0);

include("db_config.php");
require 'vendor/autoload.php';
use Bigcommerce\Api\Client as Bigcommerce;
 $Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
$api_check = $_GET['api_check'];



 //Check request is coming from API or Bigcommerce Panel
 $AllJsonData=array();
if($api_check==1)
	
{
include("header.php"); 
 $key= $_REQUEST['api_key']; 
}else {
 $data = json_decode(file_get_contents('php://input'), true);

$key=$data['api_key'];
}
 $total1=$_GET['total'];
 $page=$_GET['page'];
 $last_id =$_GET['lastid'];
$total_pages=$_GET['total_pages'];

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
	
}
else {

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

$root_path=$_SERVER['DOCUMENT_ROOT'].'/api/v1/bigtopim/'.$store_hash;
if (!file_exists($root_path)) {
    mkdir($root_path, 0777, true);
}

// Returns array of files
//$files = scandir($root_path);
//echo $num_files = count($files)-2;
/* Directory Name of the files */


//echo "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/products?page=".$page."&limit=".$total1;

/*get bigcommerce products */
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/products?page=".$page."&limit=".$total1."",
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
$all_product_data = json_decode($response,true);




$myArray = json_decode(json_encode($all_product_data), true);




      $file_json_path=$root_path.'/'.$store_hash.'_'.$page.'.json';
 $fp = fopen($file_json_path, 'w');

   foreach ($myArray['data'] as $val) {	 
   /* Big categories*/


   $BigCategory =  $val['categories'];

foreach ($BigCategory as $BigCategoryval) {
		$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$Store_Hash."/v3/catalog/categories/".$BigCategoryval,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
   "x-auth-client: $client_id",
    "x-auth-token: $oauth_token"
  ),
));
$responsecats = curl_exec($curl);
curl_close($curl);
$responsecats = json_decode($responsecats);
$responsecats = json_decode(json_encode($responsecats->data), true);
$BigcatNmes[] = $responsecats['name'] ;
} 
/*Get Brand Name*/
$BrandID = $val['brand_id'];
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$Store_Hash."/v3/catalog/brands/".$BrandID,
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
/* Get Custom Fields*/
$ProductId = $val['id'];
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$Store_Hash."/v3/catalog/products/".$ProductId."/custom-fields",
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
$responseCustomField = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
$responseCustomField = json_decode($responseCustomField);
$responseCustomField = json_decode(json_encode($responseCustomField->data), true);
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
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$Store_Hash."/v3/catalog/products/".$ProductId."/images",
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
$responseImages = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
$responseImages = json_decode($responseImages);
$responseImages = json_decode(json_encode($responseImages->data), true);
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
		//$data['Meta Keywords']=$val['meta_keywords'];
		$data['Meta Keywords']= implode(",",$val['meta_keywords']);
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
	
	
//print_R($data);

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
 
 echo $final;
 fwrite($fp, $final);
	    
	
	fclose($fp); 
	if($page==$total_pages)	
	{
		
		echo 'test';
	$dir = $root_path;
/* Scan the files in the directory */
$files = scandir ($dir);
/* Loop through the files, read content of the files and put then OutFilename.txt */
$outputFile =$root_path.'/final.json';
file_put_contents($outputFile, "");
foreach ($files as $file) {
    if ($file !== "." OR $file != "..") {
        file_put_contents ($outputFile, file_get_contents ($dir."/".$file),  FILE_APPEND);
    }
}
file_put_contents("filelist.txt", "");
$file_path= $outputFile;
// Open the file to get existing content
$current = file_get_contents($file_path);
// Append a new person to the file
$removeextrastring =  str_replace("][",",",$current);
//$fp = fopen('results1.json', 'w');


 //fwrite($fp, $removeextrastring );
	    
	
	//fclose($fp);
$updatedfile =$store_hash.'.json';

// Write the contents back to the file
file_put_contents($updatedfile, $removeextrastring );
	
		
		
		
$zip = new ZipArchive;
if ($zip->open('ImportToPim.zip', ZipArchive::CREATE) === TRUE)
{
    // Add random.txt file to zip and rename it to newfile.txt
   $zip->addFile($updatedfile);
    // Add a file new.txt file to zip using the text specified
    // All files are added, so close the zip file.
    $zip->close();
}
// Close the file
/* Prduct Import Api To Pim */
$url ='https://pim-app-dev.unbxd.io/pim/v1/imports';
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


}}
	

?>