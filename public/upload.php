<?php
header('Content-Type: application/json');

$uploadDir = 'uploads/'; // مجلد لحفظ الصور، تأكد أنه قابل للكتابة (chmod 777 أو 755)

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileName = basename($_FILES['image']['name']);
    $fileSize = $_FILES['image']['size'];
    $fileType = $_FILES['image']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension; // اسم فريد للملف

    $destPath = $uploadDir . $newFileName;

    // أنواع الملفات المسموح بها (تحقق أمني مهم)
    $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');

    if (in_array($fileExtension, $allowedfileExtensions)) {
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // قم بإرجاع الرابط الكامل للصورة
            // تأكد من أن 'your_domain.com/uploads/' هو المسار الصحيح للوصول إلى المجلد عبر HTTP
            $imageUrl = 'https://your_domain.com/uploads/' . $newFileName;
            echo json_encode(['success' => true, 'imageUrl' => $imageUrl]);
        } else {
            echo json_encode(['success' => false, 'message' => 'There was an error moving the uploaded file.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No image uploaded or an upload error occurred.']);
}
?>
