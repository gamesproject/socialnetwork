<?php
  include('classes/database.php');
  include('classes/login-info.php');
  include('classes/post.php');

  $username ="";
  $isFollowing= False;

  if(Login::isUserLoggedIn()){
      $loggedinUsername= database::query('SELECT username FROM users WHERE id=:loggedInID', array(':loggedInID'=>Login::isUserLoggedIn()))[0]['username'];
  }
  else{
    die("LOG IN PLEASE");
  }
  //if username is available
  if(isset($_GET['username'])){
          //if the user exists
          if(database::query('SELECT username FROM users WHERE username=:username' ,array(':username'=>$_GET['username']))){
            $firstname=database::query('SELECT firstname FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['firstname'];
            $username=database::query('SELECT username FROM users WHERE username=:username' ,array(':username'=>$_GET['username']))[0]['username'];
            $user_id= database::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
            $follower_id= Login::isUserLoggedIn();
                    //if follow has been submitted
                    if(isset($_POST['follow'])){
                        //get the user and follower id. follower id is the id of the person that is currently logged in
                        echo Login::isUserLoggedIn();
                        echo $user_id;
                        //the person logged in hasnt followed this user before
                        if(!database::query('SELECT follower_id FROM followers WHERE user_id=:user_id AND follower_id=:follower_id' , array(':user_id' =>$user_id , ":follower_id"=>$follower_id))){
                              database::query('INSERT INTO followers VALUES(\'\', :user_id, :follower_id)' , array(':user_id' =>$user_id , ':follower_id' =>$follower_id));
                        }
                        else{
                              echo"THIS USER IS BEING FOLLOWED BY YOU ALREADY";
                        }
                        $isFollowing= True;
                    }
                    //if unfollow has been submitted
                    if(isset($_POST['unfollow'])){
                        //get the user and follower id. follower id is the id of the person that is currently logged in
                        //the person logged in is following the user
                        if(database::query('SELECT follower_id FROM followers WHERE user_id=:user_id AND follower_id=:follower_id' , array(':user_id' =>$user_id , ":follower_id"=>$follower_id))){
                              database::query('DELETE FROM followers WHERE user_id=:user_id AND follower_id=:follower_id' , array(':user_id' =>$user_id , ':follower_id' =>$follower_id));
                        }
                        else{
                              echo"CAN'T UNFOLLOW IF YOU ARE NOT FOLLOWING THIS USER";
                        }
                        $isFollowing= False;
                    }
                            // regardless of whether or not they hit follow. if following then set it to follow
                            if(database::query('SELECT follower_id FROM followers WHERE user_id=:user_id AND follower_id=:follower_id' , array(':user_id' =>$user_id , ":follower_id"=>$follower_id))){
                              $isFollowing= True;
                            }

                                  //after checking if user exists check to see if the user has made a post
                                  if(isset($_POST['post'])){
                                    //gets the text and the user doing the posting
                                    Post::createPost($_POST['postcontent'], Login::isUserLoggedIn() , $user_id);
                                }

                                //if the like button has been clicked
                                if(isset($_GET['postid'])){
                                    Post::likePost($_GET['postid'],Login::isUserLoggedIn());
                                }

                                //retrieve the post info and display it
                                $posts=Post::displayPost($user_id, $username, Login::isUserLoggedIn());

                                //check to see if the searchbox was submitted to find users
                                if(isset($_POST['searchbox'])){
                                        header('Location: search-result.php?searchbox=' .$_POST['searchbox']);
                                }
          }
          else {
                die("NO USERS FOUND");
          }
  }

    //check for proile picture submission here
    if(isset($_POST['uploadprofilepicture'])){

        $image = base64_encode(file_get_contents($_FILES['profilepicture']['tmp_name']));

        //additional stream context info. header
        $options = array('http'=>array(
                'method'=>"POST",
                'header'=>"Authorization: Bearer 2db07acd263870a5708b81e6eaea8a8a7d5fdf4c\n".
                "Content-Type: application/x-www-form-urlencoded",
                'content'=>$image
        ));

        $context = stream_context_create($options);
        $imgururlendpoint = "https://api.imgur.com/3/upload";

        //check to see if the file size is too big
        if($_FILES['profilepicture']['size']> 10240000){
          die('FILE SIZE IS TOO BIG ');
        }

        //response from imgur server
        $response = file_get_contents($imgururlendpoint, false, $context);
        //decode the json sent back and into a php array
        $response= json_decode($response);
        //isnert into the database the response link sent from imgur
        database::query('UPDATE users SET profile_picture=:profile_picture WHERE id=:user_id'
         ,array(':profile_picture'=>$response->data->link ,':user_id'=>Login::isUserLoggedIn()));
    }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">
  </head>
  <body>
    <nav class="navbar navbar-custom" role="navigation">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <?php if(Login::isUserLoggedIn()){
              echo "<a class='navbar-brand navbar-font' href='index.php'>XSocial</a>" ;
        }

        else{
          echo "<a class='navbar-brand navbar-font' href='create_account.php'>XSocial</a>" ;
            }
        ?>
        <a  class='navbar-text' href="profile.php?username=<?php echo $loggedinUsername; ?>">My Profile</a>
        <a  class='navbar-text' href="logout.php">Log Out</a>

        <form class="navbar-form navbar-right" action="profile.php?username=<?php echo $loggedinUsername; ?>" method="post">
            <div class="form-group">
                <input class="form-control" type="text" name="searchbox" placeholder="Search For Users">
                <input class="btn btn-default btn-md" type="submit" name="search" value="Search">
            </div>
        </form>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-4">
                <h3 class="postclass"><?php echo $firstname . "'s Profile" ?></h3>


                <?php $profileimagelink=database::query("SELECT profile_picture FROM users WHERE username=:username" , array(':username'=>$username))[0]['profile_picture']; ?>
                <img src='<?php echo"$profileimagelink"?>' alt="profile image" height='200'>
                <form class="form-group" action="profile.php?username=<?php echo $username; ?>" method="post" enctype="multipart/form-data">
                    <h3>Change Your Profile Picture</h3>
                    <input type="file" name="profilepicture" value="">
                    <input class="btn btn-default" type="submit" name="uploadprofilepicture" value="Upload Profile Picture">
                </form>
        </div>

        <div class="col-lg-4">
          <h3 class="postclass">Leave A Post</h3>
          <form class="form-group" action="profile.php?username=<?php echo $username; ?>" method="post">
          <?php
                //change the button to follow / unfollow
                if($user_id !=$follower_id){
                    if($isFollowing){
                        echo '<input class="btn btn-primary btn-lg follow" type="submit" name="unfollow" value="Unfollow">';
                    }
                    else {
                      echo '<input class="btn btn-primary btn-lg follow" type="submit" name="follow" value="Follow">';
                    }
                }

           ?>
          </form>

          <form class="form-group"action="profile.php?username=<?php echo $username; ?>" method="post">
             <textarea class="" rows="15" cols="50" name="postcontent"></textarea>
             <br>
              <input class="btn btn-primary btn-lg" type="submit" name="post" value="Post">
          </form>
        </div>
        <div class="col-lg-4">
              <h3 class="postclass"> <?php echo $firstname;?>'s  Posts </h3>
              <?php echo $posts ?>
        </div>
      </div>
    </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  </body>
</html>
