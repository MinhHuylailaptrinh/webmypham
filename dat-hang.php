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
    header("Location: error-page2.php");
    exit();
}

// Lấy ID người dùng từ session
$userId = $_SESSION['user_id'];

// Lấy ID sản phẩm và số lượng từ form
if (isset($_POST['productId']) && isset($_POST['quantity'])) {
    $productId = $_POST['productId'];
    $quantity = $_POST['quantity'];

    // Lấy số lượng hiện tại của sản phẩm từ bảng Product
    $sql_check_stock = "SELECT Quantity FROM Product WHERE IdProduct = '$productId'";
    $result_check_stock = $conn->query($sql_check_stock);

    if ($result_check_stock->num_rows > 0) {
        $row = $result_check_stock->fetch_assoc();
        $stockQuantity = $row['Quantity'];

        // Kiểm tra số lượng đặt có vượt quá số lượng còn lại không
        if ($quantity > $stockQuantity) {
            // Nếu số lượng đặt lớn hơn số lượng còn lại, chuyển hướng về trang sản phẩm với thông báo lỗi
            header("Location: product-single.php?productId=$productId&error=quantity");
            exit();
        }

        // Kiểm tra xem người dùng đã có giỏ hàng chưa
        $sql_check_cart = "SELECT IdCart FROM ShoppingCart WHERE IdUser = '$userId' LIMIT 1";
        $result_check_cart = $conn->query($sql_check_cart);

        if ($result_check_cart->num_rows > 0) {
            // Nếu đã có giỏ hàng, lấy ID của giỏ hàng hiện tại
            $row = $result_check_cart->fetch_assoc();
            $cartId = $row['IdCart'];
        } else {
            // Nếu chưa có giỏ hàng, tạo một ID giỏ hàng mới
            $cartId = substr(md5(uniqid(rand(), true)), 0, 10);

            // Thêm giỏ hàng mới vào cơ sở dữ liệu
            $sql_insert_cart = "INSERT INTO ShoppingCart (IdCart, IdUser) VALUES ('$cartId', '$userId')";
            if (!$conn->query($sql_insert_cart)) {
                die("Lỗi tạo giỏ hàng mới: " . $conn->error);
            }
        }

        // Cập nhật session với ID giỏ hàng hiện tại
        $_SESSION['cartId'] = $cartId;

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $sql_check_product = "SELECT * FROM ShoppingCartItem WHERE IdCart = '$cartId' AND IdProduct = '$productId'";
        $result_check_product = $conn->query($sql_check_product);

        if ($result_check_product->num_rows > 0) {
            // Nếu sản phẩm đã có, cập nhật số lượng
            $sql_update = "UPDATE ShoppingCartItem SET QuantityItem = QuantityItem + $quantity WHERE IdCart = '$cartId' AND IdProduct = '$productId'";
            if (!$conn->query($sql_update)) {
                die("Lỗi cập nhật giỏ hàng: " . $conn->error);
            }
        } else {
            // Nếu sản phẩm chưa có, thêm mới
            $sql_insert = "INSERT INTO ShoppingCartItem (IdCart, IdProduct, QuantityItem) VALUES ('$cartId', '$productId', $quantity)";
            if (!$conn->query($sql_insert)) {
                die("Lỗi thêm sản phẩm vào giỏ hàng: " . $conn->error);
            }
        }

        // Xóa ID sản phẩm khỏi session sau khi thêm thành công
        unset($_SESSION['productId']);

        // Chuyển hướng đến trang giỏ hàng hoặc trang khác
        header("Location: cart.php");
        exit();
    } else {
        // Nếu không tìm thấy sản phẩm, chuyển hướng về trang sản phẩm với thông báo lỗi
        header("Location: product-single.php?productId=$productId&error=product");
        exit();
    }
} else {
    echo "Không có ID sản phẩm hoặc số lượng được chỉ định.";
}

$conn->close();
?>