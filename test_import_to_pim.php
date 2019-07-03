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
		//api.bigcommerce.com/stores/37kiq2b0u6/v3/catalog/products?include=variants%2Cimages%2Ccustom_fields%2Cbulk_pricing_rules%2Cprimary_image%2Cmodifiers%2Coptions%2Cvideos
		//echo "https://api.bigcommerce.com/stores/".$Store_Hash."/v3/catalog/categories";
		$BigcatNames=array();
		$categoriesval=array();
		$variation_push=array();
		$datafin=array();
		$curl = curl_init();
		$finaldatamerge=array();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$Store_Hash."/v3/catalog/categories",
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
		//print_r($responsecats);
		
		foreach ($responsecats as $responsecatsval){
			//$CatId = $responsecatsval['id'];
			$BigcatNames[$responsecatsval['id']] = $responsecatsval['name'];
		}
		//print_r($BigcatNames);

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


		$curl = curl_init();
		curl_setopt_array($curl, array(
		  // CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/products?include=variants%2Cimages%2Ccustom_fields%2Cbulk_pricing_rules%2Cprimary_image%2Cmodifiers%2Coptions&page=".$page."&limit=".$total1,
		  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$store_hash."/v3/catalog/products?limit=".$total1."&page=".$page."&include=variants%2C%20images%2C%20custom_fields%2C%20bulk_pricing_rules%2C%20primary_image%2C%20modifiers%2C%20options%2C%20videos",
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
	// echo '<pre>';
		
 // print_r($all_product_data);
	// die('dd');
		$myArray = json_decode(json_encode($all_product_data), true);
		$data=array();
		
		$option=array();
		//print_r($myArray);
		//print_r($all_product_data[variants]);
		//print_r($all_product_data['data']['variants']);

		foreach($myArray['data'] as $val)
		{
			 $ProductId=$val['id'];
			$paren['Parentid']=$val['sku'];
			
			
			//unset($val->variants);
			///print_r($val['variants']);
			
			if(!empty($val['variants']) && count($val['variants'])>=0)
			{
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
	// echo '<pre>';
		// print_r($variantresponse['data']);
		// die('dd');
		foreach ($variantresponse['data'] as $variantresponseVal) {
		$optionValue = $variantresponseVal['option_values'];
		//throw in another 'strawberry' to demonstrate that it removes multiple instances of the string
		$array_without_strawberries = array_diff($variantresponseVal, $optionValue);
		foreach ($optionValue as $optionValueVal) {
			
			 $option_display_name=$optionValueVal['option_display_name'];
			$vallabel=$optionValueVal['label'];
			$datafin[$option_display_name]=$vallabel;
			//print_R($datafin);
			//$optionValueVal['option_display_name']=$optionValueVal['label'];
			//$addoption=
			
		$arr= array_merge($datafin,$array_without_strawberries,$paren);
				
		array_push($variation_push,$arr);
		
		$arr=array();
		$datafin=array();
			}
			}
			/*echo '<pre>';
			print_r($variation_push);
			die('ddd');*/
			}
			
					 $xxx = count($variation_push);
			 for($i=0; $i <= $xxx; $i++){
			//print_r($variation_push[$i]);
		
			
			
			//print_r($val);
				 //die('ff');
				 unset($val['variants']);
				 unset($val['options']);
				 //$variant['variants']=$variation_push;
			$mergevariations = array_merge($val,$variation_push[$i]);
			
	
			
				
			foreach($val['categories'] as $catval)
			{
				// $catval;
			 $getcatval=$BigcatNames[$catval];
				
				array_push($categoriesval,$getcatval); 
			}
			 unset($mergevariations['categories']);
			// array_push($categories['categoriesname'],$categoriesval);
			$ctegories['catname']=$categoriesval;
			 $mergecategories = array_merge($mergevariations,$ctegories);
			
			 //echo '<pre>';
			  $descrip['description23']=htmlspecialchars($mergecategories['description']);
			 unset($mergecategories['description']);
			 $mergecategories = array_merge($mergecategories,$descrip);
			array_push($finaldatamerge,$mergecategories);
	$arr=array();
	$mergecategories=array();
		}
		}
			}
		echo '<pre>';
		print_r($finaldatamerge);
	
 echo json_encode($finaldatamerge,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);


//echo $cars = json_encode($finaldatamerge );
		die('ddd');
		 // print_r($option);
		 // //sprint_R($val);
		// print_r($val[variants]->option_values);


		// // unset($val[variants][option_values]);
		 
		 // if(!empty($val[variants]) && count($val[variants])>=2 )
			 
		 // //unset($val[variants]);
		 
		// unset($val[variants][option_values]);
		 
		 
		 // foreach($val[variants] as $variant)
		 // {
			 // echo $variant['option_values'];
			// array_merge($val[variants],$variant['option_values']);
			 
			 
			 
			 

		 //print_R($val[variants]);
		 
		// $responseCustomField = curl_exec($curl);
		// $err = curl_error($curl);
		// curl_close($curl);
		// $responseCustomField = json_decode($responseCustomField);
		// $responseCustomField = json_decode(json_encode($responseCustomField->data), true);
		// /*get variants of products*/
		// $curl = curl_init();
		// curl_setopt_array($curl, array(
		  // CURLOPT_URL => "https://api.bigcommerce.com/stores/".$Store_Hash."/v3/catalog/products/".$ProductId."/variants",
		  // CURLOPT_RETURNTRANSFER => true,
		  // CURLOPT_ENCODING => "",
		  // CURLOPT_MAXREDIRS => 10,
		  // CURLOPT_TIMEOUT => 30,
		  // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  // CURLOPT_CUSTOMREQUEST => "GET",
		  // CURLOPT_HTTPHEADER => array(
			// "accept: application/json",
			// "content-type: application/json",
			 // "x-auth-client: $client_id",
			// "x-auth-token: $oauth_token"
		  // ),
		// ));
		// $variantresponse = curl_exec($curl);
		// curl_close($curl);
		// $variantresponse = json_decode($variantresponse); 
		// $variantresponse = json_decode(json_encode($variantresponse), true);
		// $final=array();
		// $GetValArr=array();



		// //print_r($variantresponse['data']);
		// foreach ($variantresponse['data'] as $variantresponseVal) {

		// $Variantsku=array("SKU"=>$variantresponseVal['sku']);
		// $productsku=array("parentId"=>$val['sku']);
		// //$VariantparentId=array("parentId"=>$variantresponseVal['product_id']);
		 
		 // $options_values=$variantresponseVal['option_values'];
		 // if(empty($variantresponseVal['price']) || $variantresponseVal['price']=='' || $variantresponseVal['price']=='null')
			// {
				
			// } else{ $price = array("price"=>$variantresponseVal['price']);
			// }
		 
		 



		?>