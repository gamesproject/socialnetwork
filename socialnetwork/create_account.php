<?php
  include('classes/database.php');

  if(isset($_POST['createaccount'])){
    $firstname=$_POST['firstname'];
    $lastname=$_POST['lastname'];
    $username=$_POST['username'];
    $password=$_POST['password'];
    $email=$_POST['email'];
    //check for account validation

    //check to see if user exists already
    if(!database::query('SELECT username FROM users WHERE username=:username' , array(':username'=>$username))){
            //make sure the username and password meets length requirement
            if(strlen($username)>=5 && strlen($username)<=20 && strlen($password)>=8 && strlen($password)<=20){
                    //make sure no special char allowed
                    if(preg_match('/^[a-zA-Z0-9_]+$/', $username)){
                          //filter the email
                          if(filter_var($email, FILTER_VALIDATE_EMAIL)){
                                    if(!database::query('SELECT email FROM users WHERE email=:email' , array(':email'=> $email))){
                                      database::query('INSERT INTO users VALUES(\'\',:username,:password,:email,:firstname,:lastname, \'\')',array(':username'=>$username,
                                      ':password'=>password_hash($password, PASSWORD_BCRYPT), ':email'=>$email, ':firstname'=> $firstname , ':lastname'=> $lastname));
                                      echo "registered";
                                    }
                                    else {
                                      echo "EMAIL IS IN USE ALREADY";
                                    }
                          }
                          else{
                            echo "ENTER A VALID EMAIL PLEASE";
                          }
                    }
                    else{
                      echo "NO SPECIAL CHARACTER ALLOWED";
                    }
            }
            else{
              echo "USERNAME OR PASSWORD DOES NOT MEET LENGTH REQUIREMENT";
            }
    }
    else {
      echo "USER ALREADY EXISTS";
    }
  }
 ?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>WELCOME TO XSOCIAL</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">
  </head>
  <body>
      <!-- Navigation -->

  <nav class="navbar navbar-custom" role="navigation">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand navbar-font" href="#">XSocial</a>
    </div>
  </nav>
      <!-- The form registeration  -->
      <div class="container-fluid">
            <div class="row">
              <div class=" col-xs-12
                         col-md-6
                         col-sm-12
                         col-lg-6">
                  <form class="form-horizontal" action="create_account.php" method="post">
                      <div class="form-group">
                            <div class="col-lg-6">
                              <input class="form-control input-lg" type="text" name="firstname" placeholder="First Name" id="firstname">
                            </div>
                      </div>
                      <div class="form-group">
                            <div class="col-lg-6">
                              <input class="form-control input-lg" type="text" name="lastname" placeholder="Last Name" id="lastname">
                            </div>
                      </div>
                      <div class="form-group">
                            <div class="col-lg-6">
                            <input class="form-control input-lg" type="text" name="username" placeholder="Enter Username" id="username">
                            </div>
                      </div>
                      <div class="form-group">
                              <div class="col-lg-6">
                            <input class="form-control input-lg" type="password" name="password" placeholder="Enter Password" id="password">
                              </div>
                      </div>
                      <div class="form-group">
                                <div class="col-lg-6">
                                  <input class="form-control input-lg" type="email" name="email" placeholder="Enter Email" id="email">
                                </div>
                      </div>
                      <div class="form-group">
                        <div class="col-lg-12">
                          <input class="btn btn-success btn-lg input-lg" type="submit" name="createaccount" value="Create An Account" >
                        </div>
                      </div>
                  </form>
                  <a href="login.php">Have an account? Log in!</a>
          </div>
      </div>
    </div>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  </body>
</html>
