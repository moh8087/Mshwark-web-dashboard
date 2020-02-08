<?php



// STEP 1. Declare variables to store user information
// array will store all informaiton
if ( empty($_REQUEST["id"]) ) {

    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required information";
    return;

}


// STEP 1.1 Pass POST / GET via html encryp and assign to vars
// Rider id
$rid = htmlentities($_REQUEST["id"]);



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



// STEP 2.2 Select posts + user related to $id
$trips = $access->getMyTrips($rid);

// STEP 2.3 If posts are found, append them to $returnArray
if (!empty($trips)) {
    $returnArray["posts"] = $trips;
}












// STEP 5. Close connection after registration
$access->disconnect();



// STEP 6. JSON data
echo json_encode($returnArray);




?>
