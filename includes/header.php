<?php  
require 'config/config.php';

include("includes/classes/Post.php");
include("includes/classes/User.php");
include("includes/classes/Message.php");
include("includes/classes/Notification.php");



if(isset($_SESSION['username']))
{
	$userLoggedIn = $_SESSION['username'];
	$user_details_query = mysqli_query($connect,"SELECT * FROM users WHERE username='$userLoggedIn'");
	$user = mysqli_fetch_array($user_details_query);
}
else
{
	header("Location: register.php");
}

?>

<html>
<head>
	<title>Welcome to Zezign</title>

	<!-- script-->
 
	 <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
 <script src="assets/js/bootstrap.js"></script> 

 
 <script src="assets/js/bootbox.min.js"></script>
<script src="assets/js/demo.js"></script>
  <script src="assets/js/jquery.jcrop.js"></script>
  <script src="assets/js/jcrop_bits.js"></script>
 
 <!-- css-->
 <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
 <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
 
 <link rel="stylesheet" type="text/css" href="assets/css/style.css">
<link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />

</head>
<body>
  <div class="top_bar" style="">
  <div class="logo">
  <?php //$name="zezign";echo $name;?>
<a href="index.php">Zezign</a>

  </div>
<div class="search">

<form action="search.php" method="GET" name="search_form">

<input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search...." autocomplete="off" id="search_text_input">


<div class="button_holder">
  <img src="assets/images/icons/magnifying_glass.png">


</div>


</form>

<div class="search_results"></div>

<div class="search_results_footer_empty">


</div>

</div>




  <nav>

<?php 
//unread messages
$messages = new Message($con,$userLoggedIn);
$num_messages = $messages->getUnreadNumber();


//unread notification

$notifications = new Notification($con,$userLoggedIn);
$num_notifications = $notifications->getUnreadNumber();

//unread friend requests

$user_obj = new User($con,$userLoggedIn);
$num_requests = $user_obj->getNumberofFriendRequests();


 

 ?>


 <a href="<?php echo $userLoggedIn; ?>"  ><!--
 <i class="material-icons" style="font-size:22px;">&#xe439;</i>-->
 <?php echo $user['first_name'] ?> 
 </a>
  <a href="index.php"><i style="font-size:22px;" class="fa">&#xf015;</i></a>
 <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn;?>', 'notification')"><i class="material-icons" style="font-size:22px;">&#xe80b;</i>
<?php
if($num_notifications > 0)//notification icon
echo '<span class="notification_badge" id="unread_notification">'.$num_notifications.'</span>';


?>


 </a>


  <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn;?>', 'message')"><i class="material-icons" style="font-size:22px;">&#xe0b7;</i>
<?php
if($num_messages > 0)
echo '<span class="notification_badge" id="unread_message">'.$num_messages.'</span>';


?>

 

  </a>
 <a href="requests.php"><i class="material-icons" style="font-size:22px;">&#xe7fb;</i>

<?php
if($num_requests > 0)
echo '<span class="notification_badge" id="unread_notification">'.$num_requests.'</span>';


?>


</a>

 <a href="settings.php">
<i class="material-icons" style="font-size:24px;">&#xe5d4;</i>


 </a>
  <a href="includes/handlers/logout.php"><i class="material-icons" style="font-size:22px;">&#xe5dd;</i></a>



  </nav>


<div class="dropdown_data_window" style="height:0px;border:none;"></div>
<input type="hidden" id="dropdown_data_type" value=""> 

  </div>


 <script >
  
   
    

 var userLoggedIn = '<?php echo $userLoggedIn; ?>';

  $(document).ready(function() {

    $('.dropdown_data_window').scroll(function() {
      var inner_height = $('.dropdown_data_window').innerHeight(); //Div containing data
      var scroll_top = $('.dropdown_data_window').scrollTop();
      var page = $('.dropdown_data_window').find('.nextPageDropdownData').val();
      var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

      if ((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false') {

        var pageName; //Holds name of page to send ajax request to
        var type = $('#dropdown_data_type').val();


        if(type == 'notification')
          pageName = "ajax_load_notifications.php";
        else if(type = 'message')
          pageName = "ajax_load_messages.php"


        var ajaxReq = $.ajax({
          url: "includes/handlers/" + pageName,
          type: "POST",
          data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
          cache:false,

          success: function(response) {
            $('.dropdown_data_window').find('.nextPageDropdownData').remove(); //Removes current .nextpage 
            $('.dropdown_data_window').find('.noMoreDropdownData').remove(); //Removes current .nextpage 


            $('.dropdown_data_window').append(response);
          }
        });

      } //End if 

      return false;

    }); //End (window).scroll(function())


  });

  


   </script>





  <div class="wrapper">



<!-- dropdown-->
	<!--- comments a bootstrap  

  <div class="btn-group" role="group">
    <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      Dropdown
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
      <li><a href="#">Dropdown link</a></li>
      <li><a href="#">Dropdown link</a></li>
    </ul>
  </div>
</div>
	-->