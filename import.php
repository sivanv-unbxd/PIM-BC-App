<!DOCTYPE html>
<html>
<?php 
include("db_config.php");
  $Store_Hash = $_GET['id'];
  
 $checkvalid_account = "SELECT * FROM pim_users WHERE bc_store_hash='".$Store_Hash."'" ;
  $checkvalid_account = $conn->query($checkvalid_account);
if ($checkvalid_account->num_rows==0) {
	header("Location: ".$Site_url."/api/v1/Pim_Form.php?id=".$Store_Hash);
}else {
	$row = $checkvalid_account->fetch_assoc();
	$auth_token = $row['auth_token'];
	$bc_org_key = $row['org_key'];
	$api_key = $row['api_key'];
	if(empty($api_key) ){
header("Location: ".$Site_url."/api/v1/Pim_Form.php?id=".$Store_Hash);
	 }
}
  $Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
  $checkstorehash="SELECT * from bc_api_data where store_hash='".$Store_Hash."'";
$checkstorehash = $conn->query($checkstorehash);
if($checkstorehash->num_rows > 0)
{
$sqlget = "SELECT  bc_api_data.* ,
pim_users.* FROM   bc_api_data ,pim_users  WHERE  bc_api_data.store_hash = '". $Store_Hash ."' and pim_users.bc_store_hash = '". $Store_Hash ."' ";
$resultAll = $conn->query($sqlget);
if ($resultAll->num_rows > 0) {
    while($row[] = $resultAll->fetch_assoc()) {
	$store_hash = $row[0]['store_hash'];
	$user_email = $row[0]['user_email'];
    $client_id = $row[0]['client_id'];
	$client_secret = $row[0]['client_secret'];
	$oauth_token = $row[0]['oauth_token'];
	$path = $row[0]['path'];         
	$org_key = $row[0]['org_key'];  
    $api_key = $row[0]['api_key'];	
}
}
if ($api_key) {
 $sql = "SELECT * FROM  import_products ";
$result = $conn->query($sql);

?>
<head>
 <link rel="stylesheet" href="<?php echo $Site_url; ?>/api/v1/css/style.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css">
<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
	<script type="text/javascript">
	
jQuery(document).ready(function (){
jQuery('div#example_filter label input[type=search]').addClass('ss');
});
	jQuery(document).ready(function (){
jQuery('div#example_filter label input[type=search]').addClass('customclass');

	  jQuery('a#import').click(function(){ 
			 jQuery("div#myProgress").css("display","block");
		   jQuery(".loader").css("display","block");
        
    });  
	jQuery('#importtab a, #exporttab a').click(function(){
			   jQuery(".loader").css("display","block");
        
    }); 

    var table = jQuery('#example').DataTable({

		  "order": [[ 0, "desc" ]],

        'responsive': true

    });



    // Handle click on "Expand All" button

    jQuery('#btn-show-all-children').on('click', function(){

        // Expand row details

        table.rows(':not(.parent)').nodes().to$().find('td:first-child').trigger('click');

    });



    // Handle click on "Collapse All" button

    jQuery('#btn-hide-all-children').on('click', function(){

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
  <li  id="importtab"  class="active"><a  href="import.php?id=<?php echo $Store_Hash;  ?>">Import</a></li>
    <li id="exporttab" <?php if($_GET['exp']==1){ echo 'id="newact"';}?> ><a href="export.php?id=<?php echo $Store_Hash;  ?>">Export</a></li>
  </ul>
 

  <div class="tab-content">
    <div id="import" class="tab-pane fade in active">
      <div class="top-sec">
		<p class="sub-head">Start a new Import to get data from PIM to Big Commerce</p>
		<div class="right-btn">
			<a href="exportFromPim.php?api_key=<?php echo $api_key; ?>&api_check=1" id="import">Import Products From Pim</a>
		</div>
	  </div>
	  <div class="table-wrpr" id="custom-table">
<table id="example" class="display" cellspacing="0" width="100%">
    <thead class="tbl-head">
        <tr class="view">
                    <th>Date</th>
					<th>Activity</th>
					<th>Status</th>
            <th class="none">Success   </th>
            <th class="none">New </th>
            <th class="none">Updated </th>
			<th class="none">Failed  </th>
			<th class="none">Report </th>
        </tr>
    </thead>
 <tbody>
	<?php if ($result->num_rows > 0) {
    // output data of each row
 $user_product_import="SELECT *  from import_products where user_id ='".$Store_Hash."' ORDER BY id DESC" ;
 $user_product_import = $conn->query($user_product_import);
while($row_import = $user_product_import->fetch_assoc()) {
$resultttanothertotal="SELECT count(*) as total from products where import_id =".$row_import['id'];
  $resultttanothertotal = $conn->query($resultttanothertotal);
    $row=$resultttanothertotal->fetch_assoc();
   $total_count=$row['total'];
$getfail="SELECT count(*) as total from products where status='Fail' and import_id =".$row_import['id'];
  $getfail=$conn->query($getfail);
$getfail=$getfail->num_rows;
?>
<tr>
<td><?php echo $row_import["created"]; ?></td>
<td><?php echo $total_count; ?> Product Imported</td>
 <td><?php if($getfail==0)
					{ echo "Import Completetd"; } else {
echo "Completed with some <span style='color: #ec0202;'>failure</span>";
}
?></td>
<?php   
$resultttq="SELECT count(*) as total  from products where status ='Fail' and import_id =".$row_import['id'];
  $resultttq11 = $conn->query($resultttq);
  $resultttq11=$resultttq11->fetch_assoc();
  $resultttq11=$resultttq11['total'];
  $resultttanother="SELECT count(*) as total from products where status ='Done' and import_id =".$row_import['id'];
  $resultttanother = $conn->query($resultttanother);
  $resultttanother=$resultttanother->fetch_assoc();
  $resultttanother=$resultttanother['total'];
  $resultttanotherupdate="SELECT count(*) as total from products where status ='Updated' and import_id =".$row_import['id'];
  $resultttanotherupdate = $conn->query($resultttanotherupdate);
  $resultttanotherupdate=$resultttanotherupdate->fetch_assoc();
  $resultttanotherupdate=$resultttanotherupdate['total'];
?>
            <td><?php echo $resultttanother; ?></td>
             <td><?php echo $resultttanother; ?></td>
			  <td><?php echo $resultttanotherupdate; ?></td>
			   <td><?php echo $resultttq11; ?></td>
			    <td><a id="report-txt" href="import_getstatus.php?id=<?php echo $row_import['id'];?>&store_hash=<?php echo $Store_Hash; ?>">View Report</a></td>
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
<?php }
else { 
echo "api key not available";}
} ?>
</html>