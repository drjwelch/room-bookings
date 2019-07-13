<?php

// ajax-gettnames.php

// Script to return via ajax a list of teachers SIMS codes and emails for select boxes

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include('php/aggsbookings.php'); 

$tcode = mysqli_real_escape_string($link, $_GET['tcode']);

$stmt = $link->prepare("SELECT teacherid,name,emailaddress FROM teacher"); 
$stmt->bind_result($tid,$tn,$teml);	
$stmt->execute();
$stmt->store_result();
echo"
				  <option value=''>Choose teacher code ...</option>";
while($stmt->fetch()) {
	echo"
				  <option value='$tid' data-email='$teml'>$tn</option>";
}
$stmt->free_result();
$stmt->close();
		
?>