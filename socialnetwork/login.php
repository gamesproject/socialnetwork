<?php
    include('classes/database.php');

    if(isset($_POST['login'])){
      $username= $_POST['username'];
      $password= $_POST['password'];
      //check to see if the login username exists
      if(database::query('SELECT username FROM users WHERE username= :username' , array(':username' => $username))){
            //use password verify to match with the hashed password
            //will return one column [0]  and access row [password]
            if(password_verify($password,database::query('SELECT password FROM users WHERE username= :username' , array(':username'=> $username))[0]['password'])){

                //generate a random token for cookie. use bin2hex to convert to a hex for storage
                $cstrong= True;
                $token=bin2hex(openssl_random_pseudo_bytes(64,$cstrong));

                //get the user id that is currently logged in
                $user_id= database::query('SELECT id FROM users WHERE username = :username', array(':username' =>$username))[0]['id'];

                //insert the token into the database
                //using sha1() to hash the tokens to keep it safe
                database::query('INSERT INTO login_token VALUES(\'\' , :token, :user_id)', array(':token' => sha1( $token) , ':user_id' => $user_id));

                //set the token into cookie
                setcookie("SOCIALNETWORKUSERID" , $token, time() + 60* 60 *24 *7, '/' , NULL, NULL, TRUE);
                setcookie("SWAPTOKEN",  '911', time() + 60* 60 *24 *3, '/' , NULL, NULL, TRUE);

                //get username and send pass the variable to the get[username] in profiles.php
                $user_name=database::query('SELECT username FROM users WHERE id= :user_id', array(':user_id'=> $user_id))[0]['username'];

                header('Location: profile.php?username=' .$user_name);
            }

            else {
              echo "incorrect pw";
            }

      }
      else {
        echo "USER NOT REGISTERED";
      }
    }
 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>Login</title>
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
           <form class="form-horizontal" action="login.php" method="post">
              <div class="col-md-4 col-md-offset-4
                          col-ld-4 col-ld-offset-4
                          col-sm-8 col-sm-offset-2
                          col-xs-10 col-xs-offset-1
                          center-text">

                          <div class="form-group">
                                <div class="col-lg-12">
                                  <input class="form-control input-lg" type="text" name="username" placeholder="UserName" id="username">
                                </div>
                          </div>
                          <div class="form-group">
                                  <div class="col-lg-12">
                                <input class="form-control input-lg" type="password" name="password" placeholder="Password" id="password">
                                  </div>
                          </div>
                          <div class="form-group">
                                <div class="col-lg-12">
                                <a href="create_account.php"><button class="btn btn-primary btn-lg" type="button" >Create Account</button></a>
                                  <input class="btn btn-success btn-lg input-lg" type="submit" name="login" value="Log In" >
                                </div>
                          </div>
              </div>
           </form>
         </div>
       </div>


     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
   </body>
 </html>
