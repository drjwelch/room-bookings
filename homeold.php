<?php

// home.php

// Booking system home pageheader

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Booking Homepage</title>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Droid+Serif" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script type="text/javascript" src="js/aggsbookings.js"></script>
<script type="text/javascript">

function doteacher(){ // called to populate selection box for teachers when view/confrm/cancel is clicked

	$.ajax({url: 'ajax-gettnames.php',
		success: function(output) {
			$('#tchrcode').html(output);
		},
	  error: function (xhr, ajaxOptions, thrownError) {
		alert(xhr.status + " "+ thrownError);
	  }});		
};

function doresults(ajaxdata){ // called to populate results DIVs when ajax data comes back
	$('#blist').html(ajaxdata);
	$('#conf').html(ajaxdata);
};

$(document).ready(function(){

	$("#see").click(function(){ // called if user clicks on view/cancel
		// Show the teachername selection box
		if($("#tname").css("display") == 'none') { 
			$("#tname").css("display", "block");
		}
		//Display 'loading' status in the target select list
		$('#tchrcode').html('<option value="">Click to load ...</option>');
		doteacher(); // get the teacher list

		// show the bookinglist DIV and hide the confirm-items DIV
		$("#blist").css("display", "block");
		$("#blist").html(''); // populated by above code when ajax returns
		$("#conf").css("display", "none");
	});

	$("#confirm").click(function(){
		// Show the teachername selection box
		if($("#tname").css("display") == 'none') { 
			$("#tname").css("display", "block");
		}
		//Display 'loading' status in the target select list
		$('#tchrcode').html('<option value="">Click to load ...</option>');
		doteacher(); // get the teacher list
		
		// hide the bookinglist DIV and show the confirm-items DIV
		$("#blist").css("display", "none");
		$("#conf").css("display", "block");
		$("#conf").html(''); // populated by above code when ajax returns
	});
	
	$('#tchrcode').change(function(e) {
		//Grab the chosen values when select list changes
		var selectvalue = $(this).val();
		var ename = $(this).data('email');

		var qtype = 0;
		// Pass qtype to ajax so we get all my bookings or just my provisional ones
		if($("#blist").css("display") == 'none') { 
			qtype = QT_CONF;
		} else {
			qtype = QT_VIEW;
		}

		//Display 'loading' status in the target select list
		$('#blist').html('Loading ...');
		$('#conf').html('Loading ...');
	 
		if (selectvalue == "") {
			//Display initial prompt in target select if blank value selected
		   $('#blist').html('NOTHING');
		   $('#conf').html('NOTHING');
		} else {
		  //Make AJAX request, using the selected value as the GET
		  $.ajax({url: 'ajax-getbookings.php?tcode='+selectvalue+'&qtype='+qtype,
				 success: function(output) {
					doresults(output); // populate relevant div with results
				},
			  error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status + " "+ thrownError);
			  }});
			}
    }).trigger('change'); // trigger makes this happen onload as well
});

</script>
<style type="text/css">
.button {
    width: 250px;
    background-color: #4CAF50;
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline;
    float: left;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}
#tblist table {
	width: 90%;
}
#tblist td {
	text-align: center;
}
#tblist th {
	text-align: center;
	font-style: bold;
}
</style>

</head>
<body>
<div id="outer_booking" style="width: 800px;">
<div id="hdg" style="clear: left; height: 80px;">
<img src="logo.jpg" style="float: left; max-width: 200px;" alt="AGGS Logo">
<h2 style="margin-left: 250px; line-height: 80px;">IT Room Booking System</h2>
</div>
<div id='bkg' style="clear: left;">
<a href='calendar.php'><button class="button">Make Booking</button></a>
</div>
<div id='see'>
<button class="button">View/Cancel a Booking</button>
</div>
<div id='confirm'>
<button class="button">Confirm a Booking</button>
</div>
<div id="tname" style='display: none; clear: left; margin-top: 60px; margin-bottom: 20px;'>
	<label>Teacher Code</label>
	<select name='tchrcode' id='tchrcode' style='width: 100px;'></select>
</div>
<div id='blist' style='display: none;'>
</div>
<div id='conf' style='display: none;'>
</div>
</div>

</body>
</html>
