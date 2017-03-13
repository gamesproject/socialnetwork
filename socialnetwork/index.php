<?php
  include('classes/database.php');
  include('classes/login-info.php');
  include('classes/post.php');

  //shows timeline if logged in
  $showTimeline=False;

  if(Login::isUserLoggedIn()){
    $user_id = Login::isUserLoggedIn();
    $username= database::query('SELECT username FROM users WHERE id=:user_id', array(':user_id'=>$user_id))[0]['username'];
    $showTimeLine=True;
  }
  else {
    echo "NOT LOGGED IN";
  }

  if (isset($_GET['postid'])) {
        Post::likePost($_GET['postid'], $user_id);
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
        <?php
        //if logged in then change the xsocial location
        if(Login::isUserLoggedIn()){
              echo "<a class='navbar-brand navbar-font' href='index.php'>XSocial</a>" ;
        }

        else{
          echo "<a class='navbar-brand navbar-font' href='create_account.php'>XSocial</a>" ;
            }

            //redirect back to log in user profile
          echo "  <a class='navbar-text' href='profile.php?username=$username'>My Profile</a>"

        ?>

        <a  class='navbar-text' href="logout.php">Log Out</a>
      </div>
    </nav>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  </body>
</html>


<?php



$followersposts = database::query('SELECT post.id, post.body, post.likes, post.post_date,post.poster_id,users.firstname FROM users, post, followers
WHERE post.user_id = followers.user_id
AND users.id = post.user_id
AND follower_id =:user_id
ORDER BY post.likes DESC', array(':user_id' =>$user_id));

//loop through all the posts recieved
foreach ($followersposts as $posts) {
        //get the name of the poster
        $posterfirstname=database::query('SELECT firstname FROM users WHERE id=:poster_id', array(':poster_id'=> $posts['poster_id']))[0]['firstname'];
        echo $posts['firstname'] . "'s Timeline<br>".$posts['body']. "<br>Posted By  ". $posterfirstname .
        "<br>Likes  ".  $posts['likes'] ."<br>Posted At " . $posts['post_date'] ;

        echo "<form action='index.php?postid=".$posts['id']."' method='post'>";
        //if the user logged on hasnt liked it then show like
              if(!database::query('SELECT user_id FROM post_likes WHERE post_id =:post_id AND user_id =:user_id'
              , array(':post_id' => $posts['id'], ':user_id'=>Login::isUserLoggedIn()))){

                    echo "<input type='submit' name='like' value='Like'>";
              }
              //if user has liked show unlike
              else {
                    echo "<input type='submit' name='unlike' value='Unlike'>";
                }
                  echo "<hr />";
                echo "</form";
}



?>
