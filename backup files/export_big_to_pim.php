<?php
error_reporting(0);
 $Store_Hash = $_GET['id'];
 include("db_config.php");
 include("header.php"); 
 require 'vendor/autoload.php';
  //echo $path = $_SERVER['DOCUMENT_ROOT'].'/bigcomerce/products.zip';
  use Bigcommerce\Api\Client as Bigcommerce;
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
	$api_key = $row[0]['api_key'];
	$client_id = $row[0]['client_id'];
	$client_secret = $row[0]['client_secret'];
	$oauth_token = $row[0]['oauth_token'];
	$path = $row[0]['path'];         
	 $org_key = $row[0]['org_key']; 

 
   Bigcommerce::configure(array(
        'store_url' => '"'.$path.'"',
        'username' => '"'.$user_email.'"',
        'api_key' => '"'.$api_key.'"'
    ));
	}
}

/*get bigcommerce products */
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/products",
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

$php_array = json_decode($response,true);
//echo "<pre>";print_r($php_array['meta']['pagination']);
//echo $php_array['meta']['pagination']->total;
$totalproduct_check=$php_array['meta']['pagination']['total'];
if($totalproduct_check==0)
{?>
	<div id="opn-pop">
<div class="container" id="model-popup">
  <div class="modal fade in" id="myModal" role="dialog">
    <div class="modal">
        
      <div class="modal-content">
        <div class="modal-header">
      
        </div>
        <div class="modal-body">
		 <p>Sorry There is no product to import for now.</p>
             </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" id="close" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
</div>
	
<?php	
}else {
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
 // echo $response;
}
$all_product_data[] = json_decode($response);
   if ($all_product_data->status == "401") { ?>
 <div id="opn-pop">
<div class="container" id="model-popup">
  <div class="modal fade in" id="myModal" role="dialog">
    <div class="modal">
        <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
        <p>Invalid Api Keys, Or might be you have deleted api account , So put vaild Api keys .</p>
		
		   <a href="update_api.php?id=<?php echo $store_hash;?>" id="import" >Proceed</a>
        </div>
        <div class="modal-body">
             </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" id="close" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
</div>

<?php 

 }


