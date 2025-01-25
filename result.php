<?php
require 'config.php';
session_start();

// Check if the user is logged in, otherwise redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Define the number of records per page
$records_per_page = 4;

// Get the current page from the URL, default is 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the OFFSET
$offset = ($current_page - 1) * $records_per_page;

// Fetch data with LIMIT and OFFSET
$sql = "SELECT id,name, subject, priority, problem, comment, date, dueDate, file_input,status
        FROM request 
        LIMIT $records_per_page OFFSET $offset";
$result = $conn->query($sql);

// Get the total number of records for pagination
$total_records_query = "SELECT COUNT(*) AS total FROM request";
$total_records_result = $conn->query($total_records_query);
$total_records = $total_records_result->fetch_assoc()['total'];

// Calculate total pages
$total_pages = ceil($total_records / $records_per_page);

?>

<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="output.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.3/jspdf.plugin.autotable.min.js"></script>

    <title>طلبات الصيانة</title>
</head>
<body class="body-bg">
    
    <div class="form-container max-w-6xl mx-auto p-6 bg-white shadow-md rounded-md">
        <!-- Logout Button -->
        <div class="flex justify-end mb-4">
            <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">تسجيل الخروج</a>
        </div>

        <img src="logo.png" alt="Company Logo" class="mx-auto w-25 h-40 mb-6" />
        <h2 class="text-2xl font-bold mb-4 mr-3 text-center">طلبات الصيانة</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            <?php
            if ($result->num_rows > 0) {
                // Output each row of data as a card
                while($row = $result->fetch_assoc()) {
                    echo '<div class="p-4 bg-white border rounded shadow hover:shadow-lg transition-shadow duration-200">';
                    
                    // Display image if exists (file_input)
                    if (!empty($row['file_input'])) {
                        echo '<img src="uploads/' . htmlspecialchars($row['file_input']) . '" alt="Image" class="w-full h-48 mb-4 rounded object-cover">';
                    }
                    echo '<h2 class="text-sm font-bold mb-2 text-gray-800"><strong>رقم الطلب: </strong>' . htmlspecialchars($row['id']) . '</h2>';

                    echo "<div class='mb-4'>"; // Added margin-bottom for separation
                    echo "<select data-id='" . $row['id'] . "' class='status-dropdown min-w-[150px] px-2 py-1 text-center mb-2'>";
                    $statuses = ['طلب جديد', 'قيد التنفيذ', 'تم الاصلاح'];
                    foreach ($statuses as $status) {
                        $selected = ($row['status'] == $status) ? "selected" : "";
                        echo "<option value='$status' $selected>$status</option>";
                    }
                    echo "</select>";
                    echo "</div>"; // Close the margin div

                    echo '<h2 class="text-sm font-bold mb-2 text-gray-800">' . htmlspecialchars($row['name']) . '</h2>';
                    echo '<p class="text-sm text-gray-600 mb-1"><strong>نوع الصيانة المطلوبة:</strong> ' . htmlspecialchars($row['subject']) . '</p>';
                    echo '<p class="text-sm text-gray-600 mb-1"><strong>ملخص طلب الصيانة:</strong> ' . htmlspecialchars($row['problem']) . '</p>';
                    echo '<p class="text-sm text-gray-600 mb-1"><strong>الأولوية:</strong> ' . htmlspecialchars($row['priority']) . '</p>';
                    echo '<p class="text-sm text-gray-600 mb-1"><strong>ملاحضات:</strong> ' . htmlspecialchars($row['comment']) . '</p>';
                    echo '<p class="text-sm text-gray-600 mb-1"><strong>تاريخ الطلب:</strong> ' . htmlspecialchars($row['date']) . '</p>';
                    echo '<p class="text-sm text-gray-600"><strong>التاريخ المطلوب لانجاز العمل:</strong> ' . htmlspecialchars($row['dueDate']) . '</p>';

                    // Delete button - placed inside a flex container
                    echo "<div class='flex justify-center mt-4'>"; // Flex container to center the button
                    echo "<button data-id='" . $row['id'] . "' class='delete-button bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 w-full sm:w-auto'>حذف</button>";
                    echo "</div>"; // Close the flex container
                    


                    echo '</div>';
                }
            } else {
                echo '<p class="text-center text-gray-600">لا توجد بيانات لعرضها.</p>';
            }
            ?>
            
        </div>

        <div class="pagination mt-4 flex justify-center">
        <?php if ($total_pages > 1): ?>
            <nav class="inline-flex">
                <!-- Previous Page Link -->
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>" 
                       class="px-4 py-2 border bg-gray-200 hover:bg-gray-300 rounded-l">
                        السابق
                    </a>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php
                $range = 2; // Number of pages to show before and after the current page
                $start = max(1, $current_page - $range);
                $end = min($total_pages, $current_page + $range);

                // First page
                if ($start > 1) {
                    echo '<a href="?page=1" class="px-4 py-2 border bg-gray-200 hover:bg-gray-300">1</a>';
                    if ($start > 2) {
                        echo '<span class="px-4 py-2">...</span>';
                    }
                }

                // Middle pages
                for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" 
                       class="px-4 py-2 border <?php echo ($i == $current_page) ? 'bg-indigo-500 text-white' : 'bg-gray-200 hover:bg-gray-300'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor;

                // Last page
                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) {
                        echo '<span class="px-4 py-2">...</span>';
                    }
                    echo '<a href="?page=' . $total_pages . '" class="px-4 py-2 border bg-gray-200 hover:bg-gray-300">' . $total_pages . '</a>';
                }
                ?>

                <!-- Next Page Link -->
                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>" 
                       class="px-4 py-2 border bg-gray-200 hover:bg-gray-300 rounded-r">
                        التالي
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
        </div>

        <!-- Download Button -->
        <div class="flex justify-center mt-4">
            <a href="generate_report.php" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-blue-600">
                تنزيل تقرير اكسل
            </a>
        </div>
    </div>

     <!-- Footer -->
      <footer class="footer-text text-center py-4 mt-6">
      <p class="text-sm font-medium text-gray-700">Tools System</p>
            <p class="text-sm font-medium text-gray-700" dir="ltr">© 2025 Planning and Development Department. All rights reserved.</p>

      <p class="text-xs text-gray-500 mt-1">Version 0.1.1-beta.1</p>
    </footer>

    <script>
    $(document).ready(function () {
        // Handle status change
        $(".status-dropdown").change(function () {
            const customerId = $(this).data("id");
            const newStatus = $(this).val();

            $.ajax({
                url: "update_status.php",
                method: "POST",
                data: { id: customerId, status: newStatus },
                success: function (response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        Swal.fire({
                            icon: "success",
                            title: "تم التحديث",
                            text: result.message,
                            timer: 1500,
                            showConfirmButton: false,
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "خطأ",
                            text: result.message,
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: "error",
                        title: "خطأ",
                        text: "حدث خطأ أثناء الاتصال بالخادم.",
                    });
                },
            });
        });
          
        // Handle delete
        $(".delete-button").click(function () {
            const customerId = $(this).data("id");
            Swal.fire({
                title: "هل أنت متأكد؟",
                text: "لن تتمكن من استعادة هذا السجل!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                cancelButtonText: "لا",
                confirmButtonText: "نعم"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "delete_customer.php",
                        method: "POST",
                        data: { id: customerId },
                        success: function (response) {
                            const result = JSON.parse(response);
                            if (result.success) {
                                Swal.fire({
                                    icon: "success",
                                    title: "تم الحذف",
                                    text: result.message,
                                    timer: 1500,
                                    showConfirmButton: false,
                                });
                                location.reload(); // Refresh the page
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "خطأ",
                                    text: result.message,
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: "error",
                                title: "خطأ",
                                text: "حدث خطأ أثناء الاتصال بالخادم.",
                            });
                        },
                    });
                }
            });
        });
    });
    </script>

</body>
</html>
