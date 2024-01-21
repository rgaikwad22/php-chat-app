<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Form Validation</title>

    <meta name="description" lang="en" content="">
    <meta name="keywords" lang="en" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="shortcut icon" href="favicon.ico" />

    <!--font-awesome link for icons-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Default style-sheet is for 'media' type screen (color computer display).  -->
    <link rel="stylesheet" media="screen" href="../css/style.css">
</head>

<body>
    <!-- php starts here  -->
    <?php
    require_once "../db/db.php";

    // variable created globally
    $fnameErr = $lnameErr = $emailErr = $genderErr = $passErr = $confirmPassErr = "";
    $fname = $lname = $email = $password = $confirmpass = $gender = "";
    // $edit_id = null;
    
    require_once "../global_functions/global_func.php";
    

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $fnameval = $_POST["firstname"];
        $lnameval = $_POST["lastname"];
        $emailval = $_POST["email"];
        $passval = $_POST["password"];
        $confirmpassval = $_POST["confirmpass"];
        $genderval = $_POST["gender"];

        $fname = test_input($fnameval);
        $lname = test_input($lnameval);
        $email = test_input($emailval);
        $password = password_hash(test_input($passval), PASSWORD_DEFAULT);
        $confirmpass = test_input($confirmpassval);
        $gender = test_input($genderval);

        $chName = checkName($fnameval, $fnameErr, "First name is required.");
        $chLName = checkName($lnameval, $lnameErr, "Last name is required.");
        $chEmail = checkEmail($emailval, $emailErr);
        $chpass = checkPass($passval, $passErr, "Password is required.");
        $chconfirmpass = checkConfirmPass($confirmpassval, $passval, $confirmPassErr);
        $chMulty = checkMultiple($genderval, $genderErr, "Please select your gender.");

        try {
            if (validate()) {
                // Prepare the SQL statement
                $sql = "INSERT INTO users (FirstName, LastName, Email, Password, Gender) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);

                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . mysqli_error($conn));
                }

                // Bind parameters to the prepared statement
                mysqli_stmt_bind_param($stmt, "sssss", $fname, $lname, $email, $password, $gender);

                // Execute the prepared statement
                $result = mysqli_stmt_execute($stmt);
                var_dump($result);

                if (!$result) {
                    throw new Exception("Error inserting record: " . mysqli_stmt_error($stmt));
                }

                // Close statement
                mysqli_stmt_close($stmt);

                // Close connection
                mysqli_close($conn);
                header("Location: ../index.php");
                exit();
            }
        } catch (Exception $e) {
            echo "Exception: " . $e->getMessage();
        }
    } else {
        try {
            // If unauthorized access, throw an exception
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                throw new Exception("Invalid Request Method");
            }
        } catch (Exception $e) {
            // Handle unauthorized access exception
            echo "Error: " . $e->getMessage();
        }
    }
    ?>
    <!--container starts here-->
    <div class="container">
        <!--main starts here-->
        <main>
            <section class="form-section">
                <div class="wrapper">
                    <h1 class="section-heading">Sign Up Form</h1>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form"
                        enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo isset($edit_id) ? $edit_id : ''; ?>">
                        <div class="input-grp">
                            <label for="first-name">First Name: <span class="requred">*</span></label>
                            <input type="text" id="first-name" name="firstname" class="input-write"
                                placeholder="Enter your name" value="<?php echo $fname ?>">
                            <span class="error">
                                <?php echo $fnameErr; ?>
                            </span>
                        </div>
                        <div class="input-grp">
                            <label for="last-name">Last Name: <span class="requred">*</span></label>
                            <input type="text" id="last-name" name="lastname" class="input-write"
                                placeholder="Enter last name" value="<?php echo $lname; ?>">
                            <span class="error">
                                <?php echo $lnameErr; ?>
                            </span>
                        </div>
                        <?php if (empty($_GET['id'])): ?>
                            <div class="input-grp">
                                <label for="email">Email: <span class="requred">*</span></label>
                                <input type="email" id="email" name="email" class="input-write"
                                    placeholder="Enter your email" value="<?php echo $email; ?>">
                                <span class="error">
                                    <?php echo $emailErr; ?>
                                </span>
                            </div>
                            <div class="input-grp">
                                <label for="password">Password: <span class="requred">*</span></label>
                                <input type="password" id="password" name="password" class="input-write"
                                    placeholder="Set your password" value="<?php $password; ?>">
                                <span class="error">
                                    <?php echo $passErr; ?>
                                </span>
                            </div>
                            <div class="input-grp">
                                <label for="confirm-password">Confirm Password: <span class="requred">*</span></label>
                                <input type="password" id="confirm-password" name="confirmpass" class="input-write"
                                    placeholder="Confirm your password" value="<?php $confirmpass; ?>">
                                <span class="error">
                                    <?php echo $confirmPassErr; ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <div class="input-grp gender-input">
                            <label for="gender">Gender: <span class="required">*</span></label>
                            <?php
                            $genders = ["male", "female", "other"];
                            foreach ($genders as $genderOption) {
                                $isChecked = isset($gender) && $gender == $genderOption;
                                ?>
                                <div>
                                    <input type="radio" id="<?php echo $genderOption; ?>" name="gender" <?php echo $isChecked ? "checked" : ""; ?> value="<?php echo $genderOption; ?>">
                                    <label for="<?php echo $genderOption; ?>">
                                        <?php echo ucfirst($genderOption); ?>
                                    </label>
                                </div>
                            <?php } ?>
                            <span class="error">
                                <?php echo $genderErr; ?>
                            </span>
                        </div>
                        <input type="submit" name="submit" value="<?php echo ($edit_id ? 'Update' : 'Sign Up'); ?>"
                            class="btn submit-btn">
                        <span class="register-link">Already have an account? <a href="../index.php">Login!</a></span>
                    </form>
                    <br>
                </div>
                <?php $confirmpass ?>
            </section>
        </main>
        <!--main ends here-->
    </div>
    <!--container ends here-->
    <script src="js/script.js"></script>
</body>