$myArray = json_decode(json_encode($all_product_data), true);
 $fp = fopen('results.json', 'w');
   foreach ($myArray[0]['data'] as $val) {	 
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

$data['Item Type']=$val['open_graph_type'];
$data['Parent ID']="";
$data['Product ID']=$val['id'];
$data['Product Name']=$val['name'];
$data['Product Type']=$val['physical'];
$data['Product Code/SKU']=$val['sku'];
$data['Brand Name']=$responsebrand['name'];
$data['Option Set']=$val['option_set_id'];
$data['Option Set Align']=$val['option_set_display'];
$data['Price']=$val['price'];
$data['Cost Price']=$val['cost_price'];
$data['Retail Price']=$val['retail_price'];
$data['Sale Price']=$val['sale_price'];
$data['Fixed Shipping Cost']=$val['fixed_cost_shipping_price'];
$data['Free Shipping']=$val['is_free_shipping'];
$data['Product Warranty']=$val['warranty'];
$data['Product Weight']=$val['weight'];
$data['Product Width']=$val['width'];
$data['Product Height']=$val['height'];
$data['Product Depth']=$val['depth'];
$data['Allow Purchases?']="";
$data['Product Visible?']=$val['is_visible'];
$data['Product Availability']=$val['availability'];
$data['Track Inventory']=$val['inventory_tracking'];
$data['Current Stock Level']=$val['inventory_level'];
$data['Low Stock Level']=$val['inventory_warning_level'];

$BigcatNmesVal = implode(",",$BigcatNmes);
$data['Category']=[$BigcatNmesVal];
$BigcatNmes = array();
$data['Product Image ID - 1']="1";
$data['Product Image File - 1']="https://myvirtualpartner.net/bigcomerce/images/progress.png";
$data['Product Image Description - 1']="";
$data['Product Image Is Thumbnail - 1']="Y";
$data['Product Image Sort - 1']="";
$data['Product Image ID - 2']="";
$data['Product Image File - 2']="";
$data['Product Image Description - 2']="";
$data['Product Image Is Thumbnail - 2']="";
$data['Product Image Sort - 2']="";
$data['Product Image ID - 3']="";
$data['Product Image File - 3']="";
$data['Product Image Description - 3']="";
$data['Product Image Is Thumbnail - 3']="";
$data['Product Image Sort - 3']="";
$data['Product Image ID - 4']="";
$data['Product Image File - 4']="";
$data['Product Image Description - 4']="";
$data['Product Image Is Thumbnail - 4']="";
$data['Product Image Sort - 4']="";
$data['Product Image ID - 5']="";
$data['Product Image File - 5']="";
$data['Product Image Description - 5']="";
$data['Product Image Is Thumbnail - 5']="";
$data['Product Image Sort - 5']="";
$data['Product Image ID - 6']="";
$data['Product Image File - 6']="";
$data['Product Image Description - 6']="";
$data['Product Image Is Thumbnail - 6']="";
$data['Product Image Sort - 6']="";
$data['Product Image ID - 7']="";
$data['Product Image File - 7']="";
$data['Product Image Description - 7']="";
$data['Product Image Is Thumbnail - 7']="";
$data['Product Image Sort - 7']="";
$data['Product Image ID - 8']="";
$data['Product Image File - 8']="";
$data['Product Image Description - 8']="";
$data['Product Image Is Thumbnail - 8']="";
$data['Product Image Sort - 8']="";
$data['Product Image ID - 9']="";
$data['Product Image File - 9']="";
$data['Product Image Description - 9']="";
$data['Product Image Is Thumbnail - 9']="";
$data['Product Image Sort - 9']="";
$data['Product Image ID - 10']="";
$data['Product Image File - 10']="";
$data['Product Image Description - 10']="";
$data['Product Image Is Thumbnail - 10']="";
$data['Product Image Sort - 10']="";
$data['Search Keywords']=$val['search_keywords'];
$data['Page Title']=$val['page_title'];
$data['Meta Keywords']=$val['meta_keywords'];
$data['Meta Description']=$val['meta_description'];
$data['Product Condition']=$val['condition'];
$data['Show Product Condition?']=$val['is_condition_shown'];
$data['Event Date Required?']="";
$data['Event Date Name']="";
$data['Event Date Is Limited?']="";
$data['Event Date Start Date']="";
$data['Event Date End Date']="";
$data['Sort Order']=$val['sort_order'];
$data['Product Tax Class']=$val['tax_class_id'];
$data['Product UPC/EAN']=$val['upc'];
$data['Stop Processing Rules']="";
$data['Product URL']=$val['custom_url']['url'];
$data['Redirect Old URL?']=$val['custom_url']['is_customized'];
$data['GPS Global Trade Item Number']=$val['gtin'];
$data['GPS Manufacturer Part Number']=$val['mpn'];
$data['GPS Gender']="";
$data['GPS Age Group']="";
$data['GPS Color']="";
$data['GPS Material']="";
$data['GPS Pattern']="";
$data['GPS Item Group ID']="";
$data['GPS Category']="";
$data['GPS Enabled']="";
$data['Avalara Product Tax Code']=$val['product_tax_code'];
foreach ($responseCustomField as $responseCustomval ) {
	$name =  $responseCustomval['name'];
	$value =  $responseCustomval['value'];
	$data[$name]=$value;
			}
		$AllJsonData[] = $data;
		 $data = array();
      }
	
	  
	  fwrite($fp, json_encode($AllJsonData));
		
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
$url ='https://pim-app-dev.unbxd.io/api/v1/stores/imports';
$header = array('Authorization: 5ccbeb6ebc1413682ea46c3c','Content-Type: application/json', 
'cache-control: no-cache');
$ch = curl_init();
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
 $body ='{
"url": "https://myvirtualpartner.net/bigcomerce/ImportToPim.zip"
}';
 curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
$retValue = curl_exec($ch);
$retValue = json_decode($retValue);
 curl_close($ch);

$statusKey = $retValue->data->import_id;


 /*Get products status From Pim api*/
$url = 'https://pim-app-dev.unbxd.io/api/v1/stores/imports/'.$statusKey.'/status';
 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
  curl_setopt($ch, CURLOPT_POSTFIELDS, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                     'Authorization: 5ccbeb6ebc1413682ea46c3c',
					 //"Authorization: $org_key",
					 'Content-Type: application/json'
                 )
   );
 curl_setopt($ch, CURLOPT_POSTFIELDS,true);
   $ResponsePim = curl_exec($ch);
  curl_close($ch);
$ResultPim = json_decode($ResponsePim);

$curntdate = date('Y-m-d H:i:s');
if ($statusKey) { ?>
<div id="opn-pop">
<div class="container" id="model-popup">
  <div class="modal fade in" id="myModal" role="dialog">
    <div class="modal">
      
      <div class="modal-content">
        <div class="modal-header">
       <h2> Product Exported to pim </h2>
      
        </div>
        <div class="modal-body">

             </div>
        <div class="modal-footer">
        <a  href="import.php?id=<?php echo  $Store_Hash;?>&exp=1" class="btn btn-default" id="close" data-dismiss="modal">Close</a>
        </div>
      </div>
      
    </div>
  </div>
  
</div>
</div>
	<?php
	$AllJsonData=array();
	$BigcatNmes=array();
}
/*
$InsertProData= "INSERT INTO pim_products (	import_id,bg_store_hash,pim_user_id,status, created)
VALUES ('$statusKey','$Store_Hash','$org_key_val','', '$curntdate')";
if ($conn->query($InsertProData) === TRUE) {
    echo "Product Data added In database";
} 
else {
	echo $InsertProData;
}*/
}
	
?>