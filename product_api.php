<?php


  error_reporting(0);
   
   $Store_Hash = $_REQUEST['id'];
   $import = $_REQUEST['import'];
      $api_check = $_REQUEST['api_check'];
 include("db_config.php");

 include("header.php"); 
 require ("vendor/autoload.php");

 if(empty($Store_Hash))
 {
	$Store_Hash=$Store_Hash;
	
 } else{
	 $Store_Hash=$Store_Hash;
 }

 $total=$_GET['total'];
 $number=$_GET['page'];
 $last_id =$_GET['lastid'];

function checknumeric($data)
{
	if($data=='null')
	{
	$data=1;	
	}	
	else if(is_numeric($ProductWidth))
	{
		$data=$data;
		
		
	} else {
		
		$data=1;
	}
	
	return $data;
	
}

function substrwords($text, $maxchar, $end='') {
    if (strlen($text) > $maxchar || $text == '') {
        $words = preg_split('/\s/', $text);      
        $output = '';
        $i      = 0;
        while (1) {
            $length = strlen($output)+strlen($words[$i]);
            if ($length > $maxchar) {
                break;
            } 
            else {
                $output .= " " . $words[$i];
                ++$i;
            }
        }
        $output .= $end;
    } 
    else {
        $output = $text;
    }
    return $output;
}
 use Bigcommerce\Api\Client as Bigcommerce;

 /* Get the data from local table to get the api key for current BG accont */
/*$sql = "SELECT auth.* ,  bc_api_data.* ,
pim_users.* FROM   auth , bc_api_data ,pim_users  WHERE  auth.context=bc_api_data.store_hash and auth.context = pim_users.bc_store_hash ";
*/


$checkstorehash="SELECT * from bc_api_data where store_hash='".$Store_Hash."'";
$checkstorehash = $conn->query($checkstorehash);
if($checkstorehash->num_rows > 0)
{




$sql = "SELECT  bc_api_data.* ,
pim_users.* FROM   bc_api_data ,pim_users  WHERE  bc_api_data.store_hash = '". $Store_Hash ."' and pim_users.bc_store_hash = '". $Store_Hash ."' ";
$resultAll = $conn->query($sql);
if ($resultAll->num_rows > 0) {
    while($row[] = $resultAll->fetch_assoc()) {
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


/* --- get all products in  BC--- */
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
$err = curl_error($curl);
curl_close($curl);

$getallproducts = json_decode($response);

// echo '<pre>';
// print_r($getallproducts);
// echo '</pre>';


foreach($getallproducts->data as $productsall)

{
	$pid=$productsall->id;
	$bigcompd[$pid]=$productsall->sku;
}

// print_r($bigcompd);

// echo 'dddd';


/* --- get all categories api BC  --- */

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/categories",
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
$err = curl_error($curl);
curl_close($curl);
$getcategories = json_decode($response);
foreach ($getcategories->data as $catname) {
	/*echo "<pre>";
	print_r ($catname);
	echo "<pre>";*/
	$BigCategoryParentid[]=$catname->parent_id;
	$BigCategory[]=$catname->name;
		}
	/* --- get brand api  BC--- */
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/brands",
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
$err = curl_error($curl);
curl_close($curl);
$Brands = json_decode($response);
foreach ($Brands->data as $brandname) {
$Bigbrand[]=$brandname->name; 
}
	
/*Getting all products From Pim using api*/
$url = 'https://pim-app-dev.unbxd.io/pim/v1/products';
 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                     //'Authorization: 5ccbeb6ebc1413682ea46c3c',
					 "Authorization: $api_key",
					 'Content-Type: application/json',
                 'Cache-Control: no-cache' )
   );
   $body = '{ "page": '.$number.',
   "count": '.$total.'
 
   }'; 
 curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
   $ResponsePim = curl_exec($ch);
  curl_close($ch);
$ResultPim = json_decode($ResponsePim);



/*Total no. of products in PIM*/

 $totalcount = $ResultPim->data->total;
foreach($ResultPim as $result_val) {

	$productdata[]=$result_val->products;
	}






