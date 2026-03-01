<?php session_start();
 $sessionID=$_SESSION["userID"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
</head>
<body>

<form action="viewProfile.php?userID=<?php echo $sessionID; ?>" method="POST">
    <button type="submit" name="viewBtn">View Your Profile</button>
</form>

</body>
</html>
<?php

 if(isset($_POST['viewBtn'])){
    header("Location:viewProfile.php");
   
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
       <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <div>
        <form action="userTimeline.php" method="POST">

    <button name="logOutBtn">Log Out</button> 
    <h1>Welcome,<?php echo $_SESSION['userName'];?></h1> 
<textarea name="Post"placeholder="Share Something . ." ></textarea>
<br>
<Button type="Submit" name="postButton" style="width: 750px;height: 50px">Post</Button>

<h1>Timeline</h1>

</form>

 </div>
    
</body>
</html>

<?php
if(isset($_POST['logOutBtn'])){
    session_destroy();
    header("Location:Login2.php");
   
}

include('Database.php');

 mysqli_select_db($conn,$databaseName);

$userPosts="CREATE TABLE IF NOT EXISTS posts(
                    postID INT AUTO_INCREMENT PRIMARY KEY,
                    userPost TEXT NOT NULL,
                    userID INT NOT NULL,
                    posted_At TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY(userID) REFERENCES users( userID)
                    )";

     mysqli_query($conn,$userPosts);


if($_SERVER["REQUEST_METHOD"]=="POST"){
    if(isset($_POST["postButton"])){

        $userPost=trim($_POST["Post"]);


        if(!empty($userPost)){
             $userPost = mysqli_real_escape_string($conn,$userPost); 

            $insertPost="INSERT INTO posts(userPost,userID)
                         VALUES('$userPost',$sessionID)";

           if(mysqli_query($conn,$insertPost)){
            header('Location:userTimeline.php');
            }
            
            else{
                echo "Post not inserted";
            }
      }
   }
   
   }

              
 $postsQuery="SELECT * FROM posts ORDER BY posted_At DESC";

   $postResults=mysqli_query($conn,$postsQuery);

   if($postResults){
        if(mysqli_num_rows($postResults)>0){

             while($fetchResults=mysqli_fetch_assoc($postResults)){;

             echo "<div style='border:1px solid #ccc; margin:10px; padding:10px;'>";

             echo "<p>".$fetchResults['userPost']."</p>";
             echo "<p><small>".$fetchResults['posted_At']."</small></p>";

             echo "</div>";

             }

        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>searchBar</title>
       <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <div>
        <form action="userTimeline.php" method="POST">

   
<h1>Search Users</h1>

<input type="text" name="searchBar" placeholder="Search by name or email">
<button type="Submit" name="searchButton">Search</button>

<h1>Search Results</h1>


</form>

 </div>
    
</body>
</html>

<?php
if($_SERVER["REQUEST_METHOD"]=="POST"){
    if(isset($_POST["searchButton"])){
        $search=trim($_POST['searchBar']);

        if(!empty($search)){
    
        $searchQuery = "SELECT * FROM users WHERE  userName  LIKE '%$search%' OR Email LIKE '%$search%'";
         $searchResult = mysqli_query($conn, $searchQuery);

         if($searchResult && mysqli_num_rows($searchResult)>0){

            while($row=mysqli_fetch_assoc($searchResult)){
                $userID=$row['userID'];
                $userName=$row['userName'];
                $Email=$row['Email'];

                echo"Name: ". $userName."<br>";
                //"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp".
                echo "Email: ".$Email."<br>";
                echo "<a href='viewProfile.php?userID=$userID'>"."<button>view profile</button>"."</a><br>";
                echo"<a href='Inbox.php?userID=$userID'><button>Messsages</button></a><br><br>";

            }

         }
          else{
                echo "<h4>User not found</h4>";
            }

        mysqli_close($conn);

        }
    }
}


?>
