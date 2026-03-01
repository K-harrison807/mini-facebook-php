
<?php
session_start();

include('Database.php');
 $incorrectPass="";

if($_SERVER["REQUEST_METHOD"]=="POST"){
    if(isset($_POST['loginButton'])){

        $email=trim($_POST['Email']);
        $password=trim($_POST['UserPassword']);

         mysqli_select_db($conn,$databaseName);

       $verify="SELECT * FROM users WHERE Email='$email'";
       $queryResults=mysqli_query($conn,$verify);

       if($queryResults){

         if(mysqli_num_rows($queryResults)>0){

             $row=mysqli_fetch_assoc($queryResults);

              if(password_verify($password,$row['Password'])){
                $_SESSION['userID']=$row['userID'];
                $_SESSION['userName']=$row['userName'];

                header("Location:userTimeline.php?userID=".$row['userID']);

                //echo "corect details";
                  
                }
                else{
                    $incorrectPass=" incorrect password";

                    }
            }
       }
       else{
        echo "query failed". mysqli_error($conn);
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
    <title>login</title>
<link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
    
     <style>
        .error{
            color: red;
        }
     </style>

</head>
<body>
    <div>
        <form action="Login2.php" method="POST">
    <h1>Login</h1>
    <span class="error"><?php echo $incorrectPass; ?></span>
    <br>

     <input type="email" name="Email" placeholder="Email">
      <br>
    <input type="password" name="UserPassword" placeholder="password">
    <br>
    <br>

    <Button type="submit" name="loginButton">Login </Button> &nbsp &nbsp &nbsp <a href="Registration.php"><Button type="button" name="registerButton">Create Account</Button></a>
    <br>
    <br>

   <a href="help.php"><p>Forgot password?</p></a> 
    </form>
    </div>
    
</body>
</html>