<?php
  include('classes/database.php');
  include('classes/login-info.php');

  if (isset($_POST['logout'])) {
      //check to see if the all device was ticked
      if(isset($_POST['alldevice'])){
          database::query('DELETE FROM login_token WHERE user_id=:user_id' , array(':user_id'=> Login::isUserLoggedIn()));
      }

      else {
        //if the cookie exists then delete teh coookie and the login token from the database
        if(isset($_COOKIE['SOCIALNETWORKUSERID'])){

            database::query('DELETE FROM login_token WHERE token=:token' , array(':token'=> sha1($_COOKIE['SOCIALNETWORKUSERID'])));
        }
          //set cookies to expire
          setcookie('SOCIALNETWORKUSERID', '1', time()-9999999999999 ,'/');
          setcookie('SWAPTOKEN', '1', time()-99999999999999, '/');
          header("Location: login.php");
      }
  }
 ?>

  <!DOCTYPE html>
  <html>
    <head>
      <meta charset="utf-8">
      <title>Logout</title>
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
          <a class="navbar-brand navbar-font" href="create_account.php">XSocial</a>
        </div>
      </nav>

        <!-- Login Form -->
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-4 col-md-offset-4
                        col-ld-4 col-ld-offset-4
                        col-sm-8 col-sm-offset-2
                        col-xs-10 col-xs-offset-1
                        center-text">
              <h2 class="bold-font">Are You Sure You Want To Log Out?</h2>
              <form class="" action="logout.php" method="post">
                  <input type="checkbox" name="alldevice" value="alldevice"> Do You Want To Log Out From All Browsers? <br><br>
                  <input class="btn btn-primary" type="submit" name="logout" value="Log Out">
              </form>
            </div>
          </div>
        </div>


      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </body>
  </html>
