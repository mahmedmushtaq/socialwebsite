<?php 
include("includes/header.php");
//include("includes/classes/User.php");

 


?>

<div class="main_column column" id="main_column">
<h4>Friend Requests</h4>

<?php
$query = mysqli_query($connect,"SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
if(mysqli_num_rows($query) == 0)
{
	echo "You have no friend requests at this time";
}
else
{
	while($row = mysqli_fetch_array($query))
	{
		$user_from = $row['user_from'];
		$user_from_obj = new User($connect,$user_from);

		echo $user_from_obj->getFirstAndLastName()." send you a friend request!";

		$user_from_friend_array = $user_from_obj->getFriendArray();

		if(isset($_POST['accept_request' . $user_from]))
		 
		{//this query add both client as a friend
			$add_friend_query = mysqli_query($connect,"UPDATE users SET friend_array=CONCAT(friend_array,'$user_from,') WHERE username='$userLoggedIn'");
            $add_friend_query = mysqli_query($connect,"UPDATE users SET friend_array=CONCAT(friend_array,'$userLoggedIn,') WHERE username='$user_from'");

            $delete_query = mysqli_query($connect,"DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
            echo "You are now friends!";
            header("Location: requests.php");
		}
		if(isset($_POST['ignore_request' . $user_from]))
		{
			  $delete_query = mysqli_query($connect,"DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
            echo "Requests ignored!";
            header("Location: requests.php");
		}

		?>
<form action="requests.php" method="post">
		<input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" value="Accept"> 
		<input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" value="Ignore"> 

</form>

		<?php
	}
}

?>


</div>
<div class="user_details column" style="height:400px;overflow-y: auto;">
<?php 

$queryofofline = mysqli_query($connect,"SELECT * FROM users WHERE online='yes' ");
 

 
while( $row = mysqli_fetch_array($queryofofline))
{
	 
	  
	  

	  $userOnline = $row['username'];

	 $user_obj = new User($connect,$userOnline);
	 
      
	 if($user_obj->isClosed() == false)
	 {
	 	$name = $user_obj->getFirstAndLastName();
	 	$profile_pic = $user_obj->getProfilePic();
	  	if($userLoggedIn != $userOnline){
          echo  "<div class='information'><img src=".$profile_pic." width='60'  >&nbsp;&nbsp;&nbsp;&nbsp;".$name ."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;online<br><br><hr></div>";
           
      

       
   }


 
    }
     
  
   

}
if($row = mysqli_num_rows($queryofofline) == 1)
echo "nobody online exept you"
 

 
 
         



 



?>


</div>