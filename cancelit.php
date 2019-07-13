<?php

// cancelit.php

// Called when user clicks on a link in their email to actually cancel their booking in the database

include('php/aggsbookings.php'); 

$bkid = htmlspecialchars($_GET['bid']);
$bkid = intval($bkid);

$stmt = $link->prepare("DELETE FROM booking WHERE bookingid = ?"); 
$stmt->bind_param('i', $bkid);
$stmt->execute();
if ($stmt->affected_rows != 1) {
	aggslog("cancelit.php:  Error in cancelling booking ".$bid.".");
}
$stmt->close();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Booking Cancelled</title>
<link href="style.css" rel="stylesheet" type="text/css">

<link href="http://fonts.googleapis.com/css?family=Droid+Serif" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet" type="text/css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>

</head>

<body>

<?php
	doneItPage("The specified booking has been cancelled from the database.");
?>

</body>

</html>
