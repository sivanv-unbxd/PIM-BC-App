<?php

include("db_config.php");
 $Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
	//echo $Store_Hash;
	//$sql = "SELECT * FROM bc_api_data  ";
     $client_id= $_POST['client_id'];
	 $client_secret=$_POST['client_secret'];
	 $oauth_token=$_POST['oauth_token'];
	 $Store_Hash=$_POST['store_hash'];
	 $user_email=$_POST['user_email'];
	 $api_key_psw=$_POST['api_key'];
	 $path=$_POST['path'];
	 $date = date("Y-m-d h:i:s");
 $sql = "INSERT INTO  bc_api_data( client_id, client_secret, oauth_token, store_hash, user_email, api_key_pw, path, created)
VALUES ( '".$client_id."',  '".$client_secret."', '".$oauth_token."', '".$Store_Hash."',
 '".$user_email."', '".$api_key_psw."', '".$path."', '".$date."')";
if ($conn->query($sql) === TRUE) { 
 header("Location: ".$Site_url."/api/v1/Pim_Form.php?id=".$Store_Hash);
 }
 else {
	 echo  $sql;
 }




?>