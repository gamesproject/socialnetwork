<?php
  include('classes/database.php');
  include('classes/login-info.php');

  if(isset($_POST['resetpassword'])){
    //create a passsword token
    $cstrong= True;
    $token=bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
    $email= $_POST['email'];
    //need to get the user id when not logged in so get it via the email entered
    $user_id=database::query ('SELECT id FROM users WHERE email=:email' , array(':email'=>$email))[0]['id'];
    //insert token into password token
    database::query('INSERT INTO password_token VALUES(\'\', :token, :user_id)', array(':token'=>$token, ':user_id'=>$user_id));
    echo "EMAIL RESET SENT";
    echo "$token";
  }
  else{
    die("AA");
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Reset Password</title>
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
            <h2 class="bold-font">Reset Password</h2>
            <form class="" action="forgot-password.php" method="post">
                  <input class="form-control input-lg"type="email" name="email" placeholder="Type Email"> <br>
                  <input class= "btn btn-primary btn-lg"type="submit" name="resetpassword" value="Reset Password">

            </form>
          </div>
        </div>
      </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  </body>
</html>
