<?php
session_start(); // Start the session

// Redirect to log-out.php if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: log-out.php");
    exit();
}

// Include the database connection
$servername = "localhost"; 
$username = "root";        
$password = "12345";           
$dbname = "banhangmypham"; 
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Registration Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone = $_POST['phone']; // Get phone value from form

    // Generate a unique 10-character ID for the user
    $idUser = substr(uniqid('u'), 0, 10);

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO User (IdUser, NameUser, PasswordUser, PhoneUser) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $idUser, $email, $password, $phone); // Bind parameters

    if ($stmt->execute()) {
        header("Location: my-account.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Login Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM User WHERE NameUser = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['PasswordUser'])) {
            // Store user information in session
            $_SESSION['user_id'] = $row['IdUser'];
            $_SESSION['user_name'] = $row['NameUser'];

            // Check if the user is admin
            if ($email === 'admin@gmail.com') {
                $_SESSION['is_admin'] = true; // Set admin flag
            } else {
                $_SESSION['is_admin'] = false; // Not an admin
            }

            header("Location: product-list.php");
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "No user found with that email.";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <link rel="stylesheet" href="../../cdn.linearicons.com/free/1.0.0/icon-font.min.css" />
    <link rel="stylesheet" href="asset/css/owl.carousel.css" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" type="text/css" href="style68b3.css?ver=1" />
    <link rel="stylesheet" type="text/css" href="asset/css/flexslider.css" />
    <link rel="stylesheet" type="text/css" href="asset/css/bootstrap.min.css" />
    <link href="asset/css/all.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css?family=El+Messiri:400,500,600,700|Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,700,700i,800,800i,900,900i"
        rel="stylesheet" />
    <link rel="icon" href="favicon.png" type="image/png" />
    <title>My account</title>
</head>

<body>
    <!--header-->
    <?php include('header.php'); ?>
    <!--end header-->

    <!--title page-->
    <div class="title-page"
        style="background-image: url('imager/shop/Shop_3Columns-title.jpg'); background-position: center center; background-size: cover;">
        <div class="container">
            <div class="row">
                <div class="col-md-6 inner-title-page">
                    <h1>Shop</h1>
                    <p><span>Home</span> / Shop / My account</p>
                </div>
            </div>
        </div>
    </div>
    <!--end title page-->

    <!--content account-->
    <div class="container">
        <div class="row content-my-account">
            <div class="col-md-6 col-sm-12 col-12 content-my-account-left">
                <h2>Login</h2>
                <form action="my-account.php" method="POST">
                    <p>Username or email address <span>*</span></p>
                    <input type="text" name="email" class="text" required />
                    <p>Password<span>*</span></p>
                    <input type="password" name="password" class="text" required />
                    <button type="submit" name="login" class="bt">Login</button>
                    <span><input type="checkbox" class="checkbox" />&nbsp;&nbsp;Remember me</span>
                    <div style="margin-top: 20px">
                        <a href="javascript:void(0)" class="text-left lost-password">Lost your password?</a>
                    </div>
                </form>
            </div>
            <div class="col-md-6 col-sm-12 col-12 content-my-account-right">
                <h2>Register</h2>
                <form action="my-account.php" method="POST">
                    <p>Email address <span>*</span></p>
                    <input type="email" name="email" class="text" required />
                    <p>Password<span>*</span></p>
                    <input type="password" name="password" class="text" required />
                    <p>Phone<span>*</span></p>
                    <input type="text" name="phone" class="text" required />
                    <button type="submit" name="register" class="bt">Register</button>
                </form>
            </div>
        </div>
    </div>
    <!--end content account-->

    <!--footer-->
    <?php include('footer.php'); ?>
    <!--end footer-->

    <script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script type="text/javascript" src="asset/js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="asset/js/jquery.flexslider-min.js"></script>
    <script src="asset/js/owl.carousel.js"></script>
    <script type="text/javascript" src="asset/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="asset/js/custom.js"></script>
</body>

</html>