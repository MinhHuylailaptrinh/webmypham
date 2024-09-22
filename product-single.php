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
    <title>Detail</title>
</head>

<body>
    <?php
    session_start();
    include('header.php');// Kết nối cơ sở dữ liệu
    $servername = "localhost"; 
    $username = "root";        
    $password = "12345";           
    $dbname = "banhangmypham"; 
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");

    // Lấy ID sản phẩm từ URL
    if (isset($_GET['id'])) {
        $productId = $_GET['id'];

        // Truy vấn sản phẩm dựa trên ID
        $sql = "SELECT * FROM Product WHERE IdProduct = '$productId'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            ?>
    <div class="container">
        <div class="product-single-detail">
            <div class="row product_detail">
                <div class="col-md-6 col-sm-12 col-12">
                    <div id="slider" class="flexslider">
                        <ul class="slides">
                            <li>
                                <?php
                                        // Hiển thị hình ảnh của sản phẩm
                                        if (!empty($row['Images'])) {
                                            echo '<img src="data:image/jpeg;base64,' . base64_encode($row['Images']) . '" alt="' . $row['Name'] . '" class="img-fluid">';
                                        } elseif (!empty($row['ImagePath'])) {
                                            echo '<img src="' . $row['ImagePath'] . '" alt="' . $row['Name'] . '" class="img-fluid">';
                                        } else {
                                            echo '<img src="default-image.jpg" alt="Default Image" class="img-fluid">';
                                        }
                                        ?>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="col-md-6 col-sm-12 col-12 content-product">
                    <!-- Hiển thị thông tin chi tiết sản phẩm -->
                    <h2><?php echo $row['Name']; ?> | $<?php echo $row['Price']; ?></h2>
                    <p>
                        <!-- Hiển thị đánh giá bằng sao -->
                        <?php for ($i = 0; $i < 5; $i++) {
                                    if ($i < floor($row['Rating'])) {
                                        echo '<i class="fas fa-star"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                } ?>
                        &nbsp;
                    </p>
                    <p><?php echo $row['Description']; ?></p>

                    <!-- Hiển thị số lượng còn lại của sản phẩm -->
                    <p><strong>Số lượng còn:</strong> <?php echo $row['Quantity']; ?></p>

                    <div class="infor-product">
                        <p><span>Category: </span>Cosmetic</p>

                        <p>
                            <span>Share: </span>
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                        </p>
                    </div>

                    <!-- Form thêm vào giỏ hàng -->
                    <form action="dat-hang.php" method="post">
                        <input type="hidden" name="productId" value="<?php echo $row['IdProduct']; ?>">
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" class="form-control"
                                style="width: 100px;">
                        </div>
                        <div class="btn-group">
                            <button type="submit" class="btn add-to-cart">ADD TO CART
                                <p><i class="fas fa-cart-plus"></i></p>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
        } else {
            echo "<p>Product not found</p>";
        }
    } else {
        echo "<p>No product selected</p>";
    }

    $conn->close();
    ?>
    <!--end product detail-->

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