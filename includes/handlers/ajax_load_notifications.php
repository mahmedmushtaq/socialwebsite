<?php 
 include("../../config/config.php");
 

include("../classes/Notification.php");
include("../classes/User.php");

$limit = 7;

$Notification = new Notification($connect,$_REQUEST['userLoggedIn']);
echo $Notification->getNotifications($_REQUEST,$limit);



 ?>