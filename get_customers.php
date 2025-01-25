<?php
require 'config.php';

$sql = "SELECT id, name FROM users_tbl";
$result = $conn->query($sql);

$name = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name[] = $row; // Collect customer data
    }
}
echo json_encode($name); // Return as JSON
$conn->close();
?>
