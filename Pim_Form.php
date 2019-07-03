<?php
ob_start();
include("header.php");
include("db_config.php");
$Store_Hash = $_GET['id'];

$Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
 $sql = "SELECT * FROM pim_users WHERE bc_store_hash='".$Store_Hash."'" ;
$resultget = $conn->query($sql);
if ($resultget->num_rows > 0) {
	$row = $resultget->fetch_assoc();
 $auth_token = $row['auth_token'];
	$bc_org_key = $row['org_key'];
	$api_key = $row['api_key'];
	if(!empty($api_key) ){
header("Location: ".$Site_url."/api/v1/import.php?id=".$Store_Hash);
	 }
}

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/".$Store_Hash."/v2/store",
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

$reslt_doamain = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
$reslt_doamain = json_decode($reslt_doamain);
$reslt_doamain = json_decode(json_encode($reslt_doamain), true);
$StoreName =  $reslt_doamain['domain'];


if($_POST['activate']=="Link/Activate") {
	 $org_key = $_POST['org_name'];
			/* register api*/
		 $url = "https://pim-app-dev.unbxd.io/pim/v1/register";
	 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Authorization: '.$auth_token,
 					 'Content-Type: application/json',
                 'Cache-Control: no-cache'	 )
   );
   $body = '{
 "app_custom_id": "BIG_COMMERCE_TEST",
   "org_key": "'.$org_key.'",
   "site_name": "'.$StoreName.'"
  }';
 curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
   $reslt_register = curl_exec($ch);
   $err = curl_error($ch);
curl_close($ch);
$reslt_register = json_decode($reslt_register);
$reslt_register = json_decode(json_encode($reslt_register), true);


  $get_api_key = $reslt_register['data']['apiKey'];
 $sqlupdate = "SELECT  * FROM  pim_users  WHERE  bc_store_hash = '". $Store_Hash ."' and org_key = '".$bc_org_key."'   ";
$resultupdate = $conn->query($sqlupdate);
if ($resultupdate->num_rows > 0) {
	$row1 = $resultupdate->fetch_assoc();
   $sqlupdate = "UPDATE pim_users SET api_key= '".$get_api_key."' , org_key='".$org_key."' where id=".$row1['id'];
if ($conn->query($sqlupdate) === TRUE) {
	//echo "redirected";
header("Location: ".$Site_url."/api/v1/import.php?id=".$Store_Hash);
} 
} 
}

if(isset($_POST['submit'])) {
 $email=$_POST['email'];
	 $psw=$_POST['psw'];
	 $api_key=$_POST['api_key'];
	  $Store_Hash=$_POST['store_hash'];
		 $date = date("Y-m-d h:i:s");
 /* Login Api for get org key */
$url = "https://pim-app-dev.unbxd.io/pim/v1/login";
	 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'accept: application/json',
 					 'Content-Type: application/json',
                 'Cache-Control: no-cache'	 )
   );
   $body = '{
 // "email": "apps@unbxd.com",
  //"password": "unbxd@123" 
  "email": "'.$email.'",
  "password": "'.$psw.'"
  }';
 curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
   $result = curl_exec($ch);
   $err = curl_error($ch);
curl_close($ch);
if ($err) {
  //echo "cURL Error #:" . $err;
}
$reslt = json_decode($result);

if($reslt->data->org_list){
  $bc_org_key = $reslt->data->org_list[0]->org_key;
 $bc_name = $reslt->data->org_list[0]->name;
}
else {
	 $bc_org_key = $reslt->data[0]->org_key;
 $bc_name = $reslt->data[0]->name;
}




/* Login Api */
$url = "https://pim-app-dev.unbxd.io/pim/v1/login";
	 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                     'accept: application/json',
 					 'Content-Type: application/json',
                 'Cache-Control: no-cache',
