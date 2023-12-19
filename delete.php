<?php require_once('dbconnect.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    $id = $_GET['id'];
    var_dump($id);
    if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
        try {
            $sql = "Delete FROM users where srno = $id";
            $stmt = mysqli_query($dbconnection, $sql);
            if (!$stmt) {
                throw new Exception("Error in query: " . mysqli_error($dbconnection));
            }
        } catch (Exception $e) {
            echo  "Error :" . $e->getMessage();
        } finally {
            mysqli_close($dbconnection);
            header("Location: adminlistingpage.php");
        }
    }
    ?>
</body>

</html>
