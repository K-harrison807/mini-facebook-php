<?php
include('Database.php');

$errorUsername= $errorEmail= $errorEmailValidation= $errorMsg= $errorPassword= $errorConfirmPass= $matchPass="";

if($_SERVER['REQUEST_METHOD']=="POST"){
    
    if(isset($_POST['registerButton'])){

        $userName=trim($_POST['Username']);
        $Email=trim($_POST['Email']);
        $Password=trim($_POST['Userpassword']);
        $confirmPassword=trim($_POST['confirmPassword']);
       
        if(empty($userName)){
            $errorUsername="Field required";
        }
          else{
                $userName=filter_var($userName, FILTER_SANITIZE_SPECIAL_CHARS);

             }
        if(empty($Email)){
            $errorEmail="Field required";
        }
             else{
                  $Email=filter_var($Email, FILTER_SANITIZE_EMAIL);

                    if(!filter_var($Email, FILTER_VALIDATE_EMAIL)){
                         $errorEmailValidation="Invalid email";

                       }
                    }

        if(empty($Password)){
             $errorPassword="Fild required";

        }
              else{
                    $Password=filter_var($Password, FILTER_SANITIZE_SPECIAL_CHARS);

                 }

        if(empty($confirmPassword)){

             $errorConfirmPass="Field required";
        }
             else{

                $confirmPassword=filter_var($confirmPassword, FILTER_SANITIZE_SPECIAL_CHARS); 

                 }

        if($confirmPassword != $Password ){
            $matchPass="Passwords do not match";
            
        } 
        else{
             $hashedPassword=password_hash($Password,PASSWORD_DEFAULT);
        }

        $sql="CREATE DATABASE IF NOT EXISTS Socialdb";

        mysqli_query($conn,$sql);
    
         mysqli_select_db($conn,$databaseName);

$createTable="CREATE TABLE IF NOT EXISTS users(
                        userID INT AUTO_INCREMENT PRIMARY KEY,
                        userName VARCHAR(255) NOT NULL,
                        Email VARCHAR(255) NOT NULL UNIQUE,
                        Password VARCHAR(255) NOT NULL
                   )";
       
    mysqli_query($conn,$createTable);

    if (!$errorUsername && !$errorEmail && !$errorEmailValidation && !$errorPassword && !$errorConfirmPass && !$matchPass){

    $insertUser="INSERT INTO users(userName, Email, Password)
                             VALUES('$userName', '$Email','$hashedPassword')";

        if(mysqli_query($conn,$insertUser)){
            echo "<h4 style='color:green;'>Registered successfully, You can login</h4>";
        }
        
        else{
                if(mysqli_errno($conn)){  
                    $errorMsg="Email already registered";
                }
        }
    }

 mysqli_close($conn);

    }
             
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
   <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
     <style>
        .error{
            color: red;
        }
     </style>
</head>
<body>
    <div>
        <form action="Registration.php" method="POST">
    <h1>Create Account</h1>
    <br>
      <br>

        <span class="error">*<?php echo $errorUsername; ?> </span>
      <input type="text" name="Username" placeholder="Username">
        <br>
            <br>
            <span class="error">*<?php echo $errorEmail; ?></span>
            <span class="error"><?php echo $errorEmailValidation; ?></span>
            <span class="error"><?php echo $errorMsg; ?></span>
        <input type="email" name="Email" placeholder="Email">
            <br>
                <br>
                <span class="error">*<?php echo $errorPassword; ?></span>
            <input type="password" name="Userpassword" placeholder="password">
                <br>
                    <br>
                    <span class="error"><?php echo $matchPass; ?></span>
                     <span class="error">*<?php echo $errorConfirmPass; ?></span>
                <input type="password" name="confirmPassword" placeholder="confirm Password">
                <br>    
                    <br>

                <Button type="Submit" name="registerButton">Create</Button> &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp <a href="Login2.php"><button type="button" name='loginB'>Login</button></a>
            </form>
        </div>

    
</body>
</html>

