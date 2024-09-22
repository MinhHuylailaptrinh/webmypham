<?php
session_start(); // Start the session

// Kiểm tra quyền admin
if (!isset($_SESSION['user_name']) || $_SESSION['user_name'] !== 'admin@gmail.com') {
    header("Location: error-page.php"); // Chuyển hướng đến trang lỗi nếu không phải admin
    exit();
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "12345";
$dbname = "banhangmypham";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch orders and related user information
$sql = "SELECT so.IdOrder, so.DateOrder, so.TotalPrice, so.StatusOrder, so.AddressShipping, 
        u.NameUser, u.PhoneUser, u.AddressUser
        FROM ShoppingOrder so
        JOIN User u ON so.IdUser = u.IdUser";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Orders</title>
    <style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    table,
    th,
    td {
        border: 1px solid black;
    }

    th,
    td {
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }
    </style>
</head>

<body>

    <h2>Orders List</h2>

    <table>
        <tr>
            <th>Order ID</th>
            <th>Order Date</th>
            <th>Total Price</th>
            <th>Status</th>
            <th>Shipping Address</th>
            <th>Customer Name</th>
            <th>Phone</th>
            <th>Address</th>
        </tr>

        <?php
    if ($result->num_rows > 0) {
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["IdOrder"] . "</td>
                    <td>" . $row["DateOrder"] . "</td>
                    <td>" . $row["TotalPrice"] . "</td>
                    <td>" . $row["StatusOrder"] . "</td>
                    <td>" . $row["AddressShipping"] . "</td>
                    <td>" . $row["NameUser"] . "</td>
                    <td>" . $row["PhoneUser"] . "</td>
                    <td>" . $row["AddressUser"] . "</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='8'>No orders found</td></tr>";
    }
    ?>

    </table>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>