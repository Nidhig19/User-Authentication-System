<?php
session_start();
include('dbcon.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require 'vendor/autoload.php';

function sendemail_verify($name,$email,$verify_token)
{
    $mail = new PHPMailer(true);
   // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'nidhigunjal19@gmail.com';                     //SMTP username
    $mail->Password   = 'ilaslggeutwhczgd';                               //SMTP password
    $mail->SMTPSecure = "tls";            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('nidhi.1gunjal@gmail.com',$name);
    $mail->addAddress($email);     //Add a recipient
    

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Email verification project';
    $mail_template = "
        <h2>Successfully regiistered</h2>   
        <h5>Verify your email address to login with given link</h5>
        <br/><br/>
        <a href='http://localhost/email-verification/verify-email.php?token=$verify_token'> Click here</a>
    ";
    $mail->Body = $mail_template;
    $mail->send();
    //echo 'Message has been sent';
}

if(isset($_POST['register_btn']))
{
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $verify_token = md5(rand());
    
     //Email exists or not
    $check_email_query = "SELECT email FROM users WHERE email= '$email' LIMIT 1";
    $check_email_query_run = mysqli_query($con, $check_email_query);

    if(mysqli_num_rows($check_email_query_run) > 0)
    {
        $_SESSION['status'] = "Email id already exists";
        header("Location: register.php");
    }
    else
    {
        //Insert User / Registered Users data
        $query = "INSERT INTO users (name,phone,email,password,verify_token) VALUES ('$name','$phone','$email','$password','$verify_token')";
        $query_run = mysqli_query($con,$query);

        if($query_run)
        {
            sendemail_verify("$name","$email","$verify_token");
            $_SESSION['status'] = "Registration Successful. Please verify your email Address.";
            header("Location: register.php");
        }
        else
        {
            $_SESSION['status'] = "Registration Failed";
            header("Location: register.php");
        }
    } 
}
?>
