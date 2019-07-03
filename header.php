<?php
$Site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}";
?>
<html>
<head><link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo $Site_url; ?>/api/v1/css/style.css">
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
  <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">

   </head>
       <script>
  jQuery(document).ready(function() {
	  
	   jQuery("button#close").click(function(){
        jQuery(".modal.fade").removeClass("in");
		 jQuery("div#opn-pop").css("display","none");
		  
		
		 	  });
			  			
						
			   jQuery('button#submit').click(function() {
				     jQuery(".loader").css("display","block");
				       
            });
			jQuery('button#submit').prop('disabled', true);
			
			jQuery('#email,#psw').keyup(function () {
    if (jQuery('#email').val() == '' || jQuery('#psw').val() == '') {
		jQuery('button#submit').prop('disabled', true);
	}
        //Check to see if there is any text entered
        // If there is no text within the input ten disable the button
		else {
           jQuery('button#submit').prop('disabled', false);
    } 
});

		
	
	 jQuery("div#myProgress").css("display","none");
			
    jQuery('a#import').click(function(){
			 jQuery("div#myProgress").css("display","block");
		   jQuery(".loader").css("display","block");
           });

		});	

jQuery(document).ready(function() {
   jQuery('#example').DataTable();
});


  </script>
<body>
