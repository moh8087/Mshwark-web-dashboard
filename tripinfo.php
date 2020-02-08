<?php



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



$result = $access->tripInfo($id);

if (!empty($result)) {


  $returnArray["status"] = "200";
  $returnArray["message"] = "Driver is in the way";
  $returnArray["id"] = $result["id"];
  $returnArray["email"] = $result["email"];
  $returnArray["rid"] = $result["rid"];
  $returnArray["rname"] = $result["rname"];
  $returnArray["rmobile"] = $result["rmobile"];
  $returnArray["did"] = $result["did"];
  $returnArray["dname"] = $result["dname"];
  $returnArray["dmobile"] = $result["dmobile"];
  $returnArray["dphoto"] = $result["dphoto"];
  $returnArray["carmodel"] = $result["carmodel"];
  $returnArray["dlat"] = $result["dlat"];
  $returnArray["dlog"] = $result["dlog"];


  // STEP 4. Emailing Rider to notify him that his request has been accepted

  // send SMS
  //$message = "Your Request number is: " . $result["id"] . "";

  //$access->sendSMS($result["rmobile"], $message);

  // include email.php
  require ("secure/email.php");

  // store all class in $email var
  $email = new email();


  // refer emailing information
  $details = array();
  $details["subject"] = "Driver is in the way";
  $details["to"] = $result["email"];
  $details["fromName"] = "مشوارك";
  $details["fromEmail"] = "mshwarkk@gmail.com";



/*  $details["body"] =" <!DOCTYPE html>
  <html>
  <body>

<p>Driver is in the way, Your request has been accepted</p>
<p>Your Request number is: " . $result["id"] . "</p>
<p>Driver name: " . $result["dname"] . "</p>
<p>Driver mobile: " . $result["dmobile"] . "</p>
<p>Driver Car Model: " . $result["carmodel"] . "</p>
</body>
</html>";*/


$details["body"] = '<html><body>';
$details["body"] .= 'Driver is in the way, Your request has been accepted';
$details["body"] .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
$details["body"] .= "<tr style='background: #eee;'><td><strong>Your Request number is:</strong> </td><td>" . $result["id"] . "</td></tr>";
$details["body"] .= "<tr><td><strong>Driver name:</strong> </td><td>" . $result["dname"] . "</td></tr>";
$details["body"] .= "<tr><td><strong>Driver mobile:</strong> </td><td>" . $result["dmobile"] . "</td></tr>";
$details["body"] .= "<tr><td><strong>Driver Car Model:</strong> </td><td>" . $result["carmodel"] . "</td></tr>";
$details["body"] .= "</table>";
$details["body"] .= "</body></html>";



  $email->sendEmail($details);




  echo json_encode($returnArray);
  return;

} else {

  $returnArray["status"] = "403";
  $returnArray["message"] = "still waiting to look for a driver";
  echo json_encode($returnArray);
  return;

}










// STEP 5. Close connection after registration
$access->disconnect();



// STEP 6. JSON data
echo json_encode($returnArray);




?>