function checkarray($arg,$strip=false,$last,$shinpping=false){
	if(!empty($shinpping)) {
	if (is_array($arg)){
if ( $arg[0] == "Y"){
return "true"; 	}
	else {
	return "false"; }	
	}
	else{
		if ( $arg == "Y") {
		return "true"; 	}
	else {
	return "false"; }	
	}
	}
	else {
	if(!empty($arg)) {
	if (is_array($arg)){
 $ProductWeight = str_replace($strip,'',$arg[0]);
	}
	else {
$ProductWeight = str_replace($strip,'',$arg);
	}
	}
	else {
	  $ProductWeight = $last;
		}
		return is_array($ProductWeight) ? $ProductWeight : $ProductWeight;
	}
	}
	/*final fileds required to create big commerce product*/
	  $main=array('Product ID','sku','preorder_message','Product Type','name','Fixed Shipping Cost','Category (PIM)','Sale Price','Retail Price','cost_price','Product Weight','Price','Product UPC/EAN','width','Brand Name','height','Current Stock Level','depth','open_graph_description','open_graph_use_image','Allow Purchases?','Product Tax Class','Show Product Condition?','Product Condition','GPS Manufacturer Part Number','Track Inventory','Product Image ID - 1','Product Image File - 1','Product Image Description - 1','Product Image ID - 2','Product Image File - 2','Product Image Description - 2','Product Image ID - 3','Product Image File - 3','Product Image Description - 3','Product Image ID - 4','Product Image File - 4','Product Image Description - 4','Product Image ID - 5','Product Image File - 5','Product Image Description - 5','Product Image ID - 6','Product Image File - 6','Product Image Description - 6','Product Image ID - 7','Product Image File - 7','Product Image Description - 7','Product Image ID - 8','Product Image File - 8','Product Image Description - 8','Product Image ID - 9','Product Image File - 9','Product Image Description - 9','Product Image ID - 10','Product Image File - 10','Product Image Description - 10','Sort Order','Meta Keywords','Item Type','Product Availability','Search Keywords','Option Set Align','Avalara Product Tax Code','Product Visible?','Page Title','meta_description','Option Set','Product URL','Meta Description','Low Stock Level','Product Warranty','Free Shipping');
	  




foreach($productdata[0] as  $result_product) {
//$os[] = array("pimProductType"=>"VARIANT");

$ifVariant  =  $result_product->{'pimProductType'};
if( $ifVariant =="VARIANT"){

$url = 'https://pim-app-dev.unbxd.io/pim/v1/properties';
 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                     //'Authorization: 5ccbeb6ebc1413682ea46c3c',
					 "Authorization: $api_key",
					 'Content-Type: application/json',
                 'Cache-Control: no-cache' )
   );
   $body = '{ "page": 1,
   "count": 100,
"sku": 581
    }'; 
 curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
   $ResponsePim = curl_exec($ch);
  curl_close($ch);
