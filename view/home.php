<h1>Home Page</h1>

<?php
session_start();

if (!isset($_SESSION['is_authenticated'])) {
    header('Location: ../index.php');
    exit();
}

require_once "../db/db.php";

$uname = $_SESSION['user_name'];
echo ($_SESSION['user_id']);

$sql = "SELECT * FROM users WHERE Email <> '$uname'";
$result = $conn->query($sql);

if (!$result) {
    throw new Exception("Error fetching data: " . $conn->error);
}
?>

<h2><a href='./logout.php'>Logout</a></h2>

<?php
if ($result->num_rows > 0):
    ?>

    <p>Hello,
        <?php echo $uname; ?>!
    </p>
    <h3>You can Chat by clicking on Name of the users!</h3>
    <table border='1'>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Gender</th>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><a href="message_sending.php?id=<?php echo $row['id']; ?>">
                        <?php echo $row['FirstName']; ?>
                    </a></td>
                <td>
                    <?php echo $row['LastName']; ?>
                </td>
                <td>
                    <?php echo $row['Email']; ?>
                </td>
                <td>
                    <?php echo $row['Gender']; ?>
                </td>
            </tr>
            <?php
        endwhile;
        ?>
    </table>
    <?php
else:
    echo "No data available";
    session_destroy();
    header("Location: login.php");
    exit();
endif;
?>

<?php
$conn->close();
?>