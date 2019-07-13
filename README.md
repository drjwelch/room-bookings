# room-bookings

A quick and dirty room booking system put together for school.

Uses a calendar class from http://www.planetphp.co.uk/free-php-booking-slots-calendar/ which seems to no longer be available.

Currently connects to a mysql database on the same linux box which also handles the email (postfix) and runs an overnight php script (cron job) to mail reminders.

Features:
Book rooms for given period (teaching or non)
Rooms and periods configurable
Captures booking data like teacher, class, reason for use
Provisional bookings / confirm nearer the time to maximise usage
Unconfirmed bookings lapse and allow re-booking Users emailed at each stage of the process e.g. click this link to confirm booking

To do:
Proper admin page for multiple/repeat bookings
Tidy up all the code - curently v implementation-specific and needs generalising
Access from home - net bods need to give us an IP and do some NATing
Sell time on it for generating spam ... not really