$ResultPim1 = json_decode($ResponsePim);
echo "<pre>";
print_r($ResultPim1);
echo "</pre>";

}
else{
echo "<pre>";
echo "without variant";
print_r($result_product);
echo "</pre>";
}

	//$pim_pro_id=array($pim_pro_id);
	$pim_pro_id = $result_product->{'Product ID'};
	
	

	
	//$ProductName = $result_product->{'Product Name'};
	
	$ProductName = $result_product->{'name'};
	$ProductName = preg_replace("/[^a-zA-Z0-9]+/", " ", html_entity_decode($ProductName, ENT_QUOTES));
	
	$ProductSku = $result_product->{'Product ID'};
	$ProductWeight = checkarray( $result_product->{'Product Weight'},'[FIXED]',0);
	$ProductPrice = checkarray($result_product->{'Price'},'[FIXED]',0);
	$ProductUpc = checkarray($result_product->{'Product UPC/EAN'},'',123456);
	$ProductWidth = checkarray($result_product->{'width'},'',0);
	$ProductWidth =checknumeric($ProductWidth);
	//$ProductWidth='fdf112';
	
	
	$ProductHeight = checkarray($result_product->{'height'},'',0);
	$ProductHeight =checknumeric($ProductHeight);
	$ProductDepth = checkarray($result_product->{'depth'},'',0);
		$ProductDepth =checknumeric($ProductDepth);
	$ProductDescription = checkarray($result_product->{'open_graph_description'},'',0);
	
	$ProductDescription=htmlentities($ProductDescription);
	//$ProductDescription='test';
	$ProductCost = checkarray($result_product->{'cost_price'},'',0);
	$ProductCost =checknumeric($ProductCost);
	$ProductRetailPrice = checkarray($result_product->{'Retail Price'},'',0);
	$ProductRetailPrice =checknumeric($ProductRetailPrice);
	$ProductSalePrice =checkarray($result_product->{'Sale Price'},'',0);
	$ProductSalePrice =checknumeric($ProductSalePrice);	
	$ProductStock =checkarray($result_product->{'Current Stock Level'},'',0);
	$Productmpn =checkarray($result_product->{'GPS Manufacturer Part Number'},'',0);
	$Productlowlevelstock =checkarray($result_product->{'Low Stock Level'},'',0);
	$ProductMetaDescription =checkarray($result_product->{'Meta Description'},'',0);
	$ProductWarranty =checkarray($result_product->{'Product Warranty'},'',0);
	$ProductTaxClass =checkarray($result_product->{'Product Tax Class'},'',0);
	$ProductUrl =checkarray($result_product->{'Product URL'},'',0);
	$ProductTaxCode =checkarray($result_product->{'Avalara Product Tax Code'},'',0);
	$Productis_visible = checkarray($result_product->{'Product Visible?'},'','',1);
	$Productis_free_shipping = checkarray($result_product->{'Free Shipping'},'','',1);
	$ProductShowCondition = checkarray($result_product->{'Show Product Condition?'},'','',1);
	$ProductAllowPurchases = checkarray($result_product->{'Allow Purchases?'},'','',1);
	$ProductType = $result_product->{'Product Type'};
	$ProductAvail = $result_product->{'Product Availability'};
 if(!empty($ProductAvail)) {
	if (is_array($ProductAvail)){
 if( $ProductAvail[0] == "Y") 
  { $ProductAvail = "available";} else {$ProductAvail = ""; }
  }
 elseif($ProductAvail== "Y"){ $ProductAvail = "true" ; } else { $ProductAvail= "false";  }
}
 else{ $ProductAvail = ""; }
	$ProductCondition =$result_product->{'Product Condition'};
	$ProductTitle =checkarray($result_product->{'Page Title'},'',0);
	$ProductItemType =checkarray($result_product->{'Item Type'},'',0);
	$ProductFixedShippingCost =checkarray($result_product->{'Fixed Shipping Cost'},'',0);
	$ProductOptionSetAlign =checkarray($result_product->{'Option Set Align'},'',0);
	$ProductTrackInventory =checkarray($result_product->{'Track Inventory'},'',0);
	$ProductSearchKeywords =checkarray($result_product->{'Search Keywords'},'',0);
/* 	$ProductMetaKeywords =checkarray($result_product->{'Meta Keywords'},'',0);
 if( empty($ProductMetaKeywords)) {
	$ProductMetaKeywords =" ";
} */
	$ProductSortOrder =checkarray($result_product->{'Sort Order'},'',0);
	$ProductImg1 =checkarray($result_product->{'Product Image File - 1'},'',0);
	$ProductImg2 =checkarray($result_product->{'Product Image File - 2'},'',0);
	$ProductImg3 =checkarray($result_product->{'Product Image File - 3'},'',0);
	$ProductImg4 =checkarray($result_product->{'Product Image File - 4'},'',0);
	$ProductImg5 =checkarray($result_product->{'Product Image File - 5'},'',0);
	$ProductImg6 =checkarray($result_product->{'Product Image File - 6'},'',0);
	$ProductImg7 =checkarray($result_product->{'Product Image File - 7'},'',0);
	$ProductImg8 =checkarray($result_product->{'Product Image File - 8'},'',0);
	$ProductImg9 =checkarray($result_product->{'Product Image File - 9'},'',0);
	$ProductImg10 =checkarray($result_product->{'Product Image File - 10'},'',0);
	$ProductimgDescription1 =checkarray($result_product->{'Product Image Description - 1'},'',0);
	$ProductimgDescription2 =checkarray($result_product->{'Product Image Description - 2'},'',0);
	$ProductimgDescription3 =checkarray($result_product->{'Product Image Description - 3'},'',0);
	$ProductimgDescription4 =checkarray($result_product->{'Product Image Description - 4'},'',0);
	$ProductimgDescription5 =checkarray($result_product->{'Product Image Description - 5'},'',0);
	$ProductimgDescription6 =checkarray($result_product->{'Product Image Description - 6'},'',0);
	$ProductimgDescription7 =checkarray($result_product->{'Product Image Description - 7'},'',0);
