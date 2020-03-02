<?php
include ("../../config/config.php");
session_start();
session_destroy();


if(isset($_SESSION['username']))
{
	$userLoggedIn = $_SESSION['username'];
	$user_details_query = mysqli_query($connect,"SELECT * FROM users WHERE username='$userLoggedIn'");
	$user = mysqli_fetch_array($user_details_query);
}

  $user_offline_query = mysqli_query($connect,"UPDATE users SET online='no' WHERE username='$userLoggedIn'");
  if($user_offline_query)
header("Location: ../../register.php")

?>