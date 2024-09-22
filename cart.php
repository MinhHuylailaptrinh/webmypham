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
    <title>Cart</title>
    <style>
    .quantity-input {
        width: 80px;
        text-align: center;
    }
    </style>
</head>

<body>
    <!--header-->
    <?php 
     session_start();
     include('header.php'); 
    ?>
    <!--end header-->

    <!--title page-->
    <div class="title-page" style="
        background-image: url('imager/shop/Shop_3Columns-title.jpg');
        background-position: center center;
        background-size: cover;
      ">
        <div class="container">
            <div class="row">
                <div class="col-md-6 inner-title-page">
                    <h1>Shop</h1>
                    <p><span>Home</span>/ Shop / Cart</p>
                </div>
            </div>
        </div>
    </div>
    <!--end title page-->

    <!--content cart-->
    <div class="container">
        <div class="row content-cart">
            <form action="update-cart.php" method="POST">
                <?php
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
                    die("Bạn cần đăng nhập để xem giỏ hàng.");
                }

                // Lấy ID người dùng từ session
                $userId = $_SESSION['user_id'];

                // Kiểm tra xem người dùng có giỏ hàng hay không
                if (isset($_SESSION['cartId'])) {
                    $cartId = $_SESSION['cartId'];

                    // Truy xuất các sản phẩm trong giỏ hàng, bao gồm cả hình ảnh
                    $sql = "SELECT sci.IdProduct, p.Name, p.Price, sci.QuantityItem, p.Images, p.ImagePath 
                            FROM ShoppingCartItem sci 
                            JOIN Product p ON sci.IdProduct = p.IdProduct 
                            WHERE sci.IdCart = '$cartId'";
                    $result = $conn->query($sql);

                    if ($result === false) {
                        echo "Lỗi truy vấn: " . $conn->error;
                    } else {
                        if ($result->num_rows > 0) {
                            echo '<table class="table cart-desktop">
                                    <thead>
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col" style="text-align: left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Product</th>
                                            <th scope="col">Price</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                            $total = 0;
                            while ($row = $result->fetch_assoc()) {
                                $totalPrice = $row['Price'] * $row['QuantityItem'];
                                $total += $totalPrice;

                                if (!empty($row['Images'])) {
                                    $imageData = base64_encode($row['Images']);
                                    $imgSrc = "data:image/jpeg;base64,$imageData";
                                } else {
                                    $imgSrc = $row['ImagePath'];
                                }

                                echo '<tr>
                                        <td><a href="remove-from-cart.php?productId=' . $row['IdProduct'] . '">X</a></td>
                                        <td>
                                            <div class="row">
                                                <img src="' . $imgSrc . '" alt="Product Image" style="width: 100px; height: 100px;"/>
                                                <p>' . $row['Name'] . '</p>
                                            </div>
                                        </td>
                                        <td>$' . $row['Price'] . '</td>
                                        <td>
                                            <input type="hidden" name="cartId" value="' . $cartId . '"/>
                                            <input type="hidden" name="productId[]" value="' . $row['IdProduct'] . '"/>
                                            <input type="number" name="quantity[]" value="' . $row['QuantityItem'] . '" min="1" class="quantity-input"/>
                                        </td>
                                        <td><span>$' . $totalPrice . '</span></td>
                                    </tr>';
                            }

                            echo '<tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><strong>Total:</strong></td>
                                    <td><span>$' . $total . '</span></td>
                                </tr>
                                </tbody>
                            </table>';
                        } else {
                            echo '<p>Giỏ hàng của bạn hiện đang trống.</p>';
                        }
                    }
                } else {
                    echo '<p>Giỏ hàng của bạn hiện đang trống.</p>';
                }

                $conn->close();
                ?>

                <div class="card-button">
                    <button type="button" class="btn coupon float-left">
                        Coupon code
                    </button>
                    <button type="button" class="btn apply float-left">
                        APPLY COUPON
                    </button>
                    <button type="submit" class="btn update float-right">
                        UPDATE CART
                    </button>
                </div>
            </form>
            <div class="rocart-total">


                <a href="checkout.php" class="btn float-left">PROCEED TO CHECKOUT</a>
            </div>
        </div>
    </div>
    <!--end content cart-->

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