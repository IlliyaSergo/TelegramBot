<?php

if(!defined('STDIN'))  define('STDIN',  fopen('php://stdin',  'rb'));
if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));

use SergiX44\Nutgram\Nutgram;

require 'vendor/autoload.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli("127.0.0.1:3306", "root", "", "TestDataBase");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<p>Connection success</p>";

$conn->query("DROP TABLE IF EXISTS Users");
$conn->query("CREATE TABLE Users(id serial primary key, TGUserID varchar (30), TGName varchar(255), TGSurname varchar(255), TGUsername varchar(255))");
//$conn->query("INSERT INTO TestTable1(id) VALUES (1), (2), (3)");

$bot = new Nutgram('6465933534:AAGKM9DZgwWxNaR0iRcWm81SrRMinZmXVJo');

$bot->onCommand('Start', function (Nutgram $bot) {
    $file = "UsersID.txt";
    $user = $bot->user();
    $userID = $user->id;
    $firstName = $user->first_name;

    if(!stripos(file_get_contents($file), $userID)) {
        $bot->sendMessage('You are first! Please, enter the command /write to write your data to the database');
        $fle = fopen($file, 'w+');
        fwrite($fle, json_encode($userID .PHP_EOL, JSON_UNESCAPED_UNICODE));
        if (!file_get_contents($file)) {
            file_put_contents($file, $userID);
        }
        fclose($fle);
    } else {
        $bot->sendMessage('Hello, ' .$firstName);
    }
});

$bot->onCommand('write', function (Nutgram $bot){
    $user = $bot->user();
    $userID = $user->id;
    $firstName = $user->first_name;
    $Surname = $user->last_name;
    $Username = $user->username;

    if (isset($userID) && isset($firstName) && isset($Surname) && isset($Username)) {
        $conn = mysqli_connect("127.0.0.1:3306", "root", "", "TestDataBase");
        if (!$conn) {
            die("Error: " . mysqli_connect_error());
        }

        $UserID = $conn->real_escape_string($user->id);
        $FirstName = $conn->real_escape_string($user->first_name);
        $Surname = $conn->real_escape_string($user->last_name);
        $Username = $conn->real_escape_string($user->username);

        $res = $conn->query("SELECT COUNT(*) FROM Users WHERE id=1") or die();
        $row = mysqli_fetch_row($res);
        if ($row[0]>0) {
            $bot->sendMessage("Your data is already in the database");
        } else {
            $AddInfo = "INSERT INTO Users(TGUserID,TGName,TGSurname,TGUsername) VALUES ('$UserID','$FirstName','$Surname','$Username')";
            if ($conn->query($AddInfo)) {
                $bot->sendMessage("Data added successfully");
            } else {
                $bot->sendMessage("Data not added");
                echo "Ошибка: " . $conn->error;
            }
        }

//        $AddInfo = "INSERT INTO Users(TGUserID,TGName,TGSurname,TGUsername) VALUES ('$UserID','$FirstName','$Surname','$Username')";
//        if ($conn->query($AddInfo)) {
//            $bot->sendMessage("Data added successfully");
//        } else {
//            $bot->sendMessage("Data not added");
//            echo "Ошибка: " . $conn->error;
//        }
    }
});

$bot->onCommand('info', function(Nutgram $bot) {
    $user = $bot->user();
    $firstName = $user->first_name;
    $Surname = $user->last_name;
    $Username = $user->username;

    $bot->sendMessage('Your ID: ' .$bot->userId());
    $bot->sendMessage('Your name: ' .$firstName);
    $bot->sendMessage('Your surname: ' .$Surname);
    $bot->sendMessage('Your username: ' .$Username);
});

$bot->onText('My name is {name}', function(Nutgram $bot, string $name) {
    $bot->sendMessage("Hi $name");
});

$bot->onCommand('undead', function(Nutgram $bot) {
    $photo = fopen('Death_Knight.jpg', 'r+');
    $bot->sendPhoto($photo);
});

$conn ->close();

$bot->run();

echo "Hello";