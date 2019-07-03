<?php 
ob_start(); 
include("header.php"); 
	include("db_config.php"); 
 $Store_Hash = $_GET['id'];
   $Store_Hash = str_replace('stores/','',$Store_Hash);
   $Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
  $signedRequest = $_GET['signed_payload'];
  

  function verifySignedRequest($signedRequest)
{
    list($encodedData, $encodedSignature) = explode('.', $signedRequest, 2);

    // decode the data
     $signature = base64_decode($encodedSignature);
         $jsonStr = base64_decode($encodedData);
     $data = json_decode($jsonStr, true);
	//echo "<pre>";
	//print_r ($data);
  
   return $data;
}
 $x=verifySignedRequest($signedRequest);

	
if(!empty($Store_Hash) ) { 
     $sql = "SELECT * FROM bc_api_data WHERE store_hash = '".$Store_Hash."'";

			}
 else {
	  $sql = "SELECT * FROM bc_api_data WHERE store_hash = '".$x['store_hash']."'";
	 
 }

$result = $conn->query($sql);

if ($result->num_rows > 0) {
	    while($row = $result->fetch_assoc()) {
			
			if(!empty($Store_Hash) ) { 
			
      header("Location: ".$Site_url."/api/v1/Pim_Form.php?id=".$Store_Hash);
			}
			else {
			
				  header("Location: ".$Site_url."/api/v1/Pim_Form.php?id=".$x['store_hash']);
		}
    }
} else {
	


?>
<div id="redirecting_form"> </div>
<form id="regForm" action="login_api.php" method="post">
  <h1>Welcome to our Unbxd App</h1>
  <p class="text-center">Follow the on screen instruction to configure this app</p>
  <!-- One "tab" for each step in the form: -->
  <div class="tab">
    <p>Create an Api account and put the details below. <span class="inst"> See instruction</span></p>
	<div class="tab1 frst-frm">
	<p>Put Your API details here:</p>

	<label>Client Id </label><input type="text" name="client_id" id="client_id"> 
	<label>Client Secret </label><input type="text" name="client_secret" id="client_secret">
	<label>Acess Token  </label><input type="text" name="oauth_token" id="oauth_token">
	<?php if(!empty($Store_Hash) ) { ?>
	<input type="hidden" name="store_hash" id="store_hash" value="<?php echo $Store_Hash; ?>">
	<?php }
	else { ?>
	<input type="hidden" name="store_hash" id="store_hash" value="<?php echo $x['store_hash']; ?>">
	<?php }
	?> 
	 
    <div style="float:right;" class="step_btns">
      <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
      <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
    </div>
 
  </div>
  
	<h5 class="hd-inst">Steps to create Api account:</h5>
 <p class="instruction-list">1) Go to : Advance Setting -> Api Account <br>
2) Create an API account<br></p>
  </div>
  
  <div class="tab">
  <p >Get credentials and paste below. <span class="inst"> See instruction</span></p>
	<div class="scnd-frm">
  
	  <div class="tab1">
		<p>Paste your credentials here.</p>
			<label>User Email </label><input type="text" name="user_email" id="user_email"><br>
			<label>Api (Password) </label><input type="text" name="api_key" id="api_key"><br>
			<label>Path </label><input type="text" name="path" id="path"><br>
	  </div>
	   
    <div style="float:right;" class="step_btns">
      <button type="button" id="prevBtn" onclick="nextPrev(-1)">Previous</button>
      <button type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
  
  </div>
  </div>
  <h5 class="hd-inst">Steps to get credentials:</h5> 
  <ol class="instruction-list">
    <li> Go to : Account Setting -> Users -> Edit ( On right side under action tab ) </li>
	  <li>Copy Email </li>
	  <li>Copy Password </li>
	  <li>Copy Path </li>
	  <li>Paste those details into next tab </li>
	</ol>
  </div>
  
  
 
  <!-- Circles which indicates the steps of the form: -->
  <div style="text-align:center;margin-top:40px;" class="dotss">
    <span class="step"></span>
    <span class="step"></span>
  </div>
</form>

<script>
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
  // This function will display the specified tab of the form...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  //... and fix the Previous/Next buttons:
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Submit";
  } else {
    document.getElementById("nextBtn").innerHTML = "Next";
  }
  //... and run a function that will display the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form...
  if (currentTab >= x.length) {
    // ... the form gets submitted:
	document.getElementById("regForm").style.display = "none";
		document.getElementById("redirecting_form").innerHTML = "<div class='loader_login'><img id='gif' src='loader.gif'><p>The Form Is being reidrected To Pim Account...</p></div>";
    document.getElementById("regForm").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}
function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
      // add an "invalid" class to the field:
      y[i].className += " invalid";
      // and set the current valid status to false
      valid = false;
    }
  }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}
function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class on the current step:
  x[n].className += " active";
}
</script>
 <?php  
	 
}
include("footer.php"); 
   ?>
