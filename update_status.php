<?php
require 'config.php';

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    if (!isset($_POST['id'], $_POST['status'])) {
        echo json_encode(["success" => false, "message" => "Invalid input data."]);
        exit;
    }

    $id = intval($_POST['id']);
    $status = $conn->real_escape_string($_POST['status']);

    // Update query
    $stmt = $conn->prepare("UPDATE request SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Status updated successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update status: " . $conn->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
