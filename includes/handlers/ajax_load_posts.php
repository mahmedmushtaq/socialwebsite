<?php
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Post.php");

$limit = 10;

$posts = new Post($connect,$_REQUEST['userLoggedIn']);
$posts->loadPostsFriends($_REQUEST,$limit);

?>