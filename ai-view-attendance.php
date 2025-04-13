<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ai-login.php");
    exit();
}

include 'ai-db.php';

// Default filter values
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';
$filter_class = isset($_GET['class']) ? $_GET['class'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Build the query with filters
$query = "SELECT s.name, s.roll_number, s.class, a.date, a.status 
          FROM attendance a 
          JOIN students s ON a.student_id = s.id 
          WHERE 1=1";

if (!empty($filter_date)) {
    $query .= " AND a.date = '" . $conn->real_escape_string($filter_date) . "'";
}
if (!empty($filter_class)) {
    $query .= " AND s.class = '" . $conn->real_escape_string($filter_class) . "'";
}
if (!empty($filter_status)) {
    $query .= " AND a.status = '" . $conn->real_escape_string($filter_status) . "'";
}

$query .= " ORDER BY a.date DESC, s.name ASC";

$result = $conn->query($query);

// Get unique classes for filter dropdown
$classes = $conn->query("SELECT DISTINCT class FROM students ORDER BY class ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 View Records - Attendance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: 
                linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .records-container {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
        }
        .present-badge {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .absent-badge {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        .filter-card {
            background: rgba(255, 255, 255, 0.95);
        }
    </style>
</head>
<body class="min-h-screen p-4 md:p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    Attendance Records
                </h1>
                <p class="text-white/80 mt-1">View and filter attendance history</p>
            </div>
            <a href="ai-dashboard.php" class="mt-4 md:mt-0 flex items-center text-white hover:text-blue-200 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>

        <!-- Filter Card -->
        <div class="filter-card rounded-lg shadow-lg mb-6 p-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($filter_date) ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                    <select name="class" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">All Classes</option>
                        <?php while ($class = $classes->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($class['class']) ?>" 
                                <?= $filter_class == $class['class'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($class['class']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">All Statuses</option>
                        <option value="Present" <?= $filter_status == 'Present' ? 'selected' : '' ?>>Present</option>
                        <option value="Absent" <?= $filter_status == 'Absent' ? 'selected' : '' ?>>Absent</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" 
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md transition flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i>
                        Apply Filters
                    </button>
                    <a href="ai-view-attendance.php" 
                       class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md transition flex items-center justify-center">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Records Card -->
        <div class="records-container rounded-xl shadow-lg overflow-hidden">
            <div class="bg-indigo-600 px-6 py-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-white font-medium flex items-center">
                        <i class="fas fa-table mr-2"></i>
                        Attendance Data
                    </h2>
                    <span class="text-white/90 text-sm">
                        <?= $result->num_rows ?> records found
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-user mr-1"></i> Student
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-id-card mr-1"></i> Roll No
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-school mr-1"></i> Class
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-calendar-day mr-1"></i> Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <i class="fas fa-info-circle mr-1"></i> Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-indigo-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['name']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($row['roll_number']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= htmlspecialchars($row['class']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= date('M j, Y', strtotime($row['date'])) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= $row['status'] == 'Present' ? 'present-badge' : 'absent-badge' ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    No attendance records found matching your criteria
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-3 flex items-center justify-between border-t border-gray-200">
                <div class="text-sm text-gray-500">
                    Showing <span class="font-medium">1</span> to <span class="font-medium"><?= $result->num_rows ?></span> of <span class="font-medium"><?= $result->num_rows ?></span> results
                </div>
                <!-- Pagination would go here -->
            </div>
        </div>
    </div>
</body>
</html>