$ProductimgDescription8 =checkarray($result_product->{'Product Image Description - 8'},'',0);
$ProductimgDescription9 =checkarray($result_product->{'Product Image Description - 9'},'',0);
$ProductimgDescription10 =checkarray($result_product->{'Product Image Description - 10'},'',0);
$ProductimgThubnail1 =checkarray($result_product->{'Product Image Is Thumbnail - 1'},'',0);
if ($ProductimgThubnail1 =="1"){$ProductimgThubnail1  = "true"; } else{ $ProductimgThubnail1  = "false";  }
	$ProductimgThubnail2 =checkarray($result_product->{'Product Image Is Thumbnail - 2'},'',0);
	if ($ProductimgThubnail2 =="1"){$ProductimgThubnail2  = "true" ;} else{$ProductimgThubnail2  = "false";  }
	$ProductimgThubnail3 =checkarray($result_product->{'Product Image Is Thumbnail - 3'},'',0);
	if ($ProductimgThubnail3 =="1"){$ProductimgThubnail3  = "true"; } else{$ProductimgThubnail3  = "false";  }
	$ProductimgThubnail4 =checkarray($result_product->{'Product Image Is Thumbnail - 4'},'',0);
	if ($ProductimgThubnail4 =="1"){$ProductimgThubnail4  = "true"; } else{$ProductimgThubnail4  = "false";  }
	$ProductimgThubnail5 =checkarray($result_product->{'Product Image Is Thumbnail - 5'},'',0);
	if ($ProductimgThubnail5 =="1"){$ProductimgThubnail5  = "true"; } else{$ProductimgThubnail5  = "false";  }
	$ProductimgThubnail6 =checkarray($result_product->{'Product Image Is Thumbnail - 6'},'',0);
	if ($ProductimgThubnail6 =="1"){$ProductimgThubnail6  = "true" ;} else{$ProductimgThubnail6  = "false";  }
	$ProductimgThubnail7 =checkarray($result_product->{'Product Image Is Thumbnail - 7'},'',0);
	if ($ProductimgThubnail7 =="1"){$ProductimgThubnail7  = "true"; } else{$ProductimgThubnail7  = "false";  }
$ProductimgThubnail8 =checkarray($result_product->{'Product Image Is Thumbnail - 8'},'',0);
if ($ProductimgThubnail8 =="1"){$ProductimgThubnail8  = "true"; } else{$ProductimgThubnail8  = "false";  }
$ProductimgThubnail9 =checkarray($result_product->{'Product Image Is Thumbnail - 9'},'',0);
if ($ProductimgThubnail9 =="1"){$ProductimgThubnail9  = "true" ;} else{$ProductimgThubnail9  = "false" ; }
$ProductimgThubnail10 =checkarray($result_product->{'Product Image Is Thumbnail - 10'},'',0);
if ($ProductimgThubnail10 =="1"){$ProductimgThubnail10  = "true"; } else{$ProductimgThubnail10  = "false";  }
 $PimCategory =$result_product->{'Category (PIM)'};
$ProductBrandName = $result_product->{'Brand Name'};
/*$Productimgs = array("Img1" => $ProductImg1,"Img2" => $ProductImg2,"Img3" =>$ProductImg3,"Img4" =>$ProductImg4,"Img5" =>$ProductImg5,"Img6" =>$ProductImg6,"Img7" =>$ProductImg7,"Img8" =>$ProductImg8,"Img9" =>$ProductImg9,"Img10" =>$ProductImg10);
$ProductimgDescriptions = array("Img1" => $ProductimgDescription1,"Img2" => $ProductimgDescription2,"Img3" => $ProductimgDescription3,"Img4" => $ProductimgDescription4,"Img5" => $ProductimgDescription5,"Img6" => $ProductimgDescription6,"Img7" => $ProductimgDescription7,"Img8" => $ProductimgDescription8,"Img9" => $ProductimgDescription9,"Img10" => $ProductimgDescription10);
*/

