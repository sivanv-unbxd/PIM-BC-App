<html>

<?php 

  include("db_config.php");
   require ("vendor/autoload.php");
  $Store_Hash = $_GET['id'];
  
 $Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
  $sql = "SELECT * FROM  export_to_pim ";
$result = $conn->query($sql);
 use Bigcommerce\Api\Client as Bigcommerce;
 
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
if ($api_key ) {
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


 $totalcount_pro = $getallproducts->meta->pagination->total;

 ?> 
 <head>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script><script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css"> 
<link rel="stylesheet" href="<?php echo $Site_url; ?>/api/v1/css/style.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css"><link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css"><script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script><script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

	<script type="text/javascript">
	  jQuery(document).ready(function() {
	  jQuery('a#import').click(function(){
		
		   jQuery(".loader").css("display","block");
      
    }); 
		jQuery('#importtab a, #exporttab a').click(function(){
			   jQuery(".loader").css("display","block");
        
    }); 
	
	});
	$(document).ready(function (){
    var table = $('#example').DataTable({
		  "order": [[ 0, "desc" ]],
        'responsive': true
    });

    // Handle click on "Expand All" button
    $('#btn-show-all-children').on('click', function(){
        // Expand row details
        table.rows(':not(.parent)').nodes().to$().find('td:first-child').trigger('click');
    });

    // Handle click on "Collapse All" button
    $('#btn-hide-all-children').on('click', function(){
        // Collapse row details
        table.rows('.parent').nodes().to$().find('td:first-child').trigger('click');
    });
});
	</script>
</head>
<body>

<!------------------------- START MANAGE IMPORT AND EXPORT ---------------------------->
	  <div class="container">
  <h3 style="text-align:center";>Import And Export Products</h3>
<div class="wrapper f6">
<p class="mng-head">Manage Imports and Exports</p>
<ul class="nav nav-tabs">
    <li id="importtab"><a  href="import.php?id=<?php echo $Store_Hash;  ?>">Import</a></li>
    <li  class="active" <?php if($_GET['exp']==1){ echo 'id="newact"';}?> ><a id="exporttab" href="export.php?id=<?php echo $Store_Hash;  ?>">Export</a></li>
  </ul>
  
  <div class="tab-content">
    <div id="import" class="tab-pane fade in active">
     <div class="top-sec">
		<p class="sub-head">Start an Export to get data from Big Commerce to Pim </p>
		<div class="right-btn">
	
			<a href="importToPim.php?api_key=<?php echo $api_key ;?>&api_check=1" id="import" >Export Products Into Pim</a>
		</div>
	  </div> 
	   <div class="table-wrpr" id="custom-table">
<table id="example" class="display" cellspacing="0" width="100%">
      <thead class="tbl-head">
        <tr>
            <th>Date</th>
			<th>Activity</th>
			<th>Status</th>
            <th class="none">Report</th>
            
         
        </tr>
    </thead>
 
    <tbody>
	<?php if ($result->num_rows > 0) {
    // output data of each row
 $user_product_export="SELECT *  from export_to_pim where user_id ='".$Store_Hash."' ORDER BY id DESC" ;
 $user_product_export = $conn->query($user_product_export);

while($row_export = $user_product_export->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row_export["created"]; ?></td>
					<td><?php echo $totalcount_pro; ?> Products Imported</td>
					<td>Pre Imported completed</td>
            <td><a  href="export_getstatus.php?id=<?php echo $Store_Hash; ?>">View Report</a></td>
            
        </tr>
	<?php }} ?>
    </tbody>
</table>
  </div>
    </div>
	
	
  </div>
</div>
 <div class='loader'><img id="gif" src="loader.gif"></div>
</div>
</body>
<?php } else{echo "api key not available";}
} ?>
</html>