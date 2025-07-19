<?php
// ضفنا الـ "هيدرز" تبعت الـ CORS عشان الـ Flutter app يقدر يوصل
header("Access-Control-Allow-Origin: *"); // خلي الـ "ريكويستات" من أي مكان (عشان الـ "ديفلوبمنت")
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// تعامل مع الـ "ريكويست" اللي من نوع OPTIONS (الـ "بريفلايت ريكويست")
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ضيف الـ "كونتنت تايب" للـ "ريسبونس"
header('Content-Type: application/json'); // ✅ رح نرجع JSON

$response = []; // بلّش الـ "اري" تبعت الـ "ريسبونس"

$uploadDir = 'uploads/'; // المجلد الأساسي لـ "الأبلودات" (نسبة لمكان الـ "سكريبت")
$baseUrl = 'https://php-upload-server-1.onrender.com/'; // ✅ مهم جداً: الـ URL تبع الـ "سيرفس" تبعتك على Render.com

// جيب اسم الـ "فولدر" من الـ POST "ديتا"، أو خليه 'misc' إذا ما انبعت
$folderName = isset($_POST['folder']) && !empty($_POST['folder']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['folder']) : 'misc';
$targetDir = $uploadDir . $folderName . '/';

// اعمل الـ "فولدر" المستهدف إذا مش موجود
if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0777, true)) { // 0777 بتعطي صلاحيات كاملة، ممكن تحتاج تعديل للـ "برودكشن"
        $response['status'] = 'error';
        $response['message'] = 'Failed to create upload directory.';
        echo json_encode($response);
        exit();
    }
}

// اتأكد إذا فيه ملف انعمله "أبلود"
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = $_FILES['image']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // اعمل اسم ملف فريد
    $newFileName = uniqid() . '_' . time() . '.' . $fileExtension;
    $destPath = $targetDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        // كوّن الـ URL الكامل
        $fullUrl = $baseUrl . $destPath; // هاد بيفترض إن الـ 'public' متاح مباشرة عن طريق الـ "ويب سيرفر"
        
        $response['status'] = 'success';
        $response['message'] = 'File uploaded successfully.';
        $response['url'] = $fullUrl;
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to move uploaded file.';
        $response['upload_error_code'] = $_FILES['image']['error']; // "إيرور" مفصل أكثر
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'No file uploaded or an upload error occurred.';
    if (isset($_FILES['image'])) {
        $response['upload_error_code'] = $_FILES['image']['error'];
    }
}

echo json_encode($response);
?>
