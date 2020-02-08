<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 09.06.16
 * Time: 22:15
 */



// STEP 1. Declare parms of user inf
// if GET or POST are empty
if ( empty($_REQUEST["did"]) || empty($_REQUEST["status"]) || empty ($_REQUEST["dlat"]) || empty($_REQUEST["dlog"])) {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required information";
    echo json_encode($returnArray);
    return;
}

// Securing information and storing variables
$did = htmlentities($_REQUEST["did"]);
$dlat= htmlentities($_REQUEST["dlat"]);
$dlog = htmlentities($_REQUEST["dlog"]);
$status = htmlentities($_REQUEST["status"]);






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


// Step 3. Make sure email is not taken
//$sure = $access->getUser($email);

//if(!$sure["id"]) { // if there is no user

  // STEP 4. Insert user information

$result = $access->updateDriver($status, $dlat, $dlog, $did);

// successfully registered
if ($result) {

    // get current registered user information and store in $user
    $user = $access->selectDriverDid($did);

    // declare information to feedback to user of App as json
    $returnArray["status"] = "200";
    $returnArray["message"] = "Update successfully";
    $returnArray["did"] = $user["did"];
    $returnArray["demail"] = $user["demail"];
    $returnArray["dmobile"] = $user["dmobile"];
    $returnArray["dfullname"] = $user["dfullname"];


/*
    // STEP 4. Emailing
    // include email.php
    require ("secure/email.php");

    // store all class in $email var
    $email = new email();

    // store generated token in $token var
    $token = $email->generateToken(20);

    // save inf in 'emailTokens' table
    $access->saveToken("emailTokens", $user["id"], $token);

    // refer emailing information
    $details = array();
    $details["subject"] = "Email confirmation on Twitter";
    $details["to"] = $user["email"];
    $details["fromName"] = "Akhmed Idigov";
    $details["fromEmail"] = "akhmedidigov@gmail.com";

    // access template file
    $template = $email->confirmationTemplate();

    // replace {token} from confirmationTemplate.html by $token and store all content in $template var
    $template = str_replace("{token}", $token, $template);

    $details["body"] = $template;

    $email->sendEmail($details);

    */


} else {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Could not register with provided infomraiton";
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
