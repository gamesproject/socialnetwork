<?php
  include('classes/database.php');
  include('classes/login-info.php');

  $tokenisvalid= False;
  //display form if user is loggged in else die
  if(Login::isUserLoggedIn()){
      if(isset($_POST['changepassword'])){
          //get the old password and the current user id;
          $oldpassword= $_POST['oldpassword'];
          $newpassword= $_POST['newpassword'];
          $newpasswordrepeat=$_POST['newpasswordrepeat'];
          $user_id= Login::isUserLoggedIn();
          //verify to make sure the entered old password is teh same as teh logged in user password
          if(password_verify($oldpassword, database::query('SELECT password FROM users WHERE id=:user_id' ,
          array(':user_id' => $user_id))[0]['password'])){
                //make sure the entered password is the same as teh repeated password
                if($newpassword==$newpasswordrepeat){
                        //make sure the new password meets length requirement
                        if(strlen($newpassword)>=8 && strlen($newpassword)<=20){
                            database::query('UPDATE users SET password=:password WHERE id=:user_id',
                            array(':password' =>password_hash($newpassword, PASSWORD_BCRYPT), ':user_id' => $user_id));
                        }
                        else{
                          echo "PASSWORD DON'T MEET LENGTH REQUIREMENT ";
                        }
                }
                else{
                  echo "ENTERED PASSWORDS DON'T MATCH";
                }
          }
          else {
            echo "INCORRECT OLD PASSWORD";
          }
        }
  }

  //not logged in aka resetting password
  else {
          echo " NOT LOGGED IN";
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Change Password</title>
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
            <h2 class="bold-font">Change Password</h2>
            <form class="" action="change-password.php"method="post">
                  <input class="form-control input-lg" type="text" name="oldpassword" placeholder="Current Password"> <br>
                  <input class="form-control input-lg"type="text" name="newpassword" placeholder="Type In New Pasword"> <br>
                  <input class="form-control input-lg"type="text" name="newpasswordrepeat" placeholder="Repeat The New Password"> <br>
                  <input class= "btn btn-primary btn-lg"type="submit" name="changepassword" value="Change Password">

            </form>
          </div>
        </div>
      </div>


    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  </body>
</html>