/*
echo "<pre>";
echo "big";
print_r ($BigCategory );
echo "</pre>";*/

$arraymerge = array();
foreach($PimCategory as $PimCategoryval) {
$PimCategoryval = explode(">",$PimCategoryval);
$arayresult=array_merge($arraymerge,$PimCategoryval);
}
$CategoryMatched = array_intersect($BigCategory, $arayresult);


if (is_array($CategoryMatched) || is_object($CategoryMatched))
{
foreach ($CategoryMatched as $CategoryMatchedval) {
foreach ($getcategories->data as $catname) {
	if ($catname->name ==  $CategoryMatchedval ) {
			$catval[]=$catname->id;
}}
}
}
$catnewvall=implode(",",$catval);
$catval = array();

if (!empty($catnewvall )) {
	$catnewval = $catnewvall;
}
else{
	
	$catnewval ="23";
}

$BrandMatched = array_intersect($Bigbrand, $ProductBrandName);
if (is_array($BrandMatched) || is_object($BrandMatched))
{
foreach ($BrandMatched as $BrandMatchedval) {
foreach ($Brands->data as $brandname) {
if ($brandname->name ==  $BrandMatchedval ) {
 $brandval=$brandname->id;
}}
}
}
if (!empty($brandval )) {
	 $brandvalnewval = $brandval;
}
else{

$brandvalnewval = "37";
}

if(!empty($ProductImg1)) {
$images[]=array("image_url"=>"$ProductImg1","description"=>"$ProductimgDescription1","is_thumbnail"=>"$ProductimgThubnail1");
}
if(!empty($ProductImg2)) {
$images[]=array("image_url"=>"$ProductImg2","description"=>"$ProductimgDescription2","is_thumbnail"=>"$ProductimgThubnail2");
}
if(!empty($ProductImg3)) {
$images[]=array("image_url"=>"$ProductImg3","description"=>"$ProductimgDescription3","is_thumbnail"=>"$ProductimgThubnail4");
}
if(!empty($ProductImg4)) {
$images[]=array("image_url"=>"$ProductImg4","description"=>"$ProductimgDescription4","is_thumbnail"=>"$ProductimgThubnail4");
}
if(!empty($ProductImg5)) {
$images[]=array("image_url"=>"$ProductImg5","description"=>"$ProductimgDescription5","is_thumbnail"=>"$ProductimgThubnail5");
}
if(!empty($ProductImg6)) {
$images[]=array("image_url"=>"$ProductImg6","description"=>"$ProductimgDescription6","is_thumbnail"=>"$ProductimgThubnail6");
}
if(!empty($ProductImg7)) {
$images[]=array("image_url"=>"$ProductImg7","description"=>"$ProductimgDescription7","is_thumbnail"=>"$ProductimgThubnail7");
}
if(!empty($ProductImg8)) {
$images[]=array("image_url"=>"$ProductImg8","description"=>"$ProductimgDescription8","is_thumbnail"=>"$ProductimgThubnail8");
}
if(!empty($ProductImg9)) {
$images[]=array("image_url"=>"$ProductImg9","description"=>"$ProductimgDescription9","is_thumbnail"=>"$ProductimgThubnail9");
}
if(!empty($ProductImg10)) {
$images[]=array("image_url"=>"$ProductImg10","description"=>"$ProductimgDescription10","is_thumbnail"=>"$ProductimgThubnail10");
}

$img=count($images);
$images = json_encode($images);
/* custom  field*/
//$sliced_array = array_slice($main, 0, 4);

$i=0;
foreach( $result_product as $key=>$value) {
	
	$i++;
if(in_array($key,$main)){
	
if(is_array($value))
	
$value = $value[0];
else{
		$value = $value;
}
}
else{
		if(is_array($value)){
	$value = $value[0];
		}
else{
		$value = $value;
	}	
	/*echo "<pre>";
print_r ($value);
echo "</pre>";*/
	$value=strip_tags($value);
	$key=strip_tags($key);
	//echo $value = substr($value,0,240);
	$value=substrwords($value,240);
	
$keyname[]=array("name"=>"$key","value"=>"$value");

} 	  
}
$keyname = json_encode($keyname);


