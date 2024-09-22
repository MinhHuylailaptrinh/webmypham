<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập để thực hiện thao tác này.");
}

// Get the cart ID and product ID from the URL
if (isset($_GET['productId']) && isset($_SESSION['cartId'])) {
    $productId = $_GET['productId'];
    $cartId = $_SESSION['cartId'];

    // Connect to the database
    $servername = "localhost";
    $username = "root";
    $password = "12345";
    $dbname = "banhangmypham";
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Set the character set to utf8
    $conn->set_charset("utf8");

    // Prepare and execute the delete query
    $sql = "DELETE FROM ShoppingCartItem WHERE IdCart = ? AND IdProduct = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $cartId, $productId);

    if ($stmt->execute()) {
        // Product successfully removed, redirect back to the cart page
        header("Location: cart.php");
        exit();
    } else {
        echo "Lỗi khi xóa sản phẩm: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Thiếu thông tin sản phẩm hoặc giỏ hàng.";
}
?>