<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_logged_in = isset($_SESSION['username']) ? $_SESSION['username'] : null;

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$user_logged_in = isset($_SESSION['username']) ? $_SESSION['username'] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #222222;
        padding: 20px;
        height: 8vh;
        width: 100vw;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);

    }

    button {
        background-color: transparent;
        padding: 0px;
        border: 0px;
        color: white;
    }

    i {
        font-size: 1.5rem;
    }

    span {
        font-size: larger;
    }

    .right-section {
        display: flex;
    }
    </style>
</head>

<body>
    <header class="header">
        <div class="left-section">
            <button type="button">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
        <div class="right-section">
            <button type="button">
                <i class="fas fa-user mr-3"></i>
            </button>
            <form action="" method="POST">
                <button type="submit" name="logout">
                    <i class="fa-solid fa-lock mr-2"></i><span>Log Out</span>
                </button>
            </form>
        </div>
    </header>