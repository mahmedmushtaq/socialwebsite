<?php
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Post.php");
include("../classes/Notification.php");

$limit = 10;

$posts = new Post($connect,$_REQUEST['userLoggedIn']);
$posts->loadProfilePosts($_REQUEST,$limit);

?>