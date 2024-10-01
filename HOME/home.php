<?php
session_start();
require_once 'db.php';  // เรียกการเชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ายังล็อกอินอยู่หรือไม่
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/P2/HOME/styles.css"> <!-- ใส่ไฟล์ CSS ตามต้องการ -->
    <title>จัดการข่าวสาร</title>
    <style>
        /* สไตล์เพิ่มเติมเพื่อปรับให้เหมือนภาพ */
        form {
            width: 80%;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .submit-button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .submit-button:hover {
            background-color: #45a049;
        }
        .user-management {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>

    <h1>เพิ่มข่าวสารใหม่</h1>

    <!-- ฟอร์มสำหรับเพิ่มข่าว -->
    <form action="save.php" method="post" enctype="multipart/form-data">
    
        <!-- ประเภทข่าว -->
        <div class="form-group">
            <label for="type">ประเภทข่าว *</label>
            <select name="type" id="type" required>
                <option value="1">งานวิชาการ</option>
                <option value="2">อบรม/สมนา</option>
                <option value="3">กิจกรรม</option>
            </select>
        </div>

        <!-- หัวข้อข่าว -->
        <div class="form-group">
            <label for="title">หัวข้อข่าว *</label>
            <input type="text" name="title" id="title" required>
        </div>

        <!-- เนื้อข่าว -->
        <div class="form-group">
            <label for="content">เนื้อข่าว *</label>
            <textarea name="content" id="content" rows="10" required></textarea>
        </div>

        <!-- แนบไฟล์รูปภาพ -->
        <div class="form-group">
            <label for="images">แนบไฟล์ภาพ</label>
            <input type="file" name="images[]" id="images" multiple>
        </div>

        <!-- แนบไฟล์ PDF -->
        <div class="form-group">
            <label for="pdf_file">แนบไฟล์ PDF</label>
            <input type="file" name="pdf_file" id="pdf_file" accept=".pdf">
        </div>

        <!-- รายละเอียดเพิ่มเติม -->
        <div class="form-group">
            <label for="additional_details">รายละเอียดเพิ่มเติม</label>
            <input type="text" name="additional_details" id="additional_details" placeholder="เช่น https://www.example.com">
        </div>

        <!-- วันที่เสนอข่าว -->
        <div class="form-group">
            <label for="start_date">เริ่มเสนอข่าวตั้งแต่ *</label>
            <input type="date" name="start_date" id="start_date" required>
        </div>

        <!-- ปุ่มส่งฟอร์ม -->
        <button type="submit" class="submit-button">บันทึก</button>

    </form> <!-- ปิดแท็กฟอร์ม -->

</body>
</html>
