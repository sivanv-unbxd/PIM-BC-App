<?php
 ob_start();
 include("db_config.php");

   $code = $_REQUEST['code'];
  $context = $_REQUEST['context'];
  $scope = $_REQUEST['scope'];
 $Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";

 
$postfields = array(
	"client_id" => "340ypj2sh5tldbeoqlud1b3e9q4grx1",
    "client_secret" => "1bemkwyk9ccflbeb6hh4yk37f6nbo0r",
    "redirect_uri" => $Site_url."/api/v1/auth.php",
    "grant_type" => "authorization_code",
    "code" => $code ,
    "scope" => $scope , 
    "context" => $context
	);
$postfields = http_build_query($postfields);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://login.bigcommerce.com/oauth2/token');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
$result = curl_exec($ch);
$data = json_decode($result, TRUE);

$date = date("Y-m-d h:i:s");
 $context = str_replace('stores/','',$context);
$query = mysqli_query($conn, "SELECT * FROM auth WHERE context='".$context."'");
 if(mysqli_num_rows($query) > 0){
header("Location: ".$Site_url."/api/v1/login.php?id=".$context);
 }
 else{
$sql = "INSERT INTO  auth( code, context,   created)
VALUES ( '".$code."',   '".$context."', '".$date."')";
if ($conn->query($sql) === TRUE) { 
header("Location: ".$Site_url."/api/v1/login.php?id=".$context);

}
 }





?>