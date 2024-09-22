<?php
session_start();

// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "12345"; // Thay đổi mật khẩu nếu cần
$dbname = "banhangmypham";
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8"); // Thiết lập bộ mã ký tự

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập để thực hiện thanh toán.");
}

// Kiểm tra xem người dùng có giỏ hàng không
if (!isset($_SESSION['cartId'])) {
    die("Bạn không có giỏ hàng để thanh toán.");
}

$userId = $_SESSION['user_id']; // ID người dùng đã đăng nhập
$cartId = $_SESSION['cartId']; // ID giỏ hàng của người dùng

// Hiển thị thông tin giỏ hàng từ bảng ShoppingCartItem và Product
$sql = "SELECT p.IdProduct, p.Name, p.Price, p.ImagePath, p.Images, sci.QuantityItem
        FROM ShoppingCartItem sci
        JOIN Product p ON sci.IdProduct = p.IdProduct
        WHERE sci.IdCart = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cartId);
$stmt->execute();
$result = $stmt->get_result();
$cartItems = [];
$totalPrice = 0;

while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
    $totalPrice += $row['Price'] * $row['QuantityItem']; // Tính tổng giá
}

$stmt->close();

// Lưu thông tin đặt hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kiểm tra xem các trường form có được gửi hay không
    if (isset($_POST['firstName'], $_POST['lastName'], $_POST['address'])) {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $addressShipping = $_POST['address'];

        // Tạo mã đơn hàng mới, đảm bảo duy nhất
        $orderId = uniqid('OD', true); // Mã đơn hàng với tiền tố 'OD'

        // Chèn đơn hàng vào bảng ShoppingOrder
        $sqlOrder = "INSERT INTO ShoppingOrder (IdOrder, IdUser, IdCart, DateOrder, TotalPrice, StatusOrder, AddressShipping) 
                     VALUES (?, ?, ?, NOW(), ?, 'Success', ?)";
        $stmtOrder = $conn->prepare($sqlOrder);
        $stmtOrder->bind_param("sssss", $orderId, $userId, $cartId, $totalPrice, $addressShipping);

        // Thực thi câu lệnh và kiểm tra kết quả
        if ($stmtOrder->execute()) {
            // Thông báo thành công và chuyển hướng
            unset($_SESSION['cartId']);
            $redirect = "index.php";
            echo "<script>
                    alert('Thanh Toán thành công!');
                    setTimeout(function() {
                        window.location.href = '$redirect';
                    }, 5000);
                  </script>";
        } else {
            echo "Lỗi khi Thanh Toán: " . $stmtOrder->error;
        }

        $stmtOrder->close();
    } else {
        echo "Vui lòng điền đầy đủ thông tin.";
    }
    // Xóa giỏ hàng của người dùng
    $sql = "DELETE FROM ShoppingCartItem WHERE IdCart = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cartId);
    $stmt->execute();

    // Xóa giỏ hàng
    $sql = "DELETE FROM ShoppingCart WHERE IdCart = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cartId);
    $stmt->execute();

    // Xóa thông tin giỏ hàng khỏi session
    unset($_SESSION['cartId']);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Check Out</title>
    <link rel="icon" href="favicon.png" type="image/png" />
    <link rel="stylesheet" href="../../cdn.linearicons.com/free/1.0.0/icon-font.min.css" />
    <link rel="stylesheet" href="asset/css/owl.carousel.css" />
    <link rel="stylesheet" type="text/css" href="style68b3.css?ver=1" />
    <link rel="stylesheet" type="text/css" href="asset/css/flexslider.css" />
    <link rel="stylesheet" type="text/css" href="asset/css/bootstrap.min.css" />
    <link href="asset/css/all.css" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css?family=El+Messiri:400,500,600,700|Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,700,700i,800,800i,900,900i"
        rel="stylesheet" />
    <style>
    .title-page {
        background-image: url('imager/shop/Shop_3Columns-title.jpg');
        background-position: center;
        background-size: cover;
        padding: 20px 0;
        color: #fff;
    }

    .title-page h1 {
        font-size: 36px;
        margin: 0;
    }

    .title-page p {
        font-size: 14px;
        margin: 5px 0 0;
    }

    .content-checkout {
        padding: 20px 0;
    }

    .billing-detail,
    .order-review {
        padding: 15px;
        background: #f9f9f9;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .woocommerce-form input,
    .woocommerce-form select,
    .woocommerce-form textarea {
        width: 100%;
        padding: 10px;
        margin: 5px 0;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .woocommerce-form textarea {
        resize: vertical;
    }

    .woocommerce-form h3 {
        margin-top: 20px;
        font-size: 18px;
    }

    .order-review table {
        width: 100%;
        border-collapse: collapse;
    }

    .order-review th,
    .order-review td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    .order-review th {
        text-align: left;
        background: #f4f4f4;
    }

    .order-review td {
        text-align: right;
    }

    .payment-method input[type="radio"] {
        margin-right: 5px;
    }

    .payment-method ul {
        list-style: none;
        padding: 0;
    }

    .payment-method li {
        margin-bottom: 10px;
    }

    .btn-primary {
        background-color: #007bff;
        border: none;
        color: #fff;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }
    </style>
</head>

<body>
    <!--header-->
    <?php include('header.php'); ?>
    <!--end header-->

    <!--title page-->
    <div class="title-page">
        <div class="container">
            <div class="row">
                <div class="col-md-6 inner-title-page">
                    <h1>Shop</h1>
                    <p><span>Home</span> / Shop / Check Out</p>
                </div>
            </div>
        </div>
    </div>
    <!--end title page-->

    <!--content check out-->
    <div class="container">
        <div class="row content-checkout">
            <div class="col-md-6 billing-detail">
                <h2>Billing Details</h2>
                <form class="woocommerce-form woocommerce-form-login" method="POST" action="checkout.php">
                    <div class="row">
                        <div class="col-md-6">
                            <p>First Name<span>*</span></p>
                            <input type="text" name="firstName" required />
                        </div>
                        <div class="col-md-6">
                            <p>Last Name<span>*</span></p>
                            <input type="text" name="lastName" required />
                        </div>
                    </div>
                    <p>Company Name</p>
                    <input type="text" name="companyName" />
                    <p>Country<span>*</span></p>
                    <select name="country" required>
                        <option value="VietNam">VietNam</option>
                        <option value="American">American</option>
                    </select>
                    <p>Address<span>*</span></p>
                    <input type="text" name="address" placeholder="Street Address" required />
                    <input type="text" name="address2" placeholder="Apartment, suite, unit etc. (optional)" />
                    <p>Town/City<span>*</span></p>
                    <input type="text" name="townCity" required />
                    <p>County<span>*</span></p>
                    <input type="text" name="county" required />
                    <p>Postcode/Zip<span>*</span></p>
                    <input type="text" name="postcodeZip" required />
                    <div class="row">
                        <div class="col-md-6">
                            <p>Phone<span>*</span></p>
                            <input type="text" name="phone" required />
                        </div>
                        <div class="col-md-6">
                            <p>Email Address<span>*</span></p>
                            <input type="email" name="email" required />
                        </div>
                    </div>
                    <p>
                        <input type="checkbox" class="check-creat-account" name="createAccount" />
                        &nbsp;&nbsp;&nbsp;Create an account
                    </p>
                    <h3>Additional Information</h3>
                    <p>Order Notes</p>
                    <textarea name="orderNotes"
                        placeholder="Note about your order, e.g., special notes for delivery."></textarea>
            </div>
            <div class="col-md-6 order-review">
                <h2>Your Order</h2>
                <table class="shop_table">
                    <thead>
                        <tr>
                            <th class="product-name">Product</th>
                            <th class="product-total">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                        <tr class="cart_item">
                            <td class="product-name">
                                <?php echo htmlspecialchars($item['Name']); ?> x
                                <?php echo htmlspecialchars($item['QuantityItem']); ?>
                                <br>
                                <?php if (!empty($item['Images'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($item['Images']); ?>"
                                    alt="<?php echo htmlspecialchars($item['Name']); ?>" class="img-fluid"
                                    style="width: 100px; height: auto;">
                                <?php elseif (!empty($item['ImagePath'])): ?>
                                <img src="<?php echo htmlspecialchars($item['ImagePath']); ?>"
                                    alt="<?php echo htmlspecialchars($item['Name']); ?>" class="img-fluid"
                                    style="width: 100px; height: auto;">
                                <?php else: ?>
                                <img src="default-image.jpg" alt="Default Image" class="img-fluid"
                                    style="width: 100px; height: auto;">
                                <?php endif; ?>
                            </td>
                            <td class="product-total">
                                <?php echo number_format($item['Price'] * $item['QuantityItem'], 0, ',', '.'); ?> $
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="order-total">
                            <th>Total</th>
                            <td>
                                <strong><span class="amount"><?php echo number_format($totalPrice, 0, ',', '.'); ?>
                                        $</span></strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                <div class="payment-method">
                    <h3>Payment Method</h3>
                    <ul>
                        <li>
                            <input type="radio" name="paymentMethod" value="bankTransfer" id="bankTransfer" required />
                            <label for="bankTransfer">Bank Transfer</label>
                        </li>
                        <li>
                            <input type="radio" name="paymentMethod" value="cash" id="cash" required />
                            <label for="cash">Cash</label>
                        </li>
                        <li>
                            <input type="radio" name="paymentMethod" value="eWallet" id="eWallet" required />
                            <label for="eWallet">E-Wallet</label>
                        </li>
                    </ul>
                    <p>Shipping Address</p>
                    <textarea name="addressShipping" placeholder="Address to ship the order."></textarea>
                    <input type="submit" class="btn btn-primary" value="Place Order" />
                </div>
            </div>
        </div>
        </form>
    </div>
    <!--end content check out-->

    <!--footer-->
    <?php include('footer.php'); ?>
    <!--end footer-->

    <script src="asset/js/jquery.min.js"></script>
    <script src="asset/js/bootstrap.min.js"></script>
    <script src="asset/js/owl.carousel.min.js"></script>
    <script src="asset/js/jquery.flexslider-min.js"></script>
    <script src="asset/js/custom.js"></script>
</body>

</html>