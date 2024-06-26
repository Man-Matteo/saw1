<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=IM+Fell+English">
        <link rel="stylesheet" href="css/registration_style.css">
        <title>Registration</title>
    </head>
    <body>
        <?php include 'partials/navbar.php'; ?>
        <div class="container">
            <?php
                
                require 'functions/functions.php';
                if(IfLogged()){
                    header("Location: index.php");
                    exit();
                }    
                if ($_SERVER["REQUEST_METHOD"] == "POST") {

                    $firstname = clean_input($_POST["firstname"]);
                    $lastname = clean_input($_POST["lastname"]);
                    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                    if (!$email)
                        die('Error: invalid email.');
                    $password = clean_input($_POST["pass"]);
                    $confirm = clean_input($_POST["confirm"]);

                    // Controllo se le password coincidono
                    if ($password !== $confirm)
                        die('<div class="error">Error: passwords does not match.</div>');
                    
                    // Hash della password
                    $hashed_pass = password_hash($password, PASSWORD_DEFAULT); 

                    // Connessione al database                    
                    $conn = readWriteConnection();

                    try{
                        $conn -> begin_transaction();
                        
                        //controllo che l'email non sia già presente nel database
                        $checkQuery = "SELECT email FROM users WHERE email = ?";
                        $checkElem = array($email);
                        $checkParams = "s";
                        $checkResult = execStmt($conn, $checkQuery, $checkElem, $checkParams);
                       
                        /*if (!$checkResult)
                            die('<div class="error">something went wrong.</div>');
                        */
                        if ($checkResult -> num_rows > 0)
                            die('<div class="error">Error: email already in use.</div>');
                        
                        // Inserimento dei dati nel database
                        $insertQuery = "INSERT INTO users(firstname, lastname, email, password) VALUES (?, ?, ?, ?)";
                        $insertParams = "ssss";
                        $insertElem = array($firstname, $lastname, $email, $hashed_pass);
                        $insertResult = execStmt($conn, $insertQuery, $insertElem, $insertParams);
                      
                        if (!$insertResult)
                            die('<div class="error">Error in insert query.</div>');
                       
                        $conn -> commit();

                        
                        $conn->close();
                        header("Location: login.php?registration=success");
                        exit();
                    }
                    catch(Exception $e){
                        echo "This Email is not available!!!";
                        $conn -> rollback();
                    }
                }
            ?>

            <form id="registration-form" method="post" onsubmit="return validateConfirmPassword()">
                <label for="firstname">Firstname</label>
                <input type="text" name="firstname" pattern="\w{2,16}" title="The name must contain at least 2 alphanumeric characters." required>

                <label for="lastname">Lastname</label>
                <input type="text" name="lastname" pattern="\w{2,16}" title="The surname must contain at least 2 alphanumeric characters" required>

                <label for="email">Email</label>
                <input type="email" name="email" id="email" title="Enter a valid email address." required>

                <label for="pass">Password</label>
                <input type="password" name="pass" id="pass" required>

                <label for="confirm">Confirm password</label>
                <input type="password" name="confirm" id="confirm" required>

                <input type="submit" name="submit" class="submit-btn" value="Register">
            </form>

            <p id="message"></p>
        </div>

        <script>
            function validateConfirmPassword() {
                var password = document.getElementById("pass").value;
                var confirm = document.getElementById("confirm").value;
                var message = document.getElementById("message");
                if (password !== confirm) {
                    if(message)
                        message.innerHTML = "Passwords do not match.";
                    return false;
                }
                return true;
            }
        </script>
        
        <?php include 'partials/footer.php'; ?>
    </body>
</html>