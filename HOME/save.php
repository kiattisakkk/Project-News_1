<?php
require_once 'db.php';  // เรียกการเชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าฟอร์มถูกส่งมาหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = []; // อาร์เรย์สำหรับเก็บข้อความข้อผิดพลาด

    $type = htmlspecialchars($_POST['type']);
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $start_date = htmlspecialchars($_POST['start_date']);

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($title)) {
        $errors[] = "กรุณากรอกหัวข้อข่าว";
    }
    if (empty($content)) {
        $errors[] = "กรุณากรอกเนื้อข่าว";
    }
    if (empty($start_date)) {
        $errors[] = "กรุณาเลือกวันที่เริ่มเสนอข่าว";
    }

    // ตรวจสอบว่าโฟลเดอร์อัปโหลดไฟล์มีอยู่หรือไม่ ถ้าไม่มีก็สร้างใหม่
    $upload_dir_images = "uploads/images/";
    $upload_dir_pdf = "uploads/pdf/";

    if (!file_exists($upload_dir_images)) {
        mkdir($upload_dir_images, 0777, true);  // สร้างโฟลเดอร์สำหรับอัปโหลดภาพ
    }
    if (!file_exists($upload_dir_pdf)) {
        mkdir($upload_dir_pdf, 0777, true);  // สร้างโฟลเดอร์สำหรับอัปโหลด PDF
    }

    // จัดการการอัปโหลดภาพ (ถ้ามี)
    $images = [];
    if (isset($_FILES['images'])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = uniqid() . '_' . $_FILES['images']['name'][$key];  // ป้องกันไฟล์ชื่อซ้ำ
            $file_tmp = $_FILES['images']['tmp_name'][$key];
            $file_size = $_FILES['images']['size'][$key];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            // ตรวจสอบขนาดไฟล์และประเภทไฟล์
            if ($file_size > 2097152) {  // ขนาดไม่เกิน 2 MB
                $errors[] = "ไฟล์ภาพ {$file_name} มีขนาดใหญ่เกิน 2 MB";
            } elseif (!in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
                $errors[] = "ไฟล์ภาพ {$file_name} ไม่ใช่ไฟล์ประเภทที่รองรับ (jpg, jpeg, png)";
            } else {
                if (move_uploaded_file($file_tmp, $upload_dir_images . $file_name)) {
                    $images[] = $file_name;
                } else {
                    $errors[] = "ไม่สามารถอัปโหลดไฟล์ภาพ {$file_name} ได้";
                }
            }
        }
    }

    // จัดการการอัปโหลด PDF (ถ้ามี)
    $pdf_file = null;
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['size'] > 0) {
        $pdf_name = uniqid() . '_' . $_FILES['pdf_file']['name'];  // ป้องกันไฟล์ชื่อซ้ำ
        $pdf_tmp = $_FILES['pdf_file']['tmp_name'];
        $pdf_size = $_FILES['pdf_file']['size'];
        $pdf_ext = pathinfo($pdf_name, PATHINFO_EXTENSION);

        // ตรวจสอบขนาดไฟล์และประเภทไฟล์
        if ($pdf_size > 5242880) {  // ขนาดไม่เกิน 5 MB
            $errors[] = "ไฟล์ PDF มีขนาดใหญ่เกิน 5 MB";
        } elseif ($pdf_ext !== 'pdf') {
            $errors[] = "ไฟล์ PDF ต้องเป็นไฟล์นามสกุล .pdf เท่านั้น";
        } else {
            if (move_uploaded_file($pdf_tmp, $upload_dir_pdf . $pdf_name)) {
                $pdf_file = $pdf_name;
            } else {
                $errors[] = "ไม่สามารถอัปโหลดไฟล์ PDF ได้";
            }
        }
    }

    // ตรวจสอบว่ามีข้อผิดพลาดหรือไม่
    if (empty($errors)) {
        // แทรกข้อมูลข่าวสารลงในฐานข้อมูล
        $sql = "INSERT INTO articles (title, description, category_id, created_at) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $title, $content, $type, $start_date);
        
        if ($stmt->execute()) {
            // บันทึกสำเร็จ
            echo "บันทึกข่าวสำเร็จ!";
        } else {
            echo "เกิดข้อผิดพลาด: " . $conn->error;
        }
    } else {
        // แสดงข้อผิดพลาด
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
} else {
    echo "ฟอร์มไม่ได้ถูกส่ง";
}
?>
