<?php
require_once "../db/db.php";

session_start();

if (!isset($_GET['id'])) {
    die("Invalid request");
}

$receiver_id = mysqli_real_escape_string($conn, $_GET['id']);

$sqlReceiver = "SELECT * FROM users WHERE id = '$receiver_id'";
$resultReceiver = $conn->query($sqlReceiver);

if (!$resultReceiver) {
    throw new Exception("Error fetching receiver data: " . $conn->error);
}

$rowReceiver = $resultReceiver->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id = $_SESSION['user_id'];
    $receivers_id = $receiver_id;
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    if (!empty($message)) {
        $queryInsert = "INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)";
        $stmtInsert = $conn->prepare($queryInsert);
        $stmtInsert->bind_param("iis", $sender_id, $receivers_id, $message);

        if ($stmtInsert->execute()) {
            header("Location: " . $_SERVER['PHP_SELF'] . '?id=' . $receiver_id);
            exit();
        } else {
            echo "Error: " . $stmtInsert->error;
        }

        $stmtInsert->close();
    }
}

$querySelect = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC";
$stmtSelect = $conn->prepare($querySelect);
$stmtSelect->bind_param("iiii", $_SESSION['user_id'], $receiver_id, $receiver_id, $_SESSION['user_id']);
$stmtSelect->execute();
$resultSelect = $stmtSelect->get_result();

if (!$resultSelect) {
    throw new Exception("Error fetching messages: " . $stmtSelect->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
</head>
<body>
    <div class="message-container">
        <h2>Send Message to <?php echo $rowReceiver['FirstName']; ?></h2>
        <div class="message-history">
            <?php
            while ($rowMessage = $resultSelect->fetch_assoc()):
                ?>
                <div class="message">
                    <span style="font-weight: 600"><?php echo $rowMessage['sender_id'] == $_SESSION['user_id'] ? 'You' : $rowReceiver['FirstName']; ?>:</span>
                    <span><?php echo $rowMessage['message']; ?></span>
                </div>
                <?php
            endwhile;
            ?>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $receiver_id); ?>" method="post">
            <input type="hidden" name="sender_id" value="<?php echo $_SESSION['user_id']; ?>">
            <label for="message">Message:</label>
            <textarea id="message" name="message" placeholder="Type your message" rows="4" required></textarea>

            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>

<?php
$stmtSelect->close();
$conn->close();
?>
