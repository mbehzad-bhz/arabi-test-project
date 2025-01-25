<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $sql = "DELETE FROM request WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم حذف السجل بنجاح."]);
    } else {
        echo json_encode(["success" => false, "message" => "فشل حذف السجل."]);
    }

    $stmt->close();
}

$conn->close();
?>
