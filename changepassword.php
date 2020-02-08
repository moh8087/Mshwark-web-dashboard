<?php
/**
 * Created by PhpStorm.
 * User: macbookpro
 * Date: 09.06.16
 * Time: 22:15
 */



// STEP 1. Declare parms of user inf
// if GET or POST are empty
if ( empty($_REQUEST["id"]) || empty ($_REQUEST["password"])) {
    $returnArray["status"] = "400";
    $returnArray["message"] = "Missing required information";
    echo json_encode($returnArray);
    return;
}

// Securing information and storing variables
$id = htmlentities($_REQUEST["id"]);
$password = htmlentities($_REQUEST["password"]);




// secure password
$salt = openssl_random_pseudo_bytes(20);
$secured_password = sha1($password . $salt);



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
  $result = $access->updatePassword($id, $secured_password, $salt);

  // successfully registered
  if ($result) {

      // declare information to feedback to user of App as json
      $returnArray["id"] = "100"; // to send json id ti make sure is Changed
      $returnArray["status"] = "200";
      $returnArray["message"] = "Successfully Changed";


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
      $returnArray["message"] = "Could not change password with provided infomraiton";
  }








// STEP 5. Close connection
$access->disconnect();


// STEP 6. Json data
echo json_encode($returnArray);



?>
