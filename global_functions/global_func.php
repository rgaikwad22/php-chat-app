<?php
// functions stats here
function validate() {
    global $chName, $chLName, $chEmail, $chpass, $chconfirmpass, $chMulty;

    if ($chName && $chLName && $chEmail && $chpass && $chconfirmpass && $chMulty) {
        return true;
    }
    return false;
}


function checkUserName ($val, &$error, $errMessage) {
    if (empty($val)) {
      $error = $errMessage;
    } else if (strlen($val) < 3) {
      $error = "Length of user name shouled be greater than 3 characters.";
      return false;
    }
    return true;
  }

function checkName($val, &$error, $errMessage) {
    if (empty($val)) {
        $error = $errMessage;
    } else if (!preg_match("/^[a-zA-Z-']*$/", $val)) {
        $error = "Please enter valid name.";
        return false;
    } else if (strlen($val) < 3 || strlen($val) > 10) {
        $error = "Length of name shouled be greater than 5 and less than 15.";
        return false;
    }
    return true;
}

function checkEmail($emailval, &$error) {
    global $conn;
    $email = mysqli_real_escape_string($conn, $emailval);
    $checkQuery = "SELECT * FROM users WHERE Email = '$email'";
    $checkResult = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($checkResult) > 0) {
        $error = "Error: Email already exists!";
    } else if (empty($emailval)) {
        $error = "Email is required.";
        return false;
    } else if (!preg_match("/^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/", $emailval)) {
        $error = "Please enter valid valid email.";
        return false;
    }
    return true;
}

function checkPass ($val, &$error, $errMessage) {
    if (empty($val)) {
        $error = $errMessage;
        return false;
    } else if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/", $val)) {
        $error= "Password must be 8 char long and have at least one uppercase, one lowercase, one special and one number included.";
        return false;
    }
  
    return true;
}

function checkConfirmPass($val, $confirmval, &$error) {
    if ($val !== $confirmval) {
        $error = "Confirm password should be equal to password!";
        return false;
    } else if (empty($val)) {
        $error = "Password is required!";
        return false;
    } else if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[a-zA-Z]).{8,}$/", $val)) {
        $error = "Valid password is required!";
        return false;
    }
    return true;
}

function checkMultiple($val, &$error, $select) {
    if (empty($val)) {
        $error = $select;
        return false;
    }
    return true;
}

function checkFile() {
    global $filename, $upload;
    // file validation starts 
    $filename = $_FILES["doc"]["name"];
    $tempname = $_FILES["doc"]["tmp_name"];
    $filesize = $_FILES["doc"]["size"];
    $fileext = explode(".", $filename);
    $fileextcheck = strtolower(end($fileext));
    $extensions = array("jpg", "png", "jpeg");

    if (in_array($fileextcheck, $extensions) === true && $filesize < 2097152) {
        move_uploaded_file($tempname, "uploads/" . $filename);
        $upload = "File uploaded successfully";
        return true;
    } else {
        $upload = "File extension should be .png or .pdf or File size less than 2MB";
        return false;
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateSecureToken() {
    return bin2hex(random_bytes(32));
}
?>