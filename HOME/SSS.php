<?php
// save.php
require_once 'db.php';  // เรียกใช้การเชื่อมต่อฐานข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "ฟอร์มถูกส่งสำเร็จ!";
    print_r($_POST);
    print_r($_FILES);
} else {
    echo "ฟอร์มไม่ได้ถูกส่ง";
}

// ตรวจสอบว่าฟอร์มถูกส่งมาแล้วหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $start_date = $_POST['start_date'];
    $additional_details = $_POST['additional_details'] ?? null;

    // จัดการการอัปโหลดภาพ (ถ้ามี)
    $images = [];
    if (isset($_FILES['images'])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['images']['name'][$key];
            $file_tmp = $_FILES['images']['tmp_name'][$key];
            $file_size = $_FILES['images']['size'][$key];

            // ตรวจสอบขนาดไฟล์และประเภทไฟล์
            if ($file_size <= 2097152 && in_array(pathinfo($file_name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
                $upload_dir = "uploads/images/";
                move_uploaded_file($file_tmp, $upload_dir . $file_name);
                $images[] = $file_name;
            }
        }
    }

    // จัดการการอัปโหลด PDF (ถ้ามี)
    $pdf_file = null;
    if (isset($_FILES['pdf_file'])) {
        $pdf_name = $_FILES['pdf_file']['name'];
        $pdf_tmp = $_FILES['pdf_file']['tmp_name'];
        $pdf_size = $_FILES['pdf_file']['size'];

        // ตรวจสอบขนาดไฟล์และประเภทไฟล์
        if ($pdf_size <= 5242880 && pathinfo($pdf_name, PATHINFO_EXTENSION) == 'pdf') {
            $upload_dir = "uploads/pdf/";
            move_uploaded_file($pdf_tmp, $upload_dir . $pdf_name);
            $pdf_file = $pdf_name;
        }
    }

    // แทรกข้อมูลข่าวสารลงในฐานข้อมูล
    $sql = "INSERT INTO articles (title, description, category_id, created_at) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $title, $content, $type, $start_date);
    if ($stmt->execute()) {
        // บันทึกสำเร็จ
        header('Location: success.php');  // ไปที่หน้าประสบความสำเร็จ
        exit();
    } else {
        // มีข้อผิดพลาด
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>
