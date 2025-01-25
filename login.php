<?php
require 'config.php';
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: result.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];


    // Query to check if user exists
    $sql = "SELECT id, username FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['user_id'] = $result->fetch_assoc()['id'];
        header("Location: result.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link href="output.css" rel="stylesheet" />
    <link rel="apple-touch-icon" sizes="180x180" href="asset/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="asset/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="asset/favicon-16x16.png" />
    <link rel="manifest" href="asset/site.webmanifest" />
</head>
<body class="body-bg min-h-screen flex flex-col items-center justify-center">

    <!-- Container -->
    <div class="w-full max-w-sm mx-auto p-4 bg-white shadow-lg rounded-md">

        <!-- Form -->
        <div>
        <img src="logo.png" alt="Company Logo" class="mx-auto w-25 h-40 mb-4" />

            <h2 class="text-xl font-bold text-gray-800 text-center mb-6 mr-4">تسجيل الدخول</h2>

            <?php if (isset($error)): ?>
                <div class="bg-red-500 text-white px-4 py-2 mb-4 rounded">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">اسم المستخدم</label>
                    <input
                        type="text"
                        name="username"
                        id="username"
                        class="form-input w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                        placeholder="Username"
                    />
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-input w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                        placeholder="Password"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-blue-600 transition duration-200"
                >تسجيل الدخول</button>
            </form>
        </div>
    </div>


</body>
</html>