if($images=='null' || $images=='' || $img==0 || $images=='[]')
{
	$images = array();

	$images[]=array("image_url"=>"https://carolinadojo.com/wp-content/uploads/2017/04/default-image.jpg","description"=>"default","is_thumbnail"=>"");
	$images[]=array("image_url"=>"https://carolinadojo.com/wp-content/uploads/2017/04/default-image.jpg","description"=>"default","is_thumbnail"=>"");
	$images = json_encode($images);
}

/* --- create prdocts in Bc using API --- */

 // echo "{\"name\":\"$ProductName\",\"price\": $ProductPrice,\"cost_price\": $ProductCost,
// \"retail_price\": $ProductRetailPrice,\"sale_price\": $ProductSalePrice,
// \"categories\":[$catnewval],\"weight\": $ProductWeight,\"type\":\"physical\",
// \"sku\":\"$ProductSku\",\"description\":\"$ProductDescription\",\"width\":$ProductWidth,
// \"depth\":$ProductDepth,\"height\":$ProductHeight,\"brand_id\":$brandvalnewval,\"upc\":\"$ProductUpc\",
// \"mpn\":\"$Productmpn\",\"search_keywords\":\"$ProductSearchKeywords\", \"availability\":\"available\",
   // \"is_free_shipping\": $Productis_free_shipping,\"is_condition_shown\": $ProductShowCondition, \"is_visible\": $Productis_visible,\"product_tax_code\":\"$ProductTaxCode\",\"inventory_warning_level\": $Productlowlevelstock,\"inventory_level\": $ProductStock,\"fixed_cost_shipping_price\": $ProductFixedShippingCost,\"condition\": \"New\",\"page_title\":\"$ProductTitle\",\"meta_description\": \"$ProductMetaDescription\",\"images\":$images,\"custom_fields\":$keyname   }";
   
	$check=array_search($pim_pro_id,$bigcompd);
	
	if(!empty($bigcompd) && !empty($check))
	{
		
		

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/products/".$check,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "PUT",
     CURLOPT_POSTFIELDS => "{\"name\":\"$ProductName\",\"price\": $ProductPrice,\"cost_price\": $ProductCost,
\"retail_price\": $ProductRetailPrice,\"sale_price\": $ProductSalePrice,
\"categories\":[$catnewval],\"weight\": $ProductWeight,\"type\":\"physical\",
\"sku\":\"$ProductSku\",\"description\":\"$ProductDescription\",\"width\":$ProductWidth,
\"depth\":$ProductDepth,\"height\":$ProductHeight,\"brand_id\":$brandvalnewval,\"upc\":\"$ProductUpc\",
\"mpn\":\"$Productmpn\",\"search_keywords\":\"$ProductSearchKeywords\", \"availability\":\"available\",
   \"is_free_shipping\": $Productis_free_shipping,\"is_condition_shown\": $ProductShowCondition, \"is_visible\": $Productis_visible,\"product_tax_code\":\"$ProductTaxCode\",\"inventory_warning_level\": $Productlowlevelstock,\"inventory_level\": $ProductStock,\"fixed_cost_shipping_price\": $ProductFixedShippingCost,\"condition\": \"New\",\"page_title\":\"$ProductTitle\",\"meta_description\": \"$ProductMetaDescription\",\"images\":$images,\"custom_fields\":$keyname   }",
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "content-type: application/json",
   "x-auth-client: $client_id",
    "x-auth-token: $oauth_token"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
$all_product_data = json_decode($response);

   /*echo "<pre>";	
	print_r ($all_product_data );
	echo "</pre>";*/
/* all product data value */
$keyname=array();
 $all_product_data->status;
$check_val=$all_product_data->data->id;



$keyname = array();
 $images = array();

$curntdate = date('Y-m-d H:i:s');
if ($all_product_data->status == "409" || $all_product_data->status == "400" || $all_product_data->status == "401" || $all_product_data->status == "403" || $all_product_data->status == "404" || $all_product_data->status == "405" || $all_product_data->status == "406" || $all_product_data->status == "409" || 	$all_product_data->status == "413" || $all_product_data->status == "415" || $all_product_data->status == "422" || $all_product_data->status == "429" || $all_product_data->status == "301" || $all_product_data->status == "304" || $all_product_data->status == "503" || $all_product_data->status == "507" || $all_product_data->status == "501" || $all_product_data->status == "500") {
	$productstatus = "Fail";
	$failsdecription = $all_product_data->status."_".$all_product_data->title;
		
 }
else {
	$productstatus = "Updated";
	$failsdecription = "This Product is Updated";
}
		
		//echo $productstatus."<br>";
		//echo $failsdecription."<br>";
	}

	else  {
   
   
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/products",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
   CURLOPT_POSTFIELDS => "{\"name\":\"$ProductName\",\"price\": $ProductPrice,\"cost_price\": $ProductCost,
\"retail_price\": $ProductRetailPrice,\"sale_price\": $ProductSalePrice,
\"categories\":[$catnewval],\"weight\": $ProductWeight,\"type\":\"physical\",
\"sku\":\"$ProductSku\",\"description\":\"$ProductDescription\",\"width\":$ProductWidth,
\"depth\":$ProductDepth,\"height\":$ProductHeight,\"brand_id\":$brandvalnewval,\"upc\":\"$ProductUpc\",
\"mpn\":\"$Productmpn\",\"search_keywords\":\"$ProductSearchKeywords\", \"availability\":\"available\",
   \"is_free_shipping\": $Productis_free_shipping,\"is_condition_shown\": $ProductShowCondition, \"is_visible\": $Productis_visible,\"product_tax_code\":\"$ProductTaxCode\",\"inventory_warning_level\": $Productlowlevelstock,\"inventory_level\": $ProductStock,\"fixed_cost_shipping_price\": $ProductFixedShippingCost,\"condition\": \"New\",\"page_title\":\"$ProductTitle\",\"meta_description\": \"$ProductMetaDescription\",\"images\":$images,\"custom_fields\":$keyname   }",
   
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "content-type: application/json",
     "x-auth-client: $client_id",
    "x-auth-token: $oauth_token"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
$all_product_data = json_decode($response);



// echo '<pre>';

// print_r( $all_product_data);

// echo '</pre>';
/* all product data value */
$keyname=array();
 $all_product_data->status;
$check_val=$all_product_data->data->id;



$keyname = array();
 $images = array();

$curntdate = date('Y-m-d H:i:s');
if ($all_product_data->status == "409" || $all_product_data->status == "400" || $all_product_data->status == "401" || $all_product_data->status == "403" || $all_product_data->status == "404" || $all_product_data->status == "405" || $all_product_data->status == "406" || $all_product_data->status == "409" || 	$all_product_data->status == "413" || $all_product_data->status == "415" || $all_product_data->status == "422" || $all_product_data->status == "429" || $all_product_data->status == "301" || $all_product_data->status == "304" || $all_product_data->status == "503" || $all_product_data->status == "507" || $all_product_data->status == "501" || $all_product_data->status == "500") {
	$productstatus = "Fail";
	$failsdecription = $all_product_data->status."_".$all_product_data->title;
	
	
}
else {
	$productstatus = "Done";
	$failsdecription = "This Product is imported";
}
}
$product_id = $all_product_data->data->id;
//$SelectDatta ="SELECT * FROM products WHERE pim_pro_id='$pim_pro_id'";
$InsertProData= "INSERT INTO products (	pim_pro_id,pim_user_id,bg_store_hash,bc_pro_id, name, sku,price, status_description, status,import_id,created)
VALUES ('$pim_pro_id','$user_email','$store_hash','$product_id', '$ProductName', '$ProductSku','$ProductPrice', '$failsdecription', '$productstatus','$last_id','$curntdate')";
 $conn->query($InsertProData);
	

}

}

else  {
	
	
	$result_return=array("result"=>"Sorry store hash does not exist");
	
	echo json_encode($result_return); 
	
	
	
}
 $last_id='';
  include("footer.php"); 
 ?>