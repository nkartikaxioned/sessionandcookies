<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Start a session to store user data
session_start();
$emailErrorFlag = $passErrorFlag = 0;
$email = $password = $errorMsg = $passwordErr = $emailErr = "";
// Function to validate and sanitize input
function validateInput($data)
{
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if email exists in the database
function emailExists($dbconnection, $email)
{
    $stmt = $dbconnection->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows;
    $stmt->close();
    return $count > 0;
}

// Function to validate password
function validatePassword($password)
{
    global $passErrorFlag;
    $password = validateInput($password);

    if (empty($password)) {
        $passErrorFlag = 1;
        return "Password is required";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@#$%^&+=!])[A-Za-z\d@#$%^&+=!]{8,}$/", $password)) {
        $passErrorFlag = 1;
        return "Enter a valid password";
    }

    $passErrorFlag = 0;
    return "";
}

// Function to validate email
function validateEmail($conn, $email)
{
    global $emailErrorFlag;
    $email = validateInput($email);

    if (empty($email)) {
        $emailErrorFlag = 1;
        return "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErrorFlag = 1;
        return "Invalid email format";
    } elseif (emailExists($conn, $email)) {
        $emailErrorFlag = 1;
        return "Email already exists";
    }

    $emailErrorFlag = 0;
    return "";
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "dbconnect.php";

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Initialize error flags
    $emailErrorFlag = $passErrorFlag = 0;

    // Validate email and password
    $emailErr = validateEmail($dbconnection, $email);
    $passwordErr = validatePassword($password);

    // If there are no validation errors, proceed with authentication
    if ($emailErrorFlag === 0 && $passErrorFlag === 0) {
        try {
            // Perform authentication based on your business logic
            // For now, let's assume successful authentication for admin
            if ($email === "admin@email.com" && $password === "P@ssw0rd") {
                $_SESSION['user'] = 'admin'; // Store user data in the session
                header("Location: adminlistingpage.php");
                exit();
            } else {
                header("Location: view.php");
                exit();
            }
        } catch (Exception $e) {
            $errorMsg = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cookies And Session</title>
    <!-- Add your meta tags and links here -->
</head>
<body>
<div class="container">
    <header>
        <h1 style="font-weight:bold; font-size:25px;">Login Page</h1><br>
    </header>
    <main>
        <section class="login-form">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <label for="email">Email :</label>
                <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <span class="error"><?php echo $emailErr; ?></span><br><br>
                <label for="password">Password :</label>
                <input type="password" name="password">
                <span class="error"><?php echo $passwordErr; ?></span><br><br>
                <input type="submit" value="Submit">&nbsp;
                <a href="./register.php">Register</a>
            </form>
        </section>
        <p class="main-error"><?php echo isset($errorMsg) ? $errorMsg : ''; ?></p>
    </main>
</div>
<script src="js/vendors/jquery-1.8.3.min.js"></script>
<script src="./assets/js/script.js"></script>
</body>
</html>
