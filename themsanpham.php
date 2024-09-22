<?php
session_start(); // Start the session

// Kết nối tới cơ sở dữ liệu
$servername = "localhost";
$username = "root"; 
$password = "12345"; 
$dbname = "banhangmypham";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra quyền admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: error-page.php");
    exit();
}

// Lấy danh sách các danh mục từ cơ sở dữ liệu
$sql = "SELECT IdCategory, NameCategory FROM ProductCategory";
$result = $conn->query($sql);

// Kiểm tra nếu form đã được gửi đi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form và kiểm tra tính hợp lệ
    $IdProduct = trim($_POST['IdProduct']);
    $Description = trim($_POST['Description']);
    $Name = trim($_POST['Name']);
    $Price = (int)$_POST['Price'];
    $Quantity = (int)$_POST['Quantity'];
    $Rating = (float)$_POST['Rating'];
    $DateAdded = date('Y-m-d');  // Ngày thêm sản phẩm tự động lấy
    $IdCategory = $_POST['IdCategory'];

    // Xử lý tải lên ảnh
    if (isset($_FILES['Images']) && $_FILES['Images']['error'] == 0) {
        // Kiểm tra loại file và giới hạn kích thước file
        $allowedTypes = ['image/jpeg'];
        $fileType = $_FILES['Images']['type'];
        $fileSize = $_FILES['Images']['size'];
        $maxFileSize = 2 * 1024 * 1024; // Giới hạn kích thước 2MB

        if (in_array($fileType, $allowedTypes) && $fileSize <= $maxFileSize) {
            // Đảm bảo thư mục lưu ảnh tồn tại
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Tạo tên file duy nhất
            $imagePath = $uploadDir . uniqid() . '.jpg';
            if (move_uploaded_file($_FILES['Images']['tmp_name'], $imagePath)) {
                // Đọc nội dung file ảnh
                $imageData = file_get_contents($imagePath);
            } else {
                die("Lỗi khi lưu file.");
            }
        } else {
            die("Chỉ hỗ trợ tệp JPG và kích thước tối đa là 2MB.");
        }
    } else {
        die("Có lỗi xảy ra khi tải lên tệp.");
    }

    // Chuẩn bị câu truy vấn SQL
    $sql = "INSERT INTO Product (IdProduct, Description, Images, Name, Price, Quantity, Rating, DateAdded, IdCategory)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiiiss", $IdProduct, $Description, $imageData, $Name, $Price, $Quantity, $Rating, $DateAdded, $IdCategory);

    // Thực thi câu truy vấn
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Thêm sản phẩm thành công!</p>";
    } else {
        echo "<p style='color:red;'>Lỗi: " . $stmt->error . "</p>";
    }

    // Đóng kết nối
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Thêm sản phẩm</title>
</head>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
}

h2 {
    text-align: center;
    color: #333;
    margin-top: 20px;
    font-size: 28px;
}


form {
    max-width: 600px;
    margin: 0 auto;
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

form input[type="text"],
form input[type="number"],
form input[type="file"],
form textarea,
form select {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

/* Submit Button Styling */
form input[type="submit"] {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 12px;
    width: 100%;
    font-size: 18px;
    border-radius: 4px;
    cursor: pointer;
}

form input[type="submit"]:hover {
    background-color: #218838;
}

/* Form Labels */
form p {
    font-weight: bold;
    margin-bottom: 5px;
}


select {
    padding: 10px;
}


@media (max-width: 768px) {
    form {
        padding: 20px;
    }

    h2 {
        font-size: 24px;
    }
}
</style>

<body>

    <h2>Thêm sản phẩm mới</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        Mã sản phẩm: <input type="text" name="IdProduct" required><br><br>
        Tên sản phẩm: <input type="text" name="Name" required><br><br>
        Mô tả: <textarea name="Description" required></textarea><br><br>
        Giá: <input type="number" name="Price" required><br><br>
        Số lượng: <input type="number" name="Quantity" required><br><br>
        Đánh giá: <input type="number" name="Rating" step="0.01" min="0" max="5" required><br><br>
        Ảnh sản phẩm: <input type="file" name="Images" accept="image/jpeg" required><br><br>
        Mã danh mục:
        <select name="IdCategory" required>
            <option value="">Chọn danh mục</option>
            <?php
                if ($result->num_rows > 0) {
                    // Xuất dữ liệu của mỗi hàng
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='".$row['IdCategory']."'>".$row['NameCategory']."</option>";
                    }
                }
            ?>
        </select><br><br>
        <input type="submit" value="Thêm sản phẩm">
    </form>

</body>

</html>