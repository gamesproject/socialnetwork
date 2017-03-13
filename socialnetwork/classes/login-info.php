<?php

  class Login{

       public static function isUserLoggedIn(){
            //check to see if the cookie is available
            if(isset($_COOKIE['SOCIALNETWORKUSERID'])){

                //if the current token is exists
                if(database::query('SELECT user_id FROM login_token WHERE token = :token' , array(':token' => sha1($_COOKIE['SOCIALNETWORKUSERID'])))){
                      //get the userid that is corresponding to the login cookie token
                      $user_id=database::query('SELECT user_id FROM login_token WHERE token = :token' , array(':token' => sha1($_COOKIE['SOCIALNETWORKUSERID'])))[0]['user_id'];
                      //check to see if the swaptoken cookie is still set and if it is set then return the user id
                      if(isset($_COOKIE['SWAPTOKEN'])){
                          return $user_id;
                      }
                      //else make a new cookie
                      else{
                        $cstrong= True;
                        $token=bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
                        database::query('INSERT INTO login_token VALUES(\'\' , :token, :user_id)', array(':token' => sha1( $token) , 'user_id' => $user_id));

                        //delete the older cookie
                        database::query('DELETE FROM login_token WHERE token=:token', array(':token' => sha1($_COOKIE['SOCIALNETWORKUSERID'])));
                        setcookie("SOCIALNETWORKUSERID" , $token, time() + 60* 60 *24 *7, '/' , NULL, NULL, TRUE);
                        setcookie("SWAPTOKEN", '911', time() + 60* 60 *24 *3, '/' , NULL, NULL, TRUE);
                        return $user_id;
                      }
                }
            }
            return false;
        }
  }

 ?>
