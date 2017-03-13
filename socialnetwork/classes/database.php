<?php
  //db class to hold all functions
  class database{
    //connects to database
    private static function connect(){
        $pdo= new PDO('mysql:host=127.0.0.1;dbname=bchen580_socialnetwork;charset=utf8', 'bchen580_bchen58', 'bleach12');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    //makes a query to the database
    public static function query($query, $params=array()){
        //instance of database class to connect to query
        $statement=self::connect()->prepare($query);
        $statement->execute($params);

        //check to see if the query made is a selecy query. if it is, return the data
        if(explode(" ", $query)[0]=='SELECT'){
            $result= $statement->fetchAll();
            return $result;
        }
    }
  }
 ?>
