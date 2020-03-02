<?php 
include "config/config.php";
if(isset($_GET['id']))
{
  $id = $_GET['id'];
	 
}
$query_post = mysqli_query($con,"DELETE FROM posts WHERE id='$id'");
 
$query_comments = mysqli_query($con,"DELETE FROM comments WHERE post_id='$id'");
 
 
$query_likes = mysqli_query($con,"DELETE FROM likes WHERE post_id='$id'");

if($query_post && $query_comments && $query_likes)
   header("Location: index.php");
 
 

 ?>