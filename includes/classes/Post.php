<?php
 
 

class Post
{
	private $user_obj;
	private $con;
	public function __construct($con,$user)
	{
        $this->con = $con;
        
        $this->user_obj = new User($con,$user);
	}
	public function submitPost($body,$user_to,$imageName){
		 $body = strip_tags($body);
		 $body = mysqli_real_escape_string($this->con,$body);
		 $check_empty = preg_replace('/\s+/', '', $body);
		 if($check_empty != "" || ($check_empty == "" && $imageName != ""))
		 {

           $body_array = preg_split("/\s+/", $body);

           foreach ($body_array as $key=> $value) {

           	if(strpos($value,"www.youtube.com/watch?v=") !== false)
           	{
                  $link = preg_split("!&!", $value);

           		$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
           		$value = "<br><iframe width=\'420\' height=\'315\' src=\'".$value."\'></iframe><br>";
           		$body_array[$key] = $value;
           	}
           
           }

           $body = implode(" ", $body_array);





		 	$date_added = date("Y-m-d H:i:s");
		 	$added_by = $this->user_obj->getUsername();
		 	 

		 	if($user_to == $added_by)
		 	{
		 		$user_to = "none";
		 	}

		 	



         //insertpost
		 	$query = mysqli_query($this->con,"INSERT INTO posts VALUES('','$body','$added_by','$user_to','$date_added','no','no','0','$imageName')");
		 	$returned_id = mysqli_insert_id($this->con);

        //this query run after insert posts to increase number of posts

		 	 

 

		 	//insert notification

		 /*	if($user_to != 'none'){
		 		$notification = new Notification($this->con,$added_by);
		 		$notification->insertNotification($returned_id,$user_to,"profile_post");
		 	}*/

		 	if($user_to != 'none') {
				$notification = new Notification($this->con, $added_by);
				$notification->insertNotification($returned_id, $user_to, "profile_post");
			}

          //insert notification to all my friends

			 

             $user_details_query = mysqli_query($this->con,"SELECT * FROM users WHERE username='$added_by'");
             $user = mysqli_fetch_array($user_details_query);
             $yourFriendArray = $user['friend_array'];
                $notification = new Notification($this->con, $added_by);
 
		     $user_array_explode = explode(",", $yourFriendArray);
					 
			foreach($user_array_explode as $friend) {
			    $notification->insertNotification($returned_id, $friend , "post");
			}

			//update posts set
			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;
			$update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");
				 
				    
               

  }
	}
 
