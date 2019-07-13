<?php

// dailymailing.php

// This script:
//   emails users 7 and 5 days before a provisional booking (status = 1) asking them to confirm or cancel
//   is set to run on the server every day at midnight
//   3 days prior, sets still-provisional bookings to lapsed status (status = 3)

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// modded to hard code the path so it can run from command line
include('/var/www/php/aggsbookings.php'); 
aggslog("dailymailing.php: job started");

// Bookings for 7 days and 5 days hence that are provisional receive an email

$query = "SELECT booking.bookingid, room.name, booking.bookingdate, teacher.emailaddress";
$query = $query." FROM booking, teacher, room";
$query = $query." WHERE booking.teacherid = teacher.teacherid AND booking.roomid = room.roomid";
$query = $query." AND booking.status = 1";
$query = $query." AND ( DAYOFYEAR(booking.bookingdate) = DAYOFYEAR(NOW()+INTERVAL 6 DAY) OR DAYOFYEAR(booking.bookingdate) = DAYOFYEAR(NOW()+INTERVAL 4 DAY) )";

$stmt = $link->prepare($query); 
$stmt->bind_result($bid,$room,$bdate,$eml);
$stmt->execute();
$stmt->store_result();
aggslog("dailymailing.php: sending ".$stmt->affected_rows." emails to provisional bookings.");
while($stmt->fetch()) {
	$themail = "Dear ".$eml."\r\n\r\nPlease confirm or cancel your booking for ".$room." on ".$bdate."\r\n";
	$themail = $themail."<a href='http://10.16.56.184/confirmit.php?bid=".$bid."&eml=".$eml."'>Confirm this booking</a>\r\n";
	$themail = $themail."<a href='http://10.16.56.184/cancelit.php?bid=".$bid."'>Cancel this booking</a>\r\n";
	bookingmail($eml, 'Please confirm your room booking', $themail);
}
$stmt->free_result();
$stmt->close();

// Bookings for 3 days hence that are still provisional are changed to lapsed (status = 3)

$query = "UPDATE booking";
$query = $query." SET status = 3";
$query = $query." WHERE status = 1";
$query = $query." AND ( DAYOFYEAR(booking.bookingdate) = DAYOFYEAR(NOW()+INTERVAL 3 DAY) )";
$stmt = $link->prepare($query); 
$stmt->execute();
aggslog("dailymailing.php: changed ".$stmt->affected_rows." to LAPSED status.");
$stmt->free_result();
$stmt->close();

aggslog("dailymailing.php: job ended");

?>
