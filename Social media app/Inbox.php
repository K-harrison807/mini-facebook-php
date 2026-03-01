<?php
session_start();
include("Database.php");
mysqli_select_db($conn, $databaseName);

$createTable="CREATE TABLE IF NOT EXISTS messages (
                                    messageID INT AUTO_INCREMENT PRIMARY KEY,
                                    userMessages TEXT NOT NULL,
                                    senderID INT NOT NULL,
                                    receiverID INT NOT NULL,
                                    sentAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                    FOREIGN KEY (senderID) REFERENCES users(userID),
                                    FOREIGN KEY (receiverID) REFERENCES users(userID)
)";
mysqli_query($conn,$createTable);

$sessionID = $_SESSION['userID'] ?? null;
if (!$sessionID) {
    die("You must be logged in.");
}


if (isset($_POST["backBtn"])) {
    header("Location: userTimeline.php");
    exit();
}


$userID = $_GET['userID'] ?? null;
if (!$userID) {
    die("No user selected.");
}
$userID = (int)$userID;


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["messageBtn"])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $message = mysqli_real_escape_string($conn, $message);
        $insertMessage = "INSERT INTO messages (userMessages, senderID, receiverID)
                          VALUES ('$message', $sessionID, $userID)";
        mysqli_query($conn, $insertMessage);
        header("Location: Inbox.php?userID=$userID");
        exit();
    }
}

$fetchMessages = "
SELECT * FROM messages
WHERE (senderID = $sessionID AND receiverID = $userID)
   OR (senderID = $userID AND receiverID = $sessionID)
ORDER BY sentAt ASC
";
$results = mysqli_query($conn, $fetchMessages);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inbox</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .chat-container { max-width: 600px; margin: 20px auto; }
        .message { padding: 10px; border-radius: 15px; margin: 5px 0; max-width: 70%; word-wrap: break-word; }
        .sent { background-color: #0c9631ff; color: white; margin-left: auto; text-align: right; }
        .received { background-color: #47449fff; color: white; margin-right: auto; text-align: left; }
        .chat-box { display: flex; gap: 10px; margin-top: 10px; }
        textarea { width: 100%; }
    </style>
</head>
<body>
<div class="chat-container">
    <h2>Conversation</h2>

    <?php
    if ($results && mysqli_num_rows($results) > 0) {
        while ($msg = mysqli_fetch_assoc($results)) {
            $class = ($msg['senderID'] == $sessionID) ? "sent" : "received";
            echo "<div class='message $class'>";
            echo htmlspecialchars($msg['userMessages']);
            echo "</div>";
        }
    } else {
        echo "<p>No messages yet.</p>";
    }
    ?>


    <form method="POST">
            <textarea name="message" placeholder="Write message..."></textarea>
            <button type="submit" name="messageBtn">
                Send <i class="fa-solid fa-paper-plane"></i>
            </button>

        <button type="submit" name="backBtn">Go Back</button>
    </form>
</div>
</body>
</html>

<?php
mysqli_close($conn);
?>
