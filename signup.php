<?php include("header.php"); ?>
<?php
 /* https://pim-app-dev.unbxd.io/api/v1/stores/register \
  -H 'Authorization: auth_key_sent_in_login' \
  -H 'content-type: application/json' \
  -d '{
    "app_custom_id": "BIG_COMMERCE_TEST",
    "org_key": "ce97f30c78949790ee59054d08538414",
    "site_name": "big commerece hash store"
}'*/

$url = 'https://pim-app-dev.unbxd.io/pim/v1/register';
 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                     'Authorization: 5ccbeb6ebc1413682ea46c3c',
					 //"Authorization: $org_key",
					 'Content-Type: application/json',
                 'Cache-Control: Cache-Control' )
   );
   $body = '{ "app_custom_id": BIG_COMMERCE_TEST,
   "org_key":ce97f30c78949790ee59054d08538414,
    "site_name": gzxgekl3n1}'; 
 curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
   $ResponsePim = curl_exec($ch);
  curl_close($ch);
$ResultPim = json_decode($ResponsePim);
echo "<pre>";
print_r ($ResultPim);
?>
<div class="container">
  <div class="wrapper_signup">
	<div class="d-block">
	<div class="hdng">
		<h1>Sign Up to Unbxd PIM account</h1>
	</div>
  	
	<div class="sign-up-form-wrpr">
	<form>
		<h2>Sign Up</h2>
		<label for="name">Name</label>
		<input type="text" placeholder="Enter Your Name" id="name" name="name" required>
		
		<label for="email">Email</label>
		<input type="text" placeholder="Enter Your Email Address" id="email" name="email" required>

		<label for="psw">Password</label>
		<input type="password" placeholder="Enter a Password"  id="psw" name="psw" required>

		<label for="org">Organization Name</label>
		<input type="text" placeholder="Enter your organization name"  id="org" name="org" required>

		<label for="phn">Phone Number</label>
		<input type="text" placeholder="Enter your phone number"  id="phn" name="phn" required>
	
		<span class="trm-cdn"><input type="checkbox" name="agree" value="agree" class="wdth">I agree to the <a href="#">Unbxd terms and Conditions</a></span>
		
		<button type="submit" name="submit" id="submit" class="registerbtn">Sign UP</button>
		<div class="signin">
			<p>Already have an account? <a href="#">Sign in here!</a></p>
		</div>
	</form>
	</div>
	</div>
</div>
</div>

<?php include("footer.php"); ?>