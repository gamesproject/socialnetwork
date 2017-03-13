<?php

  include('classes/database.php');
  include('classes/login-info.php');

  $loggedinUsername= database::query('SELECT username FROM users WHERE id=:loggedInID', array(':loggedInID'=>Login::isUserLoggedIn()))[0]['username'];

  //if the user hits searchbox again in search-result.php do a clean redirect
  if(isset($_POST['searchbox'])){
          header('Location: search-result.php?searchbox=' .$_POST['searchbox']);
  }

  //get the search box info and make a query in to the database searching for user
  if(isset($_GET['searchbox'])){
     $searchuserresult= database::query('SELECT username,firstname,lastname, profile_picture FROM users WHERE firstname LIKE :firstname',
     array(':firstname' => '%'.$_GET['searchbox'].'%'));
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

         <form class="navbar-form navbar-right" action="search-result.php" method="post">
             <div class="form-group">
                 <input class="form-control" type="text" name="searchbox" placeholder="Search For Users">
                 <input class="btn btn-default btn-md" type="submit" name="search" value="Search">
             </div>
         </form>
       </div>
     </nav>

     <div class="">
        <h1>Search Results</h1>
        <?php
        foreach($searchuserresult as $s){
          echo "<a href='profile.php?username=$s[0]'>$s[firstname] $s[lastname] </a> <br> <img src='$s[profile_picture]' alt='profile image' height='200'> <br> <br>";
        }?>
     </div>
