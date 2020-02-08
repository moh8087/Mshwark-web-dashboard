<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 09.06.16
 * Time: 22:15
 */



// STEP 1. Declare parms of user inf
// if GET or POST are empty
if (empty($_REQUEST["rid"]) || empty($_REQUEST["email"])|| empty($_REQUEST["rname"]) || empty($_REQUEST["rmobile"]) || empty($_REQUEST["pickaddress"]) || empty($_REQUEST["picklatitude"]) || empty($_REQUEST["picklongitude"]) || empty($_REQUEST["dropaddress"]) || empty($_REQUEST["notes"])) {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required information";
    echo json_encode($returnArray);
    return;
}

// Securing information and storing variables
$rid = htmlentities($_REQUEST["rid"]);
$email = htmlentities($_REQUEST["email"]);
$rname = htmlentities($_REQUEST["rname"]);
$rmobile = htmlentities($_REQUEST["rmobile"]);
$pickaddress = htmlentities($_REQUEST["pickaddress"]);
$picklatitude = htmlentities($_REQUEST["picklatitude"]);
$picklongitude = htmlentities($_REQUEST["picklongitude"]);
$dropaddress = htmlentities($_REQUEST["dropaddress"]);
$notes = htmlentities($_REQUEST["notes"]);







// STEP 2. Build connection
// Secure way to build conn
$file = parse_ini_file("../../mshwark.ini");

// store in php var inf from ini var
$host = trim($file["dbhost"]);
$user = trim($file["dbuser"]);
$pass = trim($file["dbpass"]);
$name = trim($file["dbname"]);

// include access.php to call func from access.php file
require ("secure/access.php");
$access = new access($host, $user, $pass, $name);
$access->connect();



// STEP 3. Insert user information
$last_id = $access->newRequest($rid, $email, $rname, $rmobile, $pickaddress, $picklatitude, $picklongitude, $dropaddress, $notes );

// successfully registered
if ($last_id) {

// STEP 4. get current Rider information and store in $request
    $request = $access->selectRequest($last_id);

    // declare information to feedback to user of App as json
    $returnArray["status"] = "200";
    $returnArray["message"] = "Successfully request";
    $returnArray["id"] = $request["id"];
    $returnArray["email"] = $request["email"];
    $returnArray["rid"] = $request["rid"];
    $returnArray["rname"] = $request["rname"];
    $returnArray["rmobile"] = $request["rmobile"];
    $returnArray["pickaddress"] = $request["pickaddress"]; //htmlentities($_REQUEST["pickaddress"]);
    $returnArray["picklatitude"] = $request["picklatitude"];
    $returnArray["picklongitude"] = $request["picklongitude"];
    $returnArray["dropaddress"] = $request["dropaddress"];
    $returnArray["notes"] = $request["notes"];


} else {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Could not create request with provided infomraiton";
}



// STEP 5. get all Driver's online


$driver = $access->getClosestDriver($request["picklatitude"] , $request["picklongitude"] );

if ($driver) {

  $returnArray["did"] = $driver["did"];
  $returnArray["demail"] = $driver["demail"];
  $returnArray["dmobile"] = $driver["dmobile"];
  $returnArray["dfullname"] = $driver["dfullname"];
  $returnArray["ava"] = $driver["ava"];
  $returnArray["carmodel"] = $driver["carmodel"];
  $returnArray["dlat"] = $driver["dlat"];
  $returnArray["dlog"] = $driver["dlog"];


  }
  else {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Could not found any online driver";

  }

  // STEP 6. add Closest Driver to the request

  $driverIsAdded = $access->addDriverRequest($last_id, $driver["did"], $driver["demail"], $driver["dfullname"], $driver["dmobile"], $driver["ava"], $driver["carmodel"], $driver["dlat"], $driver["dlog"]);

  // Set Driver to be Offline

  $status = 22 ; // offline
  $offline = $access->setDriverOffline($status, $driver["did"]);

if ($driverIsAdded)
{


  // send email to Driver

  require ("secure/email.php");

  // store all class in $email var
  $email = new email();


  // refer emailing information
  $details = array();
  $details["subject"] = "New Request for you";
  $details["to"] = $driver["demail"];
  $details["fromName"] = "مشوارك";
  $details["fromEmail"] = "mshwarkk@gmail.com";



  /*$details["body"] =" <!DOCTYPE html>
  <html>
  <body>

<p>New Request for you.</p>
<p>Request number is: " . $request["id"] . "</p>
<p>Rider name: " . $request["rname"] . "</p>
<p>Rider mobile: " . $request["rmobile"] . "</p>
<p>Address: " . $request["pickaddress"] . "</p>
<p>Destination: " . $request["dropaddress"] . "</p>
<p>Notes: " . $request["notes"] . "</p>
<p>Location: ( " . $request["picklatitude"] . " , " . $request["picklongitude"] . " ) </p>
<p>Location: http://www.google.com/maps/place/". $request["picklatitude"] .",". $request["picklongitude"] ." </p>
</body>
</html>";*/


$details["body"] = '<html><body>';
$details["body"] .= 'New Request for you';
$details["body"] .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
$details["body"] .= "<tr style='background: #eee;'><td><strong>Request number is:</strong> </td><td>" . $result["id"] . "</td></tr>";
$details["body"] .= "<tr><td><strong>Rider name: </strong> </td><td>" . $request["rname"] . "</td></tr>";
$details["body"] .= "<tr><td><strong>Rider mobile:</strong> </td><td>" . $request["rmobile"] . "</td></tr>";
$details["body"] .= "<tr><td><strong>Address:</strong> </td><td>" . $request["pickaddress"] . "</td></tr>";
$details["body"] .= "<tr><td><strong>Destination:</strong> </td><td>" . $request["dropaddress"] . "</td></tr>";
$details["body"] .= "<tr><td><strong>Notes:</strong> </td><td>" . $request["notes"] . "</td></tr>";
$details["body"] .= "<tr><td><strong>Location:</strong> </td><td>( " . $request["picklatitude"] . " , " . $request["picklongitude"] . " )</td></tr>";
$details["body"] .= "<tr><td><strong>Location:</strong> </td><td>http://www.google.com/maps/place/". $request["picklatitude"] .",". $request["picklongitude"] ."</td></tr>";
$details["body"] .= "</table>";
$details["body"] .= "</body></html>";


  $email->sendEmail($details);


}

// STEP 5. get all Driver's online







/* STEP 4. Emailing
// include email.php
require ("secure/email.php");

// store all class in $email var
$email = new email();


// refer emailing information
$details = array();
$details["subject"] = "Your order has been saved";
$details["to"] = $request["email"];
$details["fromName"] = "مشوارك";
$details["fromEmail"] = "mshwarkk@gmail.com";



$details["body"] = "your request number is " . $request["id"] . "\r\n";

$email->sendEmail($details);
*/



// STEP 5. Close connection
$access->disconnect();


// STEP 6. Json data
echo json_encode($returnArray);



?>
