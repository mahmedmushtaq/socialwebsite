<?php
ob_start(); //Turns on output buffering 
session_start();

$timezone = date_default_timezone_set("Asia/Karachi");

$connect =  ///mysqli_connect("localhost","msvoikeo_ahmed","ltV1aXwIo8rN","msvoikeo_social"); 
    mysqli_connect("localhost", "root", "", "social"); //Connection variable
$con = $connect;

if(mysqli_connect_errno()) 
{
	echo "Failed to connect: " . mysqli_connect_errno();
}

?>