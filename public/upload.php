<?php
header('Content-Type: application/json'); // ✅ Set JSON header

$uploadDir = 'uploads/';
if (isset($_POST['folder'])) {
    $uploadDir .= $_POST['folder'] . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
}

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tempName = $_FILES['image']['tmp_name'];
    $fileName = basename($_FILES['image']['name']);
    $targetPath = $uploadDir . $fileName;

    $i = 0;
    $originalFileName = pathinfo($fileName, PATHINFO_FILENAME);
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    while (file_exists($targetPath)) {
        $i++;
        $fileName = $originalFileName . '_' . $i . '.' . $extension;
        $targetPath = $uploadDir . $fileName;
    }

    if (move_uploaded_file($tempName, $targetPath)) {
        // ✅ Return JSON response
        echo json_encode(['url' => "https://php-upload-server.onrender.com". $targetPath]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to move uploaded file.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Error during file upload: ' . $_FILES['image']['error']]);
}
?>
