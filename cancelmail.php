<?php

// cancelmail.php

// This script is called when the user clicks cancel from the home screen
//
// Email the user with a link to actually cancel the booking
// Ensures other users cannot easily cancel someone else's booking
//

include('php/aggsbookings.php'); 

$bkid = htmlspecialchars($_GET['bid']);
$bkid = intval($bkid);
$eml = htmlspecialchars($_GET['eml']);
$room = htmlspecialchars($_GET['room']);
$period = htmlspecialchars($_GET['period']);
$bdate = htmlspecialchars($_GET['bdate']);

$themail = "Dear ".$eml."<br><br>A request was made to cancel your booking for ".$room.", ".$period." on ".$bdate."<br><br>";
$themail = $themail."<a href='http://10.16.56.184/cancelit.php?bid=".$bkid."'>Click here to cancel this booking</a> (NB Link only works in school/on remote desktop.)";

bookingmail($eml, 'Request to cancel your room booking', $themail);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Booking Cancel Request</title>
<link href="style.css" rel="stylesheet" type="text/css">

<link href="http://fonts.googleapis.com/css?family=Droid+Serif" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>

</head>

<body>

<?php
	doneItPage("You have been sent an email containing a link to cancel the booking.");
?>

</body>

</html>
