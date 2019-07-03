<!DOCTYPE html>
<html><?php
//include('header.php');
  include("db_config.php");
  $id = $_GET['id'];
  $store_hash = $_GET['store_hash'];
  $sql = "SELECT * FROM products where import_id=".$id;
$result = $conn->query($sql);
 $Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
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
jQuery('div#example_filter label input[type=search]').addClass('icon__cls');
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

  <li  id="importtab"  class="active"><a  href="import.php?id=<?php echo $store_hash;  ?>">Import</a></li>

    <li id="exporttab" <?php if($_GET['exp']==1){ echo 'id="newact"';}?> ><a href="export.php?id=<?php echo $store_hash;  ?>">Export</a></li>

  </ul>

  

  <div class="tab-content">

    <div id="import" class="tab-pane fade in active">

  <a href="import.php?id=<?php echo $store_hash; ?>" id="importt"><i class="glyphicon glyphicon-arrow-left"></i></a>

      <div class="top-sec">

		<p class="sub-head">All details of Products imported PIM to Big Commerce</p>
	  </div>

	  <div class="table-wrpr" id="custom-table">

 <div class="table-wrpr" id="custom-table">
<table id="example" class="display" cellspacing="0" width="100%">
    <thead class="tbl-head">
        <tr class="view">
              <th>Pim Product Id</th>
                <th>Product Name</th>
                <th>Product Status</th>
                <th>Product Description</th>
        </tr>
    </thead>
 <tbody>

		<?php 

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row["pim_pro_id"]; ?></td>
                 <td><?php echo $row["name"]; ?></td>
				  <td><?php echo $row["status"]; ?></td>
				   <td><?php echo $row["status_description"]; ?></td>
            </tr>
          <?php
                }
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
	 <?php include('footer.php'); ?>