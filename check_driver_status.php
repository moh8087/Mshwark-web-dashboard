<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 09.06.16
 * Time: 22:15
 */



// STEP 1. Declare parms of user inf
// if GET or POST are empty
if ( empty($_REQUEST["did"])) {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required information";
    echo json_encode($returnArray);
    return;
}

// Securing information and storing variables
$did = htmlentities($_REQUEST["did"]);







// STEP 2. Build connection
// Secure way to build conn
$file = parse_ini_file("../../mshwark.ini");

// store in php var inf from ini var
$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

// include access.php to call func from access.php file
require ("secure/access_driver.php");
$access = new access($host, $user, $pass, $name);
$access->connect();







    // get current registered user information and store in $user
    $user = $access->selectDriverDid($did);

    // successfully registered
    if ($user) {

    // declare information to feedback to user of App as json

    $returnArray["did"] = $user["did"];
    $returnArray["demail"] = $user["demail"];
    $returnArray["dmobile"] = $user["dmobile"];
    $returnArray["dfullname"] = $user["dfullname"];
    $returnArray["status"] = $user["status"];

    if  ( $user["status"] == '1' ) {

      $returnArray["online"] = "1";
      $returnArray["message"] = "online";
    }
    else {

      $returnArray["online"] = "2";
      $returnArray["message"] = "offline";

    }


} else {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Could not identify with provided infomraiton";
}

/*}
else {


  $returnArray["status"] = "400";
  $returnArray["message"] = "Email is already taken";


}*/


// STEP 5. Close connection
$access->disconnect();


// STEP 6. Json data
echo json_encode($returnArray);



?>
