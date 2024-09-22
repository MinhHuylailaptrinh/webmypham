<?php
session_start(); // Start the session

// Kết nối tới cơ sở dữ liệu
$servername = "localhost";
$username = "root"; // Thay đổi nếu cần
$password = "12345"; // Thay đổi nếu cần
$dbname = "banhangmypham";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra quyền admin
if (!isset($_SESSION['user_name']) || $_SESSION['user_name'] !== 'admin@gmail.com') {
    header("Location: error-page.php"); // Chuyển hướng đến trang lỗi nếu không phải admin
    exit();
}

// Lấy danh sách các sản phẩm từ cơ sở dữ liệu
$sql = "SELECT IdProduct, Name FROM Product";
$result = $conn->query($sql);

// Kiểm tra nếu form đã được gửi đi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['IdProduct'])) {
    // Lấy ID sản phẩm từ form
    $IdProduct = $_POST['IdProduct'];

    // Câu truy vấn để xóa sản phẩm
    $sql = "DELETE FROM Product WHERE IdProduct = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $IdProduct);

    // Thực thi truy vấn và kiểm tra kết quả
    if ($stmt->execute()) {
        echo "<p style='color:green;'>Xóa sản phẩm thành công!</p>";
    } else {
        echo "<p style='color:red;'>Lỗi khi xóa sản phẩm: " . $stmt->error . "</p>";
    }

    // Đóng kết nối
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Xóa sản phẩm</title>
</head>
<style>
/* Basic Body Styling */
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

/* Form Styling */
form {
    max-width: 600px;
    margin: 0 auto;
    background-color: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

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
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 12px;
    width: 100%;
    font-size: 18px;
    border-radius: 4px;
    cursor: pointer;
}

form input[type="submit"]:hover {
    background-color: #c82333;
}

/* Form Labels */
form p {
    font-weight: bold;
    margin-bottom: 5px;
}

/* Select Option Styling */
select {
    padding: 10px;
}

/* Responsive Styling */
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

    <h2>Xóa sản phẩm</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="IdProduct">Chọn sản phẩm để xóa:</label>
        <select name="IdProduct" required>
            <option value="">Chọn sản phẩm</option>
            <?php
                if ($result->num_rows > 0) {
                    // Xuất dữ liệu của mỗi sản phẩm
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['IdProduct'] . "'>" . $row['Name'] . "</option>";
                    }
                }
            ?>
        </select><br><br>
        <input type="submit" value="Xóa sản phẩm">
    </form>

</body>

</html>