<?php
    session_set_cookie_params(0);
    session_start();
    require('../functions/functions.php');
    if (!isset($_SESSION['username'])) {
        echo "<p>You need to be logged in to make a donation.</p>";
        exit();
    }
    $conn = readWriteConnection();
    
    $firstname = clean_input($_POST['firstname']);
    $lastname = clean_input($_POST['lastname']);
    $credit_card_number = clean_input($_POST['credit_card_number']);
    $donation_amount = clean_input($_POST['donation_amount']);

    //cehck if credit card number is valid
    if (!preg_match("/^[0-9]{16}$/", $credit_card_number)) {
        echo "<p>Credit card number not valid.</p>";
        exit();
    }

    if (!is_numeric($donation_amount) || $donation_amount <= 0) {
        echo "<p>Donation amount not valid.</p>";
        exit();
    }
    // Check if the donation amount is valid
    $totalAmountResult = $conn->query("SELECT SUM(donation_amount) AS total_amount FROM crowdfunding");
        if (!$totalAmountResult)
            die("Error in total amount query");
    $totalAmountRow = $totalAmountResult->fetch_assoc();
    $total = $totalAmountRow['total_amount'];
    $target = 10000;
    if($total + $donation_amount > $target){
        echo "<p>Donation amount not valid.</p>";
        exit();
    }
    
 
    // Insert donation
    $username = $_SESSION['username'];
    $insertDonationQuery = "INSERT INTO crowdfunding (email, firstname, lastname, donation_amount) VALUES (?, ?, ?, ?)";
    $insertDonationParams = "sssi";
    $insertDonationElements = array($username, $firstname, $lastname, $donation_amount);
    if (!execStmt($conn, $insertDonationQuery, $insertDonationElements, $insertDonationParams))
        die("something went wrong");

    $conn->close();
   
    header("Location: crowdfunding.php");
    exit();

?>