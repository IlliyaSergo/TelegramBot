<?php

if(!defined('STDIN'))  define('STDIN',  fopen('php://stdin',  'rb'));
if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Message\Message;
use SergiX44\Nutgram\Telegram\Types\Keyboard\KeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\ReplyKeyboardMarkup;

require 'vendor/autoload.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli("127.0.0.1:3306", "root", "", "TestDataBase");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connection success";

$conn->query("DROP TABLE IF EXISTS TestTable1");
$conn->query("CREATE TABLE TestTable1(id INT)");
$conn->query("INSERT INTO TestTable1(id) VALUES (1), (2), (3)");

$bot = new Nutgram('6465933534:AAGKM9DZgwWxNaR0iRcWm81SrRMinZmXVJo');

//$photo = fopen('death_knight_disciples_ii_fan_art_by_svetoslavpetrov_d7v8n9e-pre.jpg', 'r+');

$bot->onCommand('start', function(Nutgram $bot) {
    $bot->sendMessage('Ciao!');
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
