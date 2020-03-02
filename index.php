<?php include("includes/header.php");
 
 


if(isset($_POST['post']))
{

     $uploadOk = 1;
     $imageName = $_FILES['fileToUpload']['name'];
     $errorMessage = "";

     if($imageName != "")
     {
      //agr image upload hui to us k bad yah condition start ho gi
      /* sb sy pahly hm directory dein gy image upload hony wali ko phr hm us directory k andr us img ka unqiue nam store krwayein gy

      or phr us ko permant directory dein gy us k bad file ka size check krein gy agr wo  kafi bari hui to usy upload nhi krwein gy 

      or phr us ka externsion check kry gy agr externsion b given condition sa na milta hua to phr b us ko upload nhi krwein gy 

      is k bad agr sb kch false raha to 

      us k bad image upload krwein gy

      is sb ka patah humein $uploadOk sy chly ga jo bataye ga agr wo one hua mean k kch nhi glt agr wo 0 hua to kahin problem hy image upload nhi ho gi*/
           $targetDir = "assets/images/posts/";
           $imageName = $targetDir . uniqid() . basename($imageName);
           $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

           if($_FILES['fileToUpload']['size'] > 10000000)
           {
              $errorMessage = "Sorry Your file is too large";
              $uploadOk = 0;

           }
             if(strtolower($imageFileType) != "jpeg" &&  strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg")
             {
               $errorMessage = "Sorry, only jpeg,jpg and png files to allowed";
              $uploadOk = 0;
             }

             if($uploadOk)
             {
               if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName))
               {
                      //image upload okay
               }
               else
               {
                //image did not upload
                $uploadOk = 0;
               }
             }


     }
     if($uploadOk )
     {
       $post = new Post($connect,$userLoggedIn);
  $post->submitPost($_POST['post_text'],'none', $imageName);
     }
     else{
          echo "<div style='text-align:center;' class='alert alert-error'>

      $errorMessage

          </div>";

     }



 // $post = new Post($connect,$userLoggedIn);
  //$post->submitPost($_POST['post_text'],'none');
}


?>
	 

	 <div class="user_details column">
     <a href="<?php echo $userLoggedIn; ?>"><img src="<?php echo $user['profile_pic'];?>"></a>
  <div class="user_details_left_right">
  <a href="<?php echo $userLoggedIn; ?>" >

  <?php
echo " ".$user['first_name']." ".$user['last_name'];

  ?>
</a>
<br>

<?php echo "Posts: ".$user['num_posts']."<br>";
       echo " Likes: ".$user['num_likes'];?>

   </div>
	 </div>

	 <div class="main_column column">
  <form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">

    <input type="file" name="fileToUpload" id="fileToUpload">
  	<textarea name="post_text" id="post_text" placeholder="Got something to say!"></textarea>
  	<input type="submit" name="post" id="post_button" value="Post">
  	<hr> 


  </form>
   <!--
<?php 
// $user_obj = new User($connect,$userLoggedIn);
 //$post = new Post($connect,$userLoggedIn);
 // $post->loadPostsFriends();

?>-->
<div class="posts_area"></div>
<img id="loading"src="assets/images/icons/loadingone.gif">

	 </div>
   <script >
   var userLoggedIn = '<?php echo $userLoggedIn;?>';
  $(document).ready(function(){
$('#loading').show();

  

  $.ajax({
  url: "includes/handlers/ajax_load_posts.php",
  type: "POST",
  data: "page=1&userLoggedIn=" + userLoggedIn,
  cache:false,
  success: function(data){
    $('#loading').hide();
    $('.posts_area').html(data);
  }


  });

$(window).scroll(function(){
var height = $('.posts_area').height();
var scroll_top = $(this).scrollTop();
var page = $('.posts_area').find('.nextPage').val();
var noMorePosts = $('.posts_area').find('.noMorePosts').val();
if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts =='false')
{
  $('#loading').show();

  //original ajax request

   var ajaxReq  = $.ajax({
  url: "includes/handlers/ajax_load_posts.php",
  type: "POST",
  data: "page="+ page + "&userLoggedIn=" + userLoggedIn,
  cache:false,
  success: function(response){
   $('.posts_area').find('.nextPage').remove();
   $('.posts_area').find('.noMorePosts').remove();


    $('#loading').hide();
    $('.posts_area').append(response);
  }


  });
}//end if
return false;

});// end (window).scroll(function)


  });

 
  


   </script>


	</div>
</body>
</html>