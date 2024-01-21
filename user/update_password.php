<?php
    session_set_cookie_params(0);
    session_start();
    require '../functions/functions.php';
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header("Location: login.php");
        exit();
    }

    $conn = readWriteConnection();

    $userEmail = $_SESSION['username'];

    // Estrai l'hash della password corrente dal database
    $passQuery = "SELECT password FROM users WHERE email = ?";
    $passParam = "s";
    $passElem = array($userEmail);
    $passResult = execStmt($conn, $passQuery, $passElem, $passParam);
    if(!$passResult)
        die("something went wrong");
    $row = $passResult->fetch_assoc();
    $current_password_hash = $row['password'];

    // Verifica se il modulo è stato inviato
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ricevi i dati dal modulo
        $old_password = clean_input($_POST['old_password']);
        $new_password = clean_input($_POST['new_password']);
        $confirm_password = clean_input($_POST['confirm_password']);

        if (password_verify($old_password, $current_password_hash)) {
            if ($new_password === $confirm_password) {
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                $newPassQuery = "UPDATE users SET password = ? WHERE email = ?";
                $newPassParams = "ss";
                $newPassElem = array($new_password_hash, $userEmail);
                if(!execStmt($conn, $newPassQuery, $newPassElem, $newPassParams))
                    die("something went wrong");
               
                // Reindirizza alla pagina di successo o al pannello utente
                header('Location: ../functions/logout.php');
                exit();
            } else 
                $error_message = "new passwords does not match.";   
        } else 
            $error_message = "old password is not correct.";
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="../css/update_password_style.css">
    <script src="logout.js"></script>
    <title>Update Password</title>
</head>
<body>
    <header>
        <button type="button" onclick="location.href='../index.php'">Home</button>
        <div class="header">
            <h1>Update Password</h1>
        </div>
    </header>
    <?php if (isset($error_message)) : ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    
    <div class="container">
        <form method="post" action="update_password.php">

            <label for="old_password">Old password:</label>
            <input type="password" name="old_password" required><br>

            <label for="new_password">New password:</label>
            <input type="password" name="new_password" required><br>
            
            <label for="confirm_password">Confirm new password:</label>
            <input type="password" name="confirm_password" required><br>

            <input type="submit" value="Update password">
        </form>
    </div>
</body>
</html>