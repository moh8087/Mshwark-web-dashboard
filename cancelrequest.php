<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 06.06.16
 * Time: 21:57
 */



// STEP 1. Declare variables to store user information
// array will store all informaiton
if ( empty($_REQUEST["id"]) ) {

    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required information";
    return;

}


// STEP 1.1 Pass POST / GET via html encryp and assign to vars
$id = htmlentities($_REQUEST["id"]);





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


// Step 3. Bring both Driver and Rider information to send them email



$result = $access->getRequestInfo($id);

if (!empty($result)) {


  $returnArray["id"] = $result["id"];
  $returnArray["email"] = $result["email"];
  $returnArray["rid"] = $result["rid"];
  $returnArray["rname"] = $result["rname"];
  $returnArray["rmobile"] = $result["rmobile"];
  $returnArray["did"] = $result["did"];
  $returnArray["dname"] = $result["dname"];
  $returnArray["demail"] = $result["demail"];
  $returnArray["dmobile"] = $result["dmobile"];
  $returnArray["dphoto"] = $result["dphoto"];
  $returnArray["carmodel"] = $result["carmodel"];
  $returnArray["dlat"] = $result["dlat"];
  $returnArray["dlog"] = $result["dlog"];


// set status to be Cancelled

$status = "cancelled";
// STEP 4. Delete request according to id
$cancel = $access->cancelRequest($status,$id);

if (!empty($cancel)) {
    $returnArray["message"] = "Successfully Cancelled";
    $returnArray["result"] = $cancel;

  }
  else {
      $returnArray["message"] = "Could not cancel request";
  }


}
else {

  $returnArray["message"] = "There is no driver to be cancelled";


}



// send email to Driver
if (!empty($result["demail"])) {

require ("secure/email.php");

// store all class in $email var
$email = new email();


// refer emailing information
$details = array();
$details["subject"] = "Request is Cancelled";
$details["to"] = $result["demail"];
$details["fromName"] = "مشوارك";
$details["fromEmail"] = "mshwarkk@gmail.com";



$details["body"] =" <!DOCTYPE html>
<html>
<body>

<p>Rider has been cancelled the request</p>
<p>Request number is: " . $result["id"] . "</p>
<p>Rider name: " . $result["rname"] . "</p>
<p>Rider mobile: " . $result["rmobile"] . "</p>

</body>
</html>";


$email->sendEmail($details);
}






// STEP 5. Close connection after registration
$access->disconnect();



// STEP 6. JSON data
echo json_encode($returnArray);




?>