	public function loadPostsFriends($data,$limit)//this $data k ander page ki request aye gi $limit k ander k kitni pic ya post reload honi hain
	{

								$page = $data['page'];
								$userLoggedIn = $this->user_obj->getUsername();

												     if($page == 1)
												     	$start = 0;
												     else
												     	$start = ($page -1) * $limit;//agr page ki request dusi bar ai to yah us mein is tarah value add kry ga 2-1*10=10 or yah thus mazeed page reload krny ka order jry kry ga


														$str = "";
														$data = mysqli_query($this->con,"SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");
														
												    $data_query = $data;
												    if(mysqli_num_rows($data_query)){


												  $num_iterations = 0;
												  $count = 1;


					while($row = mysqli_fetch_array($data))
				 {
														$id = $row['id'];
														$body = $row['body'];
														$added_by = $row['added_by'];
                                                        $user_to_two = $row['user_to'];
														$date_time = $row['date_added'];
                                                       $imagePath = $row['image'];
														//prepare user_to string so it can be included even if
														//not posted to a user
														if($row['user_to'] == "none")
														{
															$user_to = "";
														}
														else
														{
															$user_to_obj = new User($this->con,$row['user_to']);
															$user_to_name = $user_to_obj->getFirstAndLastName();

															$user_to = "to <a href='".$row['user_to']."'>".$user_to_name ."</a>";
											                   
											}
														//check if user who posted ,has ther accocount closed
														$added_by_obj = new User($this->con,$added_by);
														if($added_by_obj->isClosed())
														{
															continue;
														}

											        $user_logged_obj = new User($this->con,$userLoggedIn);
											        if($user_logged_obj->isFriend($added_by)){


											/*
											//first if e ka matlab hy k jb hum shuru mein page reload krein gy to tab page sirf aik bar reload ho jaye ga
											//or jb  reload kr dy ga to 10 post pey ja k rk jaye ga q k hm ney limit 10 ki set ki hy or page sirf first reload mein 10 post reload kry ga or ruk jaye ga or mazeed reload krny
											//start variable ki value brha dy ga while loop 0 sy jis sy yah ho ga k mazeed 10 posts reload ho jain gi
											//jesy he reload ho gi 10 posts jo second if hy wo loop ko break kry ga or mazeed reload hony sy bachaye ga
											//or second if k bad else hy wo tab execute hota rhy ga jb tk count 10 ko nhi phnch jata 	*/		

											            if($num_iterations++ < $start)
											          	continue;
											   
											             if($count > $limit)
											             	break;
											            else
											             	$count++;
											 
											      if($userLoggedIn == $added_by)
											      {
											       
											      $delete_button = "<button class='delete_button btn-danger' id='post$id' >X</button>";
											      }
											      else
											       	$delete_button = "";


														$user_details_query = mysqli_query($this->con,"SELECT first_name,last_name,profile_pic FROM users WHERE username='$added_by'");
														$user_row = mysqli_fetch_array($user_details_query);
											         $first_name = $user_row['first_name'];
											         $last_name = $user_row['last_name'];
											       $profile_pic= $user_row['profile_pic'];
											 
											?>
											       
											 <script > 
																	function toggle<?php echo$id; ?>() {
																		 var target = $(event.target);
																		 if(!target.is("a"))
																		 {
																		 				 	var element = document.getElementById("toggleComment<?php echo $id; ?>");

																			if(element.style.display == "block") 
																				element.style.display = "none";
																			else 
																				element.style.display = "block";

																		  
																		 }

															
																		 
																	}

																</script>

											     
											<?php

											$comments_check = mysqli_query($this->con,"SELECT * FROM comments WHERE post_id='$id'");
											$comments_check_num = mysqli_num_rows($comments_check);

														//timeframe
														$date_time_now = date('Y-m-d H:i:s');
														$start_date = new DateTime($date_time);
														$end_date = new DateTime($date_time_now);
														$interval = $start_date->diff($end_date);
														if($interval->y >= 1)
														{
															if($interval == 1)
															{
																$time_message = $interval->y ." year ago";
															}
															else
																$time_message = $interval->y ." years ago";
														}

														else if($interval-> m >= 1)
														{
															 if($interval->d == 0)
															 {
															 	$days = " ago";
															 }
															 else if($interval->d == 1)
															 	$days = $interval->d." day ago";
															 else
															 		$days = $interval->d." days ago";
											            if($interval->m == 1)
															{
																$time_message = $interval->m ." month ".$days;
															}
															else
																$time_message = $interval->m ." month ".$days;
														



														}
														else if($interval->d >= 1)
														{
															if($interval->d == 1)
															 	 $time_message = " Yesterday";
															 else
															 		$time_message = $interval->d." days ago";
														}
														else if($interval->h >= 1)
														{
															if($interval->h == 1)
															 	 $time_message = $interval->h ." hour ago";
															 else
															 		$time_message = $interval->h." hours ago";
														

														}

															else if($interval->i >= 1)
														{
															if($interval->i == 1)
															 	 $time_message = $interval->i ." minute ago";
															 else
															 		$time_message = $interval->i." minutes ago";
														

														}

														else
														{
															if($interval->s <= 30)
															 	 $time_message = " Just now";
								 else
								 $time_message = $interval->s." seconds ago";
														
                              } 

			 if($imagePath != ""){
					 $imageDiv = "<div class='postedImage'>
                <img src='$imagePath'>
				 </div>";
			 }
		 else{
			 $imageDiv = "";
			 }

			// if($added_by == $userLoggedIn || $userLoggedIn == $user_to)
       
         
        if($added_by == $userLoggedIn || $userLoggedIn == $user_to_two )
		 {
		 	 $button="<a href='deletepost.php?id=".$id."' style='text-decoration:none;'>X</a>";
 
			 }
		 else
		 {
			 $button = "";
				 }
        
					 $str .= "

						 <div class='status_post'  >
							 <div class='post_profile_pic' >
							 <img src='$profile_pic' width=50  >
							  </div>
								  <div class='posted_by' >
								 <a href='$added_by'>$first_name $last_name</a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                 &nbsp;&nbsp;&nbsp;&nbsp;<b style='float:right;margin-top:0px;'>".$button."</b>
								 </div>
                         <div id='post_body'>
							  $body
							 <br>
                               $imageDiv
                               <br>
								 <br>
								 </div>
								 <div class='newsfeedPostOptions'   >
									 <b  class='bold_comment'  onClick='javascript:toggle$id()'>Comments($comments_check_num)&nbsp;&nbsp;&nbsp;&nbsp;</b>
                            <iframe class='like_frame' src='like.php?post_id=$id' scrolling='no'></iframe>
                          </div> 
                           </div>
							  <div class='post_comment' id='toggleComment$id' style='display:none;' >
							 <iframe src='comment_frame.php?post_id=$id' class='comment_iframe' id='comment_iframe' frameborder='0'></iframe>
							 </div>
                            <hr>";//
				  }
												?>
						 
			 <?php		 
												

											//the div after newsfeedPostOption it make me own that comments only show when click on it comments div
														
				 }//ends while loop

				 if($count > $limit)//es ka mtlb hy k agr count mean posts br gai hain to yah mazeed us sy aik or page ki request bhajy ga jquery ajax ko
				 {
					 $str .="<input type='hidden' class='nextPage' value='".($page + 1)."'>
				 <input type='hidden' class='noMorePosts' value='false'>";
 }
				 else//or agr nhi bachi to to yah request nhi bhajy ga js loop b execute nhi ho ga or na kch or ho js sy sy darj zail text show ho jaye ga
				 {
			 $str .="<input type='hidden' class='noMorePosts' value='true'><p style='text-align:center;'>no
						 more posts to show !</p>";

				 }
 }
				 echo $str;
}









