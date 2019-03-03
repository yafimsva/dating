<?php
/**
 * Yafim Vainilovich
 * March 1, 2019
 * 328/dating/model/Database.php
 */

/*
 * CREATE TABLE `yvainilo_grc`.`Members` ( `member_id` INT(100) NOT NULL AUTO_INCREMENT ,
 * `fname` VARCHAR(50) NOT NULL , `lname` VARCHAR(50) NOT NULL , `age` INT(100) NOT NULL ,
 * `gender` VARCHAR(15) NOT NULL , `phone` VARCHAR(50) NOT NULL , `email` VARCHAR(50) NOT NULL ,
 * `state` VARCHAR(50) NOT NULL , `seeking` VARCHAR(50) NOT NULL , `bio` VARCHAR(1000) NOT NULL ,
 * `premium` TINYINT NOT NULL , `image` VARCHAR(100), `interests` VARCHAR(1000),
 * PRIMARY KEY (`member_id`)) ENGINE = MyISAM;
 */

ini_set("display_errors", 1);
error_reporting(E_ALL);

require_once('/home/yvainilo/config.php');

class Database
{

    function connect()
    {
        #connect to the database
        try {
            $dbh = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
            return $dbh;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    function insertMember($fname, $lname, $age, $gender, $phone, $email,
                          $state, $seeking, $bio, $premium, $image, $interests)
    {
        global $dbh;

        $sql = "INSERT INTO Members (fname, lname, age, gender, phone, email, state,
                seeking, bio, premium, image, interests) 
            VALUES(:fname, :lname, :age, :gender, :phone, :email, :state,
                :seeking, :bio, :premium, :image, :interests)";

        $statement = $dbh->prepare($sql);
        $statement->bindParam(':fname', $fname, PDO::PARAM_STR);
        $statement->bindParam(':lname', $lname, PDO::PARAM_STR);
        $statement->bindParam(':age', $age, PDO::PARAM_INT);
        $statement->bindParam(':gender', $gender, PDO::PARAM_STR);
        $statement->bindParam(':phone', $phone, PDO::PARAM_STR);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->bindParam(':state', $state, PDO::PARAM_STR);
        $statement->bindParam(':seeking', $seeking, PDO::PARAM_STR);
        $statement->bindParam(':bio', $bio, PDO::PARAM_STR);
        $statement->bindParam(':premium', $premium, PDO::PARAM_INT);
        $statement->bindParam(':image', $image, PDO::PARAM_STR);
        $statement->bindParam(':interests', $interests, PDO::PARAM_STR);

        $success = $statement->execute();
        return $success;
    }

    function getMembers()
    {
        global $dbh;
        $sql = "SELECT * FROM Members ORDER BY lname";
        $statement = $dbh->prepare($sql);
        $statement->execute();

        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    function getMember($id)
    {
        global $dbh;

        $sql = "SELECT * FROM Members WHERE member_id=$id";
        $statement = $dbh->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}