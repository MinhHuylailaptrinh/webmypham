<?php
session_start(); // Start session at the top of the file

// Database connection
$servername = "localhost"; 
$username = "root";        
$password = "12345";           
$dbname = "banhangmypham"; 
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Handle product search
if (isset($_POST['search'])) {
    $searchTerm = $_POST['search'];
    
    // Prepare SQL query for product search
    $sql = "SELECT * FROM Product WHERE Name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = '%' . $searchTerm . '%';
    $stmt->bind_param('s', $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Default product listing query
    $sql = "SELECT * FROM Product";
    
    // Handle sorting if selected
    if (isset($_POST['sort'])) {
        $sort = $_POST['sort'];
        
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY Price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY Price DESC";
                break;
            case 'rating':
                $sql .= " ORDER BY Rating DESC";
                break;
            case 'latest':
                $sql .= " ORDER BY DateAdded DESC";
                break;
            default:
                $sql .= " ORDER BY Name ASC";
                break;
        }
    }

    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../cdn.linearicons.com/free/1.0.0/icon-font.min.css">
    <link rel="stylesheet" href="asset/css/owl.carousel.css">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="style68b3.css?ver=1">
    <link rel="stylesheet" type="text/css" href="asset/css/flexslider.css">
    <link rel="stylesheet" type="text/css" href="asset/css/bootstrap.min.css">
    <link href="asset/css/all.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=El+Messiri:400,500,600,700|Raleway:100,100i,200,200i,300,300i,400,400i,500,500i,600,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link rel="icon" href="favicon.png" type="image/png">
    <title>Product list</title>

</head>

<body>
    <style>
    body {
        color: black;

    }

    .product-item h3,
    .product-item p {
        color: black;
    }
    </style>
    <!--header-->
    <?php include('header.php'); ?>
    <!--end header-->

    <!--title detail-->
    <div class="title-page"
        style="background-image: url('imager/shop/Shop_3Columns-title.jpg');background-position: center center;background-size: cover;">
        <div class="container">
            <div class="row">
                <div class="col-md-6 inner-title-page">
                    <h1>Shop</h1>
                    <p><span>Home</span> / Shop / Product List</p>
                </div>
            </div>
        </div>
    </div>
    <!--end title detail-->

    <!--product list-->
    <div class="container">
        <div class="prodcut-list">
            <div class="row">
                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12 col-12">
                    <!-- Search Form -->
                    <form method="POST" id="searchForm">
                        <input type="text" name="search" id="search" placeholder="Tìm kiếm sản phẩm..."
                            value="<?php echo isset($_POST['search']) ? $_POST['search'] : ''; ?>" />
                        <button type="submit">Tìm kiếm</button>
                    </form>

                    <div class="row header-show-list">
                        <div class="col-md-6 col-sm-12 col-12"></div>
                        <div class="col-md-6 col-sm-12 col-12">
                            <!-- Sorting Form -->
                            <form method="POST" id="productFilterForm" class="float-right">
                                <select name="sort" id="sort"
                                    onchange="document.getElementById('productFilterForm').submit();">
                                    <option value="default"
                                        <?php if (isset($_POST['sort']) && $_POST['sort'] == 'default') echo 'selected'; ?>>
                                        Sắp xếp mặc định</option>
                                    <option value="price_asc"
                                        <?php if (isset($_POST['sort']) && $_POST['sort'] == 'price_asc') echo 'selected'; ?>>
                                        Giá: thấp đến cao</option>
                                    <option value="price_desc"
                                        <?php if (isset($_POST['sort']) && $_POST['sort'] == 'price_desc') echo 'selected'; ?>>
                                        Giá: cao đến thấp</option>
                                    <option value="rating"
                                        <?php if (isset($_POST['sort']) && $_POST['sort'] == 'rating') echo 'selected'; ?>>
                                        Đánh giá</option>
                                    <option value="latest"
                                        <?php if (isset($_POST['sort']) && $_POST['sort'] == 'latest') echo 'selected'; ?>>
                                        Sản phẩm mới nhất</option>
                                </select>
                            </form>
                        </div>

                        <!-- Product Display -->
                        <div class="container">
                            <div class="product-list">
                                <div class="row justify-content-center">
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            echo '<div class="col-xl-4 col-lg-4 col-md-6 col-sm-12 col-12">';
                                            echo '    <div class="product-item text-center">';
                                            echo '    <a href="product-single.php?id=' . $row['IdProduct'] . '">';
                                            
                                            // Display product image
                                            if (!empty($row['Images'])) {
                                                echo '        <img src="data:image/jpeg;base64,' . base64_encode($row['Images']) . '" alt="' . $row['Name'] . '" class="img-fluid">';
                                            } elseif (!empty($row['ImagePath'])) {
                                                echo '        <img src="' . $row['ImagePath'] . '" alt="' . $row['Name'] . '" class="img-fluid">';
                                            } else {
                                                echo '        <img src="default-image.jpg" alt="Default Image" class="img-fluid">';
                                            }
                                            
                                            // Display product name and price
                                            echo '        <h3>' . $row['Name'] . '</h3>';
                                            echo '        <p><strong>Price: </strong>$' . $row['Price'] . '</p>';
                                            echo '    </a>';
                                            echo '    </div>';
                                            echo '</div>';
                                        }
                                    } else {
                                        echo "<p>No products found</p>";
                                    }

                                    $conn->close();
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
                    <div class="content-blog-left">
                        <div class="category-blog">

                            <?php
// Only display "Add Product", "Delete Product", and "Order Status" buttons if user is admin
if (isset($_SESSION['user_name']) && $_SESSION['user_name'] === 'admin@gmail.com') {
    echo '<a href="./themsanpham.php"><button class="btn btn-primary">Add Product</button></a>';
    echo '<a href="./deleteproduct.php"><button class="btn btn-danger">Delete Product</button></a>';
    echo '<a href="./orders.php"><button class="btn btn-info">Order Status</button></a>';
}
?>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end product list-->

    <!--footer-->
    <?php include('footer.php'); ?>
    <!--end footer-->

    <script data-cfasync="false" src="../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script src="asset/js/jquery-3.3.1.min.js"></script>
    <script src="asset/js/owl.carousel.js"></script>
    <script src="asset/js/main.js"></script>
    <script src="asset/js/bootstrap.min.js"></script>
    <script src="asset/js/jquery.flexslider-min.js"></script>
    <script src="asset/js/jquery.nstSlider.min.js"></script>
</body>

</html>