											// load profile post start here hehe












public function  loadProfilePosts($data,$limit)//this $data k ander page ki request aye gi $limit k ander k kitni pic ya post reload honi hain
	{
     $page = $data['page'];
     $profileUser = $data['profileUsername'];
     $userLoggedIn = $this->user_obj->getUsername();

     if($page == 1)
     	$start = 0;
     else
     	$start = ($page -1) * $limit;//agr page ki request dusi bar ai to yah us mein is tarah value add kry ga 2-1*10=10 or yah thus mazeed page reload krny ka order jry kry ga


		$str = "";
		$data = mysqli_query($this->con,"SELECT * FROM posts WHERE deleted='no' AND((added_by='$profileUser' AND user_to='none') OR user_to='$profileUser') ORDER BY id DESC");
		
    $data_query = $data;
    if(mysqli_num_rows($data_query)){


  $num_iterations = 0;
  $count = 1;


		while($row = mysqli_fetch_array($data))
		{
			$id = $row['id'];
			$body = $row['body'];
			$added_by = $row['added_by'];
            $user_to = $row['user_to'];
			$date_time = $row['date_added'];
			$imagePath = $row['image'];

			//prepare user_to string so it can be included even if
			//not posted to a user
			 
			 
        
/*
//first if e ka matlab hy k jb hum shuru mein page reload krein gy to tab page sirf aik bar reload ho jaye ga
//or jb  reload kr dy ga to 10 post pey ja k rk jaye ga q k hm ney limit 10 ki set ki hy or page sirf first reload mein 10 post reload kry ga or ruk jaye ga or mazeed reload krny
//start variable ki value brha dy ga while loop 0 sy jis sy yah ho ga k mazeed 10 posts reload ho jain gi
//jesy he reload ho gi 10 posts jo second if hy wo loop ko break kry ga or mazeed reload hony sy bachaye ga
//or second if k bad else hy wo tab execute hota rhy ga jb tk count 10 ko nhi phnch jata 	*/		

            if($num_iterations++ < $start)
          	continue;
   
             if($count > $limit)
             	break;
            else
             	$count++;
 
      if($userLoggedIn == $added_by)
      {
       
      $delete_button = "<button class='delete_button btn-danger' id='post$id' >X</button>";
      }
      else
       	$delete_button = "";


			$user_details_query = mysqli_query($this->con,"SELECT first_name,last_name,profile_pic FROM users WHERE username='$added_by'");
			$user_row = mysqli_fetch_array($user_details_query);
         $first_name = $user_row['first_name'];
         $last_name = $user_row['last_name'];
       $profile_pic= $user_row['profile_pic'];
 
?>
       
 <script > 
						function toggle<?php echo$id; ?>() {
							 var target = $(event.target);
				  if(!target.is("a"))
				 {
				 var element = document.getElementById("toggleComment<?php echo $id; ?>");

			 if(element.style.display == "block") 
			 element.style.display = "none";
			 else 
			 element.style.display = "block";

							  
			 }

				
							 
			 }

					</script>

     
<?php

$comments_check = mysqli_query($this->con,"SELECT * FROM comments WHERE post_id='$id'");
$comments_check_num = mysqli_num_rows($comments_check);

			//timeframe
			$date_time_now = date('Y-m-d H:i:s');
			$start_date = new DateTime($date_time);
			$end_date = new DateTime($date_time_now);
			$interval = $start_date->diff($end_date);
			if($interval->y >= 1)
			{
				if($interval == 1)
				{
					$time_message = $interval->y ." year ago";
				}
				else
					$time_message = $interval->y ." years ago";
			}

			else if($interval-> m >= 1)
			{
				 if($interval->d == 0)
				 {
				 	$days = " ago";
				 }
				 else if($interval->d == 1)
				 	$days = $interval->d." day ago";
				 else
				 		$days = $interval->d." days ago";
            if($interval->m == 1)
				{
					$time_message = $interval->m ." month ".$days;
				}
				else
					$time_message = $interval->m ." month ".$days;
			



			}
			else if($interval->d >= 1)
			{
				if($interval->d == 1)
				 	 $time_message = " Yesterday";
				 else
				 		$time_message = $interval->d." days ago";
			}
			else if($interval->h >= 1)
			{
				if($interval->h == 1)
				 	 $time_message = $interval->h ." hour ago";
				 else
				 		$time_message = $interval->h." hours ago";
			

			}

				else if($interval->i >= 1)
			{
				if($interval->i == 1)
				 	 $time_message = $interval->i ." minute ago";
				 else
				 		$time_message = $interval->i." minutes ago";
			

			}

			else
			{
				if($interval->s <= 30)
				 	 $time_message = " Just now";
				 else
				 		$time_message = $interval->s." seconds ago";
			

			
			}//float:left;margin-right:7px;
			/*
             //status post

             style='width: 96%;
	font-size: 15px;
	padding: 0px 5px;
	min-height: 75px;' 


	//post_profile_pic
	style='	float: left;margin-right: 17px;'

	//posted by
	style='color:#ACACAC;'

	//

	//it occur first status post onClick='javascript:toggle$id()' //twitor paste it with status post but i post it with comments

			
  $delete_button;*/


  if($imagePath != ""){
  $imageDiv = "<div class='postedImage'>
    <img src='$imagePath'>
 </div>";
 }
 else{
		$imageDiv = "";
}

if($added_by == $userLoggedIn || $userLoggedIn == $user_to)
 {
 $button="<a href='deletepost.php?id=".$id."' style='text-decoration:none;'>X</a>";
 } else
 {
 $button = "";
 }

			$str .= "

			<div class='status_post'  
			  >
          <div class='post_profile_pic' >
          <img src='$profile_pic' width=50  >
         </div>
      <div class='posted_by' >
    <a href='$added_by'>$first_name $last_name</a>   &nbsp;&nbsp;&nbsp;&nbsp;$time_message
  


    </div>

    <div id='post_body'>
  $body<b style='float:right'>".$button."</b>
  <br>
  $imageDiv
  <br>
  <br>
    </div>
    
<div class='newsfeedPostOptions'   >
<b  class='bold_comment'  onClick='javascript:toggle$id()'>Comments($comments_check_num)&nbsp;&nbsp;&nbsp;&nbsp;</b>

<iframe class='like_frame' src='like.php?post_id=$id' scrolling='no'></iframe>

</div>


   </div>
  <div class='post_comment' id='toggleComment$id' style='display:none;' >
								<iframe src='comment_frame.php?post_id=$id' class='comment_iframe' id='comment_iframe' frameborder='0'></iframe>
							</div>

   <hr>";//
     
	?>
<script >
/*$(document).ready(function(){
   $('#post<?php echo $id;?>').on('click',function(){
bootbox.confirm("Do you want to delete this post?>",function(result){

$.post("includes/form_handlers/delete_post.php?post_id<?php echo $id; ?>",{result:result});
if(result)
{
	location.reload();

}


});
   });

});*/


</script>
	<?php		 
	

//the div after newsfeedPostOption it make me own that comments only show when click on it comments div
			
		}//ends while loop

		if($count > $limit)//es ka mtlb hy k agr count mean posts br gai hain to yah mazeed us sy aik or page ki request bhajy ga jquery ajax ko
		{
			$str .="<input type='hidden' class='nextPage' value='".($page + 1)."'>
			<input type='hidden' class='noMorePosts' value='false'>";

		}
		else//or agr nhi bachi to to yah request nhi bhajy ga js loop b execute nhi ho ga or na kch or ho js sy sy darj zail text show ho jaye ga
		{
			$str .="<input type='hidden' class='noMorePosts' value='true'><p style='text-align:center;'>no
			more posts to show !</p>";

		}
	}

		echo $str;
	}



//singpost function start here





