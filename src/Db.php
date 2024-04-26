<?php

namespace Ethansilver\Restaurant;

use PDO;
use PDOException;

class Db
{
    public static function connect()
    {
        try {
            return new PDO(
                "mysql:host=localhost;dbname=restaurantDB",
                "root",
                ""
            );
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }
}
