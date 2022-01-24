<?php
  //Import PHPMailer classes into the global namespace
  //These must be at the top of your script, not inside a function
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\SMTP;
  use PHPMailer\PHPMailer\Exception;
    //composer vendor
    require ("vendor/autoload.php");

  // API for countries
  $countries_request = file_get_contents("https://restcountries.eu/rest/v2/all");
  $countries = array_map(function ($value) { return $value->name;}, json_decode($countries_request));

  $subject=array(
      "Other",
      "Complaints",
      "Info",
      "Price",
      "Technical issue"
  );

  //function to check input
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  } 

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $isvalid=true;

      if (empty($_POST["firstname"])) {
        $firstnameErr = "Name is required";
        $isvalid=false;
      } else {
        $firstname = test_input($_POST["firstname"]);
        // check if name only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z-' ]*$/",$firstname)) {
            $firstnameErr = "Only letters and white space allowed";
            $isvalid=false;
        }
      }
      if (empty($_POST["lastname"])) {
        $lastnameErr = "Name is required";
        $isvalid=false;
      } else {
        $lastname = test_input($_POST["lastname"]);
        // check if name only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z-' ]*$/",$lastname)) {
            $lastnameErr = "Only letters and white space allowed";
            $isvalid=false;
        }
      }
  
      if (empty($_POST["email"])) {
        $emailErr = "Email is required";
        $isvalid=false;
      } else {
        $email = test_input($_POST["email"]);
        // check if e-mail address is well-formed
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
            $isvalid=false;
        }
      }
  
      if (empty($_POST["message"])) {
        $messageErr = "Message is required";
        $isvalid=false;
      } else {
        $message = test_input($_POST["message"]);
      }
    }
  
    if(isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['gender']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['message'])&& isset($_POST['country'])){     
        // define variables and set to empty values
        $firstnameErr = $lastnameErr = $emailErr = $messageErr = "";
        $firstname=test_input($_POST ["firstname"]);
        $lastname= test_input($_POST["lastname"]);
        $email= test_input($_POST["email"]);
        $country=$_POST["country"];
        $subject=$_POST["subject"];
        $message=test_input($_POST["message"]);
       

            
          var_dump ($isvalid);
            if($isvalid){
                 
              try {
                //Server settings
                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host = 'smtp.mailtrap.io';
                $mail->SMTPAuth = true;
                $mail->Port = 2525;
                $mail->Username = 'c6d60421b3695f';
                $mail->Password = 'ceca18e464c151';
                
                //Recipients
                $mail->setFrom('noreply@poulette.com', 'Mailer');
                $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
                $mail->addAddress('ellen@example.com');               //Name is optional
                $mail->addReplyTo('info@example.com', 'Information');
                $mail->addCC('cc@example.com');
                $mail->addBCC('bcc@example.com');
                
                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'Here is the subject';
                $mail->Body    = 'Hello ' . $firstname . '<br> We have received your email with the following information: <br> Firstname: ' . $firstname . '<br> Lastname: ' . $lastname . '<br> Subject: ' . $subject . '<br> Message: ' . $message . '.' ;
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                
                $mail->send();
                    echo 'Message has been sent';
                    header("Location: thankyou.html");
              } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            }           
    }
  
?>       
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="HackeusePoulette" content="putting php into practice">
        <meta name="keywords" content="HTML, CSS, JavaScript,PHP">
        <meta name="author" content="Elsa Magalhaes">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hackeuse Poulette</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
    <div class="img">
        <img src="assets/img/hackers-poulette-logo.png" alt="hackers-poulette-logo">
    </div>
    <div class="form">     
        <h1>Get in touch</h1>
        <h2>Contact form</h2>
        <p><span class="error">* required field</span></p>
        <form method="post" action="">
            <label for="firstname">First Name:</label>
            <input type="text" name="firstname">
            <span class="error">* <?php echo isset($firstnameErr)? $firstnameErr:"";?></span>
            <br>
            <label for="lastname">Last Name:</label>
            <input type="text" name="lastname">
            <span class="error">* <?php echo isset($lastnameErr)? $lastnameErr:"";?></span>
            <input id="website" name="website" type="text" value="" style=" display:none" />
            <br>
            <label for="gender">Gender:</label>
            <input type="radio" name="gender" value="male">
            <label for="male">M</label>
            <input type="radio" name="gender" value="female">
            <label for="female">F</label>
            <input type="radio" name="gender" value="other">
            <label for="other">X</label>
            <br>
            <label for="email">Email:</label>
            <input type="text" name="email">
            <span class="error">* <?php echo isset($emailErr)? $emailErr:"";?></span>
            <br>
            <label for="country">Country:</label>
            <select name="country" id="country">
            <option>Select country</option>
            <?php foreach ($countries as $country) {
                    echo "<option value=\"".$country . "\">" . $country . "</option>";};?>
            </select>
            <br>
            <label for="subject">Subject:</label>
            <select name="subject" id="subject">
            <?php foreach ($subject as $key=>$value) {
                    echo "<option value=\"".$value . "\">" . $value . "</option>";};?>
            </select>
            <br>
            <textarea name="message">
               
            </textarea>
            <span class="error">* <?php echo isset($messageErr)? $messageErr:"";?></span>
            <br>
            <input type="submit" value="Submit">
            <input type="reset" value="Reset">
        </form>   
        
    </div>
    <script src="assets/javascript/script.js"></script>
    </body>
</html>