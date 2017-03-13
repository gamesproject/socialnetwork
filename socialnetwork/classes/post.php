<?php

  class Post{
      //create post
      public static function createPost($postcontent, $poster_id ,$user_id){

        //if the string len dont meet requirements then nothing is done
        if(strlen($postcontent) >200 || strlen($postcontent) <1){
            echo "POST CONTENT CANT EXCEED 200 WORDS OR BE EMPTY";
        }
        else {
          database::query('INSERT INTO post VALUES(\'\',:postcontent, NOW(),:user_id, :poster_id, 0,0)', array(':postcontent'=> $postcontent ,
          ':user_id'=>$user_id ,':poster_id' => $poster_id));
        }
      }
      //like post
      public static function likePost($post_id, $poster_id){
        //check to see if the user has not liked the post before to prevent like spams
          if(!database::query('SELECT user_id FROM post_likes WHERE post_id = :post_id AND user_id= :poster_id'
            ,array(':post_id' =>$_GET['postid'] ,':poster_id'=>$poster_id))){
              //increment the post like count by 1
              database::query('UPDATE post SET likes=likes+1 WHERE id=:post_id', array(':post_id' =>$post_id));
              //insert into query the post id and the user_id of the poster
              database::query('INSERT INTO post_likes VALUES(\'\' , :post_id, :poster_id)' , array(':post_id'=>$post_id, ':poster_id'=>$poster_id));
            }

            //else user has liked the post and you can hit it again to unlike
            else{
              database::query('UPDATE post SET likes=likes-1 WHERE id=:post_id', array(':post_id' =>$post_id));
              //insert into query the post id and the user_id of the poster
              database::query('DELETE FROM post_likes WHERE post_id=:post_id AND user_id= :poster_id'
              ,array(':post_id'=> $post_id, ':poster_id'=>$poster_id));

            }
        }



        //display all post_likes
        public static function displayPost($user_id ,$username, $poster_id ){

          $retrievedPost= database::query('SELECT * FROM post WHERE user_id=:user_id ORDER BY id DESC', array(':user_id' => $user_id));
          $posts = "";
          //print the info out of all the retrieved post
          foreach($retrievedPost as $p){
                //if user has liked this
                if(!database::query('SELECT post_id FROM post_likes WHERE post_id=:post_id AND user_id=:poster_id',
                array(':post_id'=>$p['id'] , ':poster_id'=> $poster_id))){

                    $postername=database::query('SELECT firstname FROM users WHERE id=:poster_id', array(':poster_id' => $p['poster_id']))[0]['firstname'];
                    $posts .=self::mentionPost($p['body']) ."<br> Posted at ".$p['post_date'] ."<br> Posted by $postername"
                    //make a like button next to each post. if clicked, send a header to the query string in url
                    ."<form class='form-group' action='profile.php?username=$username&postid=".$p['id']." ' method='post'>
                        <input class='btn btn-primary btn-xs' type='submit' name='likepost' value='Like'>
                        <span>$p[likes] Likes </span>
                    </form>"

                    ."<hr>";
                }
                else{
                    $postername=database::query('SELECT firstname FROM users WHERE id=:poster_id', array(':poster_id' => $p['poster_id']))[0]['firstname'];
                    $posts .=self::mentionPost($p['body']) ."<br> Posted at ".$p['post_date'] ."<br> Posted by $postername"
                    //make a like button next to each post. if clicked, send a header to the query string in url
                    ."<form class='form-group' action='profile.php?username=$username&postid=".$p['id']." ' method='post'>
                        <input class='btn btn-primary btn-xs' type='submit' name='unlikepost' value='Unlike'>
                          <span>$p[likes] Likes </span>
                    </form>"

                    ."<hr>";
                }

          }

          return $posts;
        }

        //check to see if there was mention
        public static function mentionPost($post){
                //split up the word into arrays and check the first char of each word
                $post= explode(" ", $post);
                $revisedpost= "";

                  foreach($post as $p){
                        if(substr($p,0,1)== '@'){
                          $revisedpost.= "<a href='profile.php?username=".substr($p,1)."'>" .htmlspecialchars($p). "</a>";
                        }
                        else {
                           $revisedpost.= htmlspecialchars($p). " ";
                        }
              }

              return $revisedpost;
        }

  }

 ?>
