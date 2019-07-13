<?php

// calendar.php

//  Main booking page called when make booking selected from home page
//  Uses classes/class_calendar.php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include('php/aggsbookings.php'); 
include('classes/class_calendar.php');

// ========================================================================================
// PHP below is taken from calendar class creator unmodified apart from where commented JW
// ========================================================================================

$calendar = new booking_diary($link);

if(isset($_GET['month'])) $month = $_GET['month']; else $month = date("m");
if(isset($_GET['year'])) $year = $_GET['year']; else $year = date("Y");
if(isset($_GET['day'])) $day = $_GET['day']; else $day = 0;

// Unix Timestamp of the date a user has clicked on
$selected_date = mktime(0, 0, 0, $month, 01, $year); 

// Unix Timestamp of the previous month which is used to give the back arrow the correct month and year 
$back = strtotime("-1 month", $selected_date); 

// Unix Timestamp of the next month which is used to give the forward arrow the correct month and year 
$forward = strtotime("+1 month", $selected_date);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Calendar</title>
<link href="style.css" rel="stylesheet" type="text/css">

<link href="http://fonts.googleapis.com/css?family=Droid+Serif" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script type="text/javascript">

// JWs jQ from here

$(document).ready(function(){

	$(".fields").click(function(){ // if you click on anything with class fields i.e. all the room grid radiobuttons
	
		dataval = $(this).data('val'); // extracts the data-val attribute of the field class widget
	
		// Show the Selected Slots box if someone selects a slot
		if($("#outer_basket").css("display") == 'none') { 
			$("#outer_basket").css("display", "block");
		}

		rname = dataval.substring(0,dataval.indexOf("%"));
		roomid = dataval.substring(dataval.indexOf("@")+1);
		periodid = dataval.substring(dataval.indexOf("%")+1,dataval.indexOf("@"));
		
		// Populate the Selected Slots section
		// this is now the room id - no need to show that so slots changed to empty string
		$("#make").html("Make Booking for "+rname);
		
		// set hidden fields
		$("#periodid").val(periodid);		
		$("#roomid").val(roomid);		

		// data-status attribute of field lets us know if we are booking a clear slot or overbooking a lapsed one
		// format of this attribute is status_num#bkid@user_email if it is a lapsed one
		datastatus = $(this).data('status');
		$("#statusbid").val(datastatus);		

	}); // end of fields click

	$("#tchrcode").change(function(){ // when teacher select box option is chosen
		temail = $(this).find(':selected').data('email');
		$("#temail").val(temail);		// set hidden field temail to be the data-email attribute of the selected teacher SIMS name
	}).trigger('change'); // trigger makes this happen onload as well
	
	$(".classname").click(function(){ // called when we click on the Make Booking button - form validation
		msg = '';
		if($("#tchrcode").val() == '')
			msg += 'Please enter a teacher code\r\n';
		if($("#yeargroup").val() == '')
			msg += 'Please enter a year group\r\n';
		// deal with reason boxes
		var reason = 0;
		$.each($("input[name='reason']:checked"), function(){            
                reason += parseInt($(this).val()); // add up the binary powers for each selected option
            });
		$("#reason").val(reason);	// store in hidden field	
		if (reason == 0)
			msg += 'Please enter a reason\r\n';	
		// alert it
		if(msg != '') {
			alert(msg);
			return false;
		}
	});

	// Firefox caches the checkbox state.  This resets all checkboxes on each page load 
	$('input:checkbox').removeAttr('checked');
	
});




</script>

</head>
<body>
<div id="outer_booking" style="width: 1000px;">
<div id="hdg" style="clear: left; height: 80px;">
<img src="logo.jpg" style="float: left; max-width: 200px;" alt="AGGS Logo">
<h2 style="margin-left: 250px; line-height: 80px;"><a href="home.php">IT Room Booking System</a></h2>
</div>
<?php     
        
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $calendar->after_post($month, $day, $year);  
}   

// Call calendar function
$calendar->make_calendar($selected_date, $back, $forward, $day, $month, $year);

?>
</div>

</body>
</html>
