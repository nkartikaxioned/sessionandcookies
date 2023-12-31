<?php
session_start();

if (isset($_SESSION['sessionID']) && $_SESSION['sessionID'] === $_COOKIE['sessionID']) {
  if ($_SESSION['user'] === 'admin') {
    header("Location: adminlistingpage.php");
  } else {
    header("Location: view.php");
  }
  exit();
}

$email = $password = $errorMsg = $passwordErr = $emailErr = "";

function validateInput($data)
{
  $data = trim($data);
  $data = htmlspecialchars($data);
  return $data;
}

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
  }
  $emailErrorFlag = 0;
  return "";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  require_once("dbconnect.php");
  $email = $_POST['email'];
  $password = $_POST['password'];
  $emailErrorFlag = $passErrorFlag = 0;
  $emailErr = validateEmail($dbconnection, $email);
  $passwordErr = validatePassword($password);
  
  function loginUser($username, $sessionID, $redirectLocation) {
    $_SESSION['user'] = $username;
    $_SESSION['sessionID'] = $sessionID;
    setcookie('sessionID', $sessionID, time() + 3600, '/', '', false, true);
    header("Location: $redirectLocation");
    exit();
}
  if ($emailErrorFlag === 0 && $passErrorFlag === 0) {
    try {
      $sql = "SELECT * FROM users WHERE email = ? AND password = ?";
      $stmt = mysqli_prepare($dbconnection, $sql);
      mysqli_stmt_bind_param($stmt, "ss", $email, $password);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      $rowCount = mysqli_stmt_num_rows($stmt);
      $sessionID = bin2hex(random_bytes(32));
      $_SESSION["logged"] = "OK";
      if ($email === "admin@email.com" && $password === "P@ssw0rd") {
        loginUser('admin', $sessionID, 'adminlistingpage.php');
    } elseif ($rowCount > 0) {
        loginUser($email, $sessionID, 'view.php');
    } else {
      $errorMsg = "Invalid username and password!";
      session_destroy();
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
