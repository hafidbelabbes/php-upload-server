<?php
header('Content-Type: application/json');

$uploadDir = 'uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $fileName = uniqid() . "_" . basename($_FILES['file']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        echo json_encode([
            "success" => true,
            "url" => $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/uploads/" . $fileName
        ]);
    } else {
        echo json_encode(["success" => false, "error" => "Upload failed"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "No file uploaded"]);
}
?>
