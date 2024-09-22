<?php
session_start();

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "12345";
$dbname = "banhangmypham";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập để cập nhật giỏ hàng.");
}

$userId = $_SESSION['user_id'];

// Kiểm tra dữ liệu từ form
if (empty($_POST['cartId']) || !is_array($_POST['productId']) || !is_array($_POST['quantity'])) {
    die("Không có ID giỏ hàng, ID sản phẩm hoặc số lượng được chỉ định.");
}

$cartId = $_POST['cartId'];
$productIds = $_POST['productId'];
$quantities = $_POST['quantity'];

// Cập nhật số lượng sản phẩm trong giỏ hàng
$sql = "UPDATE ShoppingCartItem 
        SET QuantityItem = ? 
        WHERE IdCart = ? AND IdProduct = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Lỗi chuẩn bị câu lệnh SQL: " . $conn->error);
}

foreach ($productIds as $index => $productId) {
    $quantity = intval($quantities[$index]);

    if ($quantity < 1) {
        die("Số lượng không hợp lệ cho sản phẩm với ID: " . htmlspecialchars($productId));
    }

    $stmt->bind_param("iss", $quantity, $cartId, $productId);
    if (!$stmt->execute()) {
        die("Lỗi thực thi câu lệnh SQL: " . $stmt->error);
    }
}

$stmt->close();
$conn->close();

// Chuyển hướng về trang giỏ hàng sau khi cập nhật
header("Location: cart.php");
exit();
?>