<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=IM+Fell+English">
        <link rel="stylesheet" href="css/login_style.css">
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="logout.js"></script>
        <title>Login</title>
    </head>
    <body>
       
        <button class="submit-btn" onclick="location.href='index.php'">Home</button>
        
        <div class="container">
            <?php
            require('functions.php');
            if(IfLogged()){
                header("Location: http://localhost/index.php");
                exit();
            }


                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $email = clean_input($_POST["email"]);
                    $password = clean_input($_POST["pass"]);

                    // Connessione al database con prepared statement
                    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                    try {
                        error_reporting(0);
                        $conn = readWriteConnection();
                
                        $query = "SELECT email, password FROM users WHERE email = ?";
                        $params = "s";
                        $elem = array($email);
                        $result = execStmt($conn, $query, $elem, $params);
                        if (!$result)
                            die("error in select query");

                        $row = $result->fetch_assoc();
                        $storedEmail = $row['email'];
                        $storedPassword = $row['password'];

                        if (($result->num_rows === 1) && (password_verify($password, $storedPassword))) {

                            session_start();
                            $_SESSION['logged_in'] = true;
                            $_SESSION['username'] = $email;
                            $_SESSION['email'] = $email;
                        
                            $temp = session_id();
                            //update della tabella cart cambiando il session id con l'email dell'utente loggato
                            $updateCartQuery = "UPDATE cart SET email = ? WHERE email = ?";

                            
                            $updateCartParams = "ss";
                            $updateCartElem = array($email, $temp);
                            $updateCartResult = execStmt($conn, $updateCartQuery, $updateCartElem, $updateCartParams);
                            //controllare valori di ritorno di execStmt forse si rompe tutto

                            //update della tabella wishlist cambiando il session id con l'email dell'utente loggato

                            header("Location: http://localhost/index.php");
                            exit();
                        } else
                            displayError("Wrong Email or Password");

                    } catch (Exception $e) {
                        // Registra gli errori nel file di log personalizzato
                        error_log("Error in query: " . $e->getMessage() . "\n", 3, "error_log");
                        displayError("An error occurred while logging in. Please try again later.");
                    } finally {
                        $conn->close();
                    }
                }

                function displayError($message) {
                    echo '<div style="color: red;">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</div>';
                }
            ?>
        
            <form id="registration-form" method="post">
                <label for="email">Email:</label>
                <input type="email" name="email" required>

                <label for="pass">Password:</label>
                <input type="password" name="pass" required>

                <a href="registration.php">Don't have an account? Register here!</a>

                <input type="submit" name="submit" class="submit-btn" value="Login">
            </form>
        </div>
    </body>
</html>