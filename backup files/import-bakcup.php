<?php 
include('header.php');
  include("db_config.php");
  $Store_Hash = $_GET['id'];
  
  $sql = "SELECT * FROM  import_products ";
$result = $conn->query($sql);
/*if ($result=mysqli_query($conn,$sql))
echo $rowcount=mysqli_num_rows($result);*/
  // Return the number of rows in result set


 ?>

<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
<script>

jQuery(document).ready(function() {

    jQuery('#example').DataTable();
} );

$(function(){
  $(".tbl-body tr.view").on("click", function(){
	$(".tbl-body tr.view").removeClass("open");
	$(".tbl-body tr.fold").removeClass("open");
    $(this).toggleClass("open").next(".fold").toggleClass("open");
  });
});
</script>


<!------------------------- START MANAGE IMPORT AND EXPORT ---------------------------->
	  <div class="container">
  <h3 style="text-align:center";>Import And Export Products</h3>
<div class="wrapper f6">
<p class="mng-head">Manage Imports and Exports</p>
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#import">Import</a></li>
    <li <?php if($_GET['exp']==1){ echo 'id="newact"';}?> ><a data-toggle="tab" href="#export">Export</a></li>
  </ul>
  
  <div class="tab-content">
    <div id="import" class="tab-pane fade in active">
      <div class="top-sec">
		<p class="sub-head">Start a new Import to get data from PIM to Big Commerce</p>
		<div class="right-btn">
			
			<a href="import_pmtobc.php?id=<?php echo $Store_Hash;?>" id="import">Import Products From Pim</a>
		</div>
	  </div>
	  <div class="table-wrpr">
		<table id="example" class="table table-striped " style="width:100%">
			<thead class="tbl-head">
				<tr>
					<th></th>
					<th>Date</th>
					<th>Activity</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody class="tbl-body">
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
				<tr class="view">
					<td><a href="#" class="show-details"><span class="glyphicon glyphicon-triangle-right"></span><span class="glyphicon glyphicon-triangle-bottom"></span></a></td>
					<td><?php echo $row_import["created"]; ?></td>
					<td><?php echo $total_count; ?> Product Imported</td>
					<td><?php if($getfail==0)
					{ echo "Import Completetd"; } else {
						
						echo "Completed with some <span style='color: #ec0202;'>failure</span>";
					}
				?>
				</tr>
				<tr class="fold">
					<td colspan="4">
						<div class="dtls-wrpr">
							<div class="prdt-smry">
								<p>Product Summary</p>
								<hr>
								<ul class="smry-list">
								<?php   
								$resultttq="SELECT count(*) as total  from products where status ='Fail' and import_id =".$row_import['id'];
  $resultttq11 = $conn->query($resultttq);
  $resultttq11=$resultttq11->fetch_assoc();
$resultttq11=$resultttq11['total'];


$resultttanother="SELECT count(*) as total from products where status ='Done' and import_id =".$row_import['id'];
  $resultttanother = $conn->query($resultttanother);
 $resultttanother=$resultttanother->fetch_assoc();
$resultttanother=$resultttanother['total'];


	?>
									<li class="smry-list-item">Processed <span><?php echo $resultttanother; ?></span></li>
									<li class="smry-list-item">New <span><?php echo $resultttanother; ?></span></li>
									<li class="smry-list-item">Updated <span><?php echo $resultttanother; ?></span></li>
									<li class="smry-list-item">Failed <span><?php echo $resultttq11; ?></span></li>
									<li class="smry-list-item"><a href="getstatus.php?id=<?php echo $row_import['id'];?>&store_hash=<?php echo $Store_Hash; ?>">View Report</a></li>
								</ul>
								<p class="ttl">Total <span><?php echo count($counttotal); ?></span></p>
							</div>
							<div class="asst-smry">
								<p>Asset Summary</p>
								<hr>
								<ul class="smry-list">
									<li class="smry-list-item">Success <span><?php echo $resultttanother; ?></span></li>
									<li class="smry-list-item">failed <span><?php echo $resultttq11; ?></span></li>
								</ul>
								<p class="ttl">Pending <span><?php echo $resultttq11; ?></span></p>
							</div>
						</div>
					</td>
				</tr>
			<?php }} ?>
			</tbody>
		</table>
	  </div>
    </div>
    <div id="export" class="tab-pane fade">
      <div class="top-sec">
		<p class="sub-head">Start an Export to get data from Big Commerce to Pim </p>
		<div class="right-btn">
	
			<a href="export_big_to_pim.php?id=<?php echo $Store_Hash;?>" id="import" >Export Products Into Pim</a>
		</div>
	  </div> 
	  <div class="table-wrpr">
		<table id="example" class="table table-striped " style="width:100%">
			<thead class="tbl-head">
				<tr>
					<th></th>
					<th>Date</th>
					<th>Activity</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody class="tbl-body">
				<tr class="view">
					<td><a href="#" class="show-details"><span class="glyphicon glyphicon-triangle-right"></span><span class="glyphicon glyphicon-triangle-bottom"></span></a></td>
					<td>10-05-2019 11:00PM (IST)</td>
					<td>159 Products Imported</td>
					<td>Pre Imported completed</td>
				</tr>
				<tr class="fold">
					<td colspan="4">
						<table class="table inner-table">
							<thead>
								<tr>
									<th>Product Id</th>
									<th>Product Name</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>100000014</td>
									<td>Dolor sit amet</td>
								</tr>
								<tr>
									<td>100000014</td>
									<td>Dolor sit amet</td>
								</tr>
								<tr>
									<td>100000014</td>
									<td>Dolor sit amet</td>
								</tr>
								<tr>
									<td>100000014</td>
									<td>Dolor sit amet</td>
								</tr>
								<tr>
									<td>100000014</td>
									<td>Dolor sit amet</td>
								</tr>
								<tr>
									<td>100000014</td>
									<td>Dolor sit amet</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr class="view">
					<td><a href="#" class="show-details"><span class="glyphicon glyphicon-triangle-right"></span><span class="glyphicon glyphicon-triangle-bottom"></span></a></td>
					<td>10-05-2019 11:00PM (IST)</td>
					<td>159 Products Imported</td>
					<td>Pre Imported completed</td>
				</tr>
				<tr class="fold">
					<td colspan="4">
						<table class="table inner-table">
							<thead>
								<tr>
									<th>Product Id</th>
									<th>Product Name</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>100000014</td>
									<td>Dolor sit amet</td>
								</tr>
								<tr>
									<td>100000014</td>
									<td>Dolor sit amet</td>
								</tr>
								<tr>
									<td>100000014</td>
									<td>Dolor sit amet</td>
								</tr>
								<tr>
									<td>100000014</td>
									<td>Dolor sit amet</td>
								</tr>
								<tr>
									<td>100000014</td>
									<td>Dolor sit amet</td>
								</tr>
								<tr>
									<td>100000014</td>
									<td>Dolor sit amet</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	  </div>
    </div>
  </div>
</div>
 <div class='loader'><img id="gif" src="loader.gif"></div>
</div>
<script>
jQuery(document).ready(function() {
	//alert('ffff'); 
if($('#newact').length){
	$(document).ready(function() {
  setTimeout(function() {
     $("#newact").trigger("click");
  }, 5000);
});
	
	
		
	}else{
		//alert("Div1 does not exists");
	}
	} );

</script>
<style>
.loader {
    position: fixed;
    top: 0;
	    background: #ffffffb5 !Important;
}
</style>
 

<?php 
include('footer.php'); ?>