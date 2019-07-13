<?php

// Globally-included header file

// Make a MySQL Connection

$host="localhost";
$user="root";
$password="password";
$db = "aggsbookings";

$link = mysqli_connect($host, $user, $password);
mysqli_select_db($link, $db) or die(mysql_error());

// Logfile action

function aggslog($msg) {
	$tstamp = date("Y-m-d") . " " . date("h:i:sa");
	file_put_contents("/var/log/aggsbookings.log", $tstamp." ".$msg."\r\n", FILE_APPEND | LOCK_EX);
}

// Control whether email is really sent or not

function bookingmail($addr, $subj, $msg) {

	// Additional headers for html content and setting From address to replace www-data
	$headers  = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
	$headers .= "From: IT Room Bookings <no-reply@aggs.bfet.uk>" . "\r\n";

	mail($addr."@aggs.bfet.uk",$subj,$msg,$headers);
	mail('jwelch@aggs.bfet.uk',$subj." - Copy",$msg,$headers);

	aggslog("mailing.php: email sent to ".$addr." title ".$subj);
//	aggslog($msg);
}

// Pretty print a date string

function nicedate($thedate) {
	$date = new DateTime($thedate);
	return $date->format('D j M y');
}

function doneItPage($txt) {
	echo"<div id='outer_booking' style='width: 800px;'>";
	echo "<div id='hdg' style='clear: left; height: 80px;'>";
	echo "<img src='logo.jpg' style='float: left; max-width: 200px;' alt='AGGS Logo'>";
	echo "<h2 style='margin-left: 250px; line-height: 80px;'>IT Room Booking System</h2>";
	echo "</div>";
	echo "<div class='success'>".$txt."</div>";
	echo "<div><p>You can close this window or <a href='home.php'>return to homepage.</a></p></div>";
}

// Constants - MUST ALSO APPEAR IN aggsbookings.js

define("ST_PROV",1);
define("ST_CONF",2);
define("ST_LAPS",3);
define("ST_TTBL",4);

define("QT_VIEW",1);
define("QT_CONF",2);

?>
