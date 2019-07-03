<!DOCTYPE html>
<html><?php
//include('header.php');
include("db_config.php");
  $Store_Hash = $_GET['id'];
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
 
 /*Getting total count of  all products  import details from pim*/
$url = 'https://pim-app-dev.unbxd.io/pim/v1/imports/details';
 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                     "Authorization: $api_key",
					 //"Authorization: $org_key",
					 'Content-Type: application/json' )
   );
   $body = '{ "page": 1,
   "count": 1000}'; 
 curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
   $ResponsePim1 = curl_exec($ch);
  curl_close($ch);
  $ResultPim1 = json_decode($ResponsePim1);
$ResultPim1 = json_decode(json_encode($ResultPim1), true);
$total = $ResultPim1['data']['total'];
 /*Getting   all products  import details from pim*/
$url = 'https://pim-app-dev.unbxd.io/pim/v1/imports/details';
 $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                     "Authorization: $api_key",
					 //"Authorization: $org_key",
					 'Content-Type: application/json' )
   );
   $body = '{ "page": 1,
   "count": "'.$total.'"}'; 
 curl_setopt($ch, CURLOPT_POSTFIELDS,$body);
   $ResponsePim = curl_exec($ch);
  curl_close($ch);
    $ResultPim = json_decode($ResponsePim);
$ResultPim = json_decode(json_encode($ResultPim), true);
$getentries = $ResultPim['data']['entries'];
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
  <li  id="importtab" ><a  href="import.php?id=<?php echo $Store_Hash;  ?>">Import</a></li>
    <li id="exporttab" class="active" ><a href="export.php?id=<?php echo $Store_Hash;  ?>">Export</a></li>
  </ul>
  <div class="tab-content">
    <div id="import" class="tab-pane fade in active">
		 <a href="export.php?id=<?php echo $Store_Hash; ?>" id="importt"><i class="glyphicon glyphicon-arrow-left"></i></a>
      <div class="top-sec">
		<p class="sub-head">All details of Products imported Big Commerce to PIM</p>
</div>
	  <div class="table-wrpr" id="custom-table">
 <div class="table-wrpr" id="custom-table">
<table id="example" class="display" cellspacing="0" width="100%">
    <thead class="tbl-head">
        <tr class="view">
          <th>Import file Id</th>
          <th>Import file Name</th>
          <th>Import Status</th>
        </tr>
    </thead>
 <tbody>
		<?php 

foreach ($getentries as $getentriesval) { 

?>
            <tr>
                <td><?php echo $getentriesval["import_file_id"]; ?></td>
                 <td><?php echo $getentriesval["file_name"]; ?></td>
				  <td><?php echo $getentriesval["import_state"]; ?></td>
				
            </tr>
          <?php
               
}

 ?>
         
        </tbody>
        </table>
	</div>
  </div>
  </div>
  </div>
  </div>
  </div>
	 <div class='loader'><img id="gif" src="loader.gif"></div>
	 <?php 
}
include('footer.php'); ?>