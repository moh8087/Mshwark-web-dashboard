<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 06.06.16
 * Time: 21:57
 */



// STEP 1. Declare variables to store user information
// array will store all informaiton
/*if ( empty($_REQUEST["test"]) ) {

    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required information";
    return;

}*/





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
$access->getDriversLocation();



//if (!empty($driver)) {
  //  $returnArray = $driver;
//}


/*if (empty($driver)) {

    $returnArray["status"] = "403";
    $returnArray["message"] = "There are no drivers avalibale";
    echo json_encode($returnArray);
    return;

}
else{
    $returnArray["drivers"] = $driver;
  echo json_encode($returnArray);
  return;


}*/






// STEP 5. Close connection after registration
$access->disconnect();



// STEP 6. JSON data
//echo json_encode($returnArray);




?>
