<?php
ob_start();
$Store_Hash = $_GET['id'];
include("db_config.php");
include("header.php");
$Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
// Check connection
if(isset($_POST['submit_update'])) {
 $oauth_token=$_POST['oauth_token'];
  $client_id=$_POST['client_id'];
   $client_secret=$_POST['client_secret'];
   $email=$_POST['user_email'];
   $psw=$_POST['api_key_pw'];
   $path=$_POST['path'];
$sql = "UPDATE bc_api_data SET oauth_token = '$oauth_token' ,client_id = '$client_id', 
       client_secret = '$client_secret' ,user_email = '$email' , api_key_pw = '$psw',path = '$path'  WHERE store_hash = '$Store_Hash'";

if (mysqli_query($conn, $sql)) {
   header("Location: ".$Site_url ."/api/v1/import.php?id=".$Store_Hash);
} 
}

?>
<form action="update_api.php?id=<?php echo $Store_Hash; ?>" method="post">
  <div class="container1">
    <h1>Update Your Api Keys</h1>
    <hr>
  
   <br> <label>(Client Id) </label><input type="text" name="client_id" id="client_id" required> <br>
<label>(Client Secret) </label><input type="text" name="client_secret" id="client_secret" required><br>
    <label>(Access Token)</label>
    <input type="text"  id="oauth_token" name="oauth_token" required>
	 <br> <label>Email </label><input type="text" name="user_email" id="user_email" required> <br>
<label>Password </label><input type="text" name="api_key" id="api_key" required><br>
<label>Path </label><input type="text" name="path" id="path" required><br>

<div class="btn-wrpr">	
	    <input type="submit" name="submit_update" id="submitt"  value="Update" class="registerbtn">
  </div>  </div>
  </form>
  
  <?php include("footer.php"); ?>