	public function getSinglePost($post_id){
 
         
								$userLoggedIn = $this->user_obj->getUsername();


                         $opened_query = mysqli_query($this->con,"UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");
								$str = "";
								$data = mysqli_query($this->con,"SELECT * FROM posts WHERE deleted='no' AND id='$post_id' ORDER BY id DESC");
														
								$data_query = $data;
								if(mysqli_num_rows($data_query)){
                                   $row = mysqli_fetch_array($data);

														$id = $row['id'];
														$body = $row['body'];
														$added_by = $row['added_by'];
														$date_time = $row['date_added'];
                                                          $imagePath = $row['image'];
														//prepare user_to string so it can be included even if
														//not posted to a user
														if($row['user_to'] == "none")
														{
															$user_to = "";
														}
														else
														{
															$user_to_obj = new User($this->con,$row['user_to']);
															$user_to_name = $user_to_obj->getFirstAndLastName();

															$user_to = "to <a href='".$row['user_to']."'>".$user_to_name ."</a>";
											                   
											}
														//check if user who posted ,has ther accocount closed
														$added_by_obj = new User($this->con,$added_by);
														if($added_by_obj->isClosed())
														{
															return;
														}

											        $user_logged_obj = new User($this->con,$userLoggedIn);
											        if($user_logged_obj->isFriend($added_by)){

 
											   
											              
											 
											      if($userLoggedIn == $added_by)
											      {
											       
											      $delete_button = "<button class='delete_button btn-danger' id='post$id' >X</button>";
											      }
											      else
											       	$delete_button = "";


														$user_details_query = mysqli_query($this->con,"SELECT first_name,last_name,profile_pic FROM users WHERE username='$added_by'");
														$user_row = mysqli_fetch_array($user_details_query);
											         $first_name = $user_row['first_name'];
											         $last_name = $user_row['last_name'];
											       $profile_pic= $user_row['profile_pic'];
											 
											?>
											       
											 <script > 
																	function toggle<?php echo$id; ?>() {
																		 var target = $(event.target);
																		 if(!target.is("a"))
																		 {
																		 				 	var element = document.getElementById("toggleComment<?php echo $id; ?>");

																			if(element.style.display == "block") 
																				element.style.display = "none";
																			else 
																				element.style.display = "block";

																		  
																		 }

															
																		 
																	}

																</script>

											     
											<?php

											$comments_check = mysqli_query($this->con,"SELECT * FROM comments WHERE post_id='$id'");
											$comments_check_num = mysqli_num_rows($comments_check);

														//timeframe
														$date_time_now = date('Y-m-d H:i:s');
														$start_date = new DateTime($date_time);
														$end_date = new DateTime($date_time_now);
														$interval = $start_date->diff($end_date);
														if($interval->y >= 1)
														{
															if($interval == 1)
															{
																$time_message = $interval->y ." year ago";
															}
															else
																$time_message = $interval->y ." years ago";
														}

														else if($interval-> m >= 1)
														{
															 if($interval->d == 0)
															 {
															 	$days = " ago";
															 }
															 else if($interval->d == 1)
															 	$days = $interval->d." day ago";
															 else
															 		$days = $interval->d." days ago";
											            if($interval->m == 1)
															{
																$time_message = $interval->m ." month ".$days;
															}
															else
																$time_message = $interval->m ." month ".$days;
														



														}
														else if($interval->d >= 1)
														{
															if($interval->d == 1)
															 	 $time_message = " Yesterday";
															 else
															 		$time_message = $interval->d." days ago";
														}
														else if($interval->h >= 1)
														{
															if($interval->h == 1)
															 	 $time_message = $interval->h ." hour ago";
															 else
															 		$time_message = $interval->h." hours ago";
														

														}

															else if($interval->i >= 1)
														{
															if($interval->i == 1)
															 	 $time_message = $interval->i ." minute ago";
															 else
															 		$time_message = $interval->i." minutes ago";
														

														}

														else
														{
															if($interval->s <= 30)
															 	 $time_message = " Just now";
															 else
															 		$time_message = $interval->s." seconds ago";

														

														
											 }
								 if($imagePath != ""){
							  $imageDiv = "<div class='postedImage'>
							    <img src='$imagePath'>
							 </div>";
							 }
							 else{
									$imageDiv = "";
							}

										 $str.="

														<div class='status_post'  >
											          <div class='post_profile_pic' >
											          <img src='$profile_pic' width=50  >
											         </div>
											      <div class='posted_by' >
											    <a href='$added_by'>$first_name $last_name</a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
											  
											  


											    </div>

											    <div id='post_body'>
											  $body
											  <br>
											 $imageDiv
											  <br>
											  <br>
											    </div>
											    
											<div class='newsfeedPostOptions'   >
											<b  class='bold_comment'  onClick='javascript:toggle$id()'>Comments($comments_check_num)&nbsp;&nbsp;&nbsp;&nbsp;</b>

											<iframe class='like_frame' src='like.php?post_id=$id' scrolling='no'></iframe>

											</div>


											   </div>
											  <div class='post_comment' id='toggleComment$id' style='display:none;' >
																			<iframe src='comment_frame.php?post_id=$id' class='comment_iframe' id='comment_iframe' frameborder='0'></iframe>
																		</div>

											   <hr>";//
											     
												?>
											<script >
											 


											</script>
												<?php
}
 else{
echo "<p>You cannot see this post because you are not friend with this users</p>";
return;
}		 
}
else{
	echo "<p>No post found.If you clicked a link.It may be broken</p>";
return;
}

				echo $str;
}




}


?>

 