<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 06.06.16
 * Time: 21:57
 */



// STEP 1. Declare variables to store user information
// array will store all informaiton
if ( empty($_REQUEST["email"]) ) {

    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required information";
    return;

}


// STEP 1.1 Pass POST / GET via html encryp and assign to vars
$email = htmlentities($_REQUEST["email"]);



// STEP 2. Build Connection
// Secure way to store Connection Infromation
$file = parse_ini_file("../../mshwark.ini");   // accessing the file with connection infromation

// retrieve data from file
$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

// include MySQLDAO.php for connection and interacting with db
require("secure/access.php");

// running MySQLDAO Class with constructed variables
$access = new access($host, $user, $pass, $name);
$access->connect();   // launch opend connection function



// STEP 3. Get user
$user = $access->getUser($email);

if ($user) {

  $returnArray["id"] = $user["id"];
  $returnArray["email"] = $user["email"];
  $returnArray["mobile"] = $user["mobile"];
  $returnArray["fullname"] = $user["fullname"];
  $returnArray["ava"] = $user["ava"];
  echo json_encode($returnArray);
  return;



}

else
{
  $returnArray["status"] = "403";
  $returnArray["message"] = "User is not found";
  echo json_encode($returnArray);
  return;
}













// STEP 5. Close connection after registration
$access->disconnect();



// STEP 6. JSON data
echo json_encode($returnArray);




?>