'app_custom_id: BIG_COMMERCE_TEST',
				    "org_key: $bc_org_key",
					"site_name : $StoreName "				 )
   );
   $body = '{
 // "email": "apps@unbxd.com",
  //"password": "unbxd@123" 
  "email": "'.$email.'",
  "password": "'.$psw.'"
  }';
 curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
   $result = curl_exec($ch);
   $err = curl_error($ch);
curl_close($ch);
if ($err) {
  //echo "cURL Error #:" . $err;
}
$reslt1 = json_decode($result);
 $get_apiresult = json_decode(json_encode($reslt1), true);
 $auth_token = $get_apiresult['data']['auth_token'];
 $get_api_keys = $get_apiresult['data']['org_list'];
 


if ($bc_org_key){
	 $sql_if_insert = "SELECT * FROM pim_users WHERE bc_store_hash='".$Store_Hash."'" ;
$resultgetif = $conn->query($sql_if_insert);
if ($resultgetif->num_rows == 0) {
	$row = $resultgetif->fetch_assoc();
	$sql = "INSERT INTO  pim_users( store_name, org_key, auth_token, email, bc_store_hash, api_key,  created)
VALUES ( '".$bc_name."',   '".$bc_org_key."','".$auth_token."', '".$email."','".$Store_Hash."','".$get_api_key ."', '".$date."')";
$conn->query($sql); 	
} else {
$sqlupdate = "UPDATE pim_users SET auth_token= '".$auth_token."' where bc_store_hash='".$Store_Hash."'";
	$conn->query($sqlupdate);
}
?>

<div class="container">	<div class="wrpr-main">	<div class="form-wrpr">		
<div class='loader'>	
	<img id="gif" src="loader.gif">	
	</div>		
	<div class="frm-wrpr-child">
	<form action="" method="post">	
	<p class="text-center">The Current user is associated with</p>	
 
	<select id="org_name" name="org_name" class="form-control"> 			 
	<option value="">Organisation Name</option>			
	<?php foreach ($get_api_keys as $get_api_keys_val ) { ?>
	<option value="<?php echo $get_api_keys_val['org_key']; ?> ">
	<?php echo $get_api_keys_val['name']; ?> </option>				
	<?php 	 }
 ?>	
	</select>			<div class="btn-wrpr">	
	<input type="submit" name="activate" value="Link/Activate" class="registerbtn">
	</div>	
	</form>	
	</div>	
	</div>
	</div> 
	</div>
	<?php
include('footer.php');
die();
} else {
	?>
 <div id="opn-pop">
<div class="container" id="model-popup">
  <div class="modal fade in" id="myModal" role="dialog">
    <div class="modal">
        <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">        
        </div>
        <div class="modal-body">
          <p>Invalid email, Please register on pim account.</p>
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


}

?>
 <div class='loader'><img id="gif" src="loader.gif"></div>
<form action="pim_form_old.php?id=<?php echo $Store_Hash;  ?>" method="post" class="m0">
  <div class="container">
  <div class="wrpr-main">
	<div class="d-block">
	<div class="hdng">
		<h1>Sign in To Unbxd PIM account</h1>
	</div>
  	
	<div class="form-wrpr">
		
		<h2>Log in</h2>
		<label for="email"><b>Enter Email</b></label>
		<input type="text" placeholder="Enter Email" id="email" name="email" required>

		<label for="psw"><b>Enter Password</b></label>
		<input type="password" placeholder="Enter Password"  id="psw" name="psw" required>
		<input type="hidden" id="store_hash" name="store_hash"  value="<?php echo $Store_Hash; ?>">

		<button type="submit" name="submit" id="submit" class="registerbtn">Login</button>
		<!--<div class="signin">
			<p>Don't have an account? <a href="https://pim-app-dev.unbxd.io/api/v1/stores/register">Sign Up Now!</a>.</p>
		</div>-->
	</div>
	</div>
</div>
</div>
</form>

<?php 

 
	 

	
	
include('footer.php');  ?>