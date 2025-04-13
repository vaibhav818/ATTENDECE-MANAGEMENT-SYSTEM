<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ai-login.php");
    exit();
}

include 'ai-db.php';

// Get statistics
$totalStudents = $conn->query("SELECT COUNT(*) FROM students")->fetch_array()[0];
$today = date('Y-m-d');
$presentToday = $conn->query("SELECT COUNT(*) FROM attendance WHERE date = '$today' AND status = 'Present'")->fetch_array()[0];
$absentToday = $conn->query("SELECT COUNT(*) FROM attendance WHERE date = '$today' AND status = 'Absent'")->fetch_array()[0];
$attendanceRate = $totalStudents > 0 ? round(($presentToday / $totalStudents) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📊 Dashboard - Attendance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #10b981;
        }
        body {
            background-color: #f3f4f6;
            font-family: 'Inter', sans-serif;
        }
        .sidebar {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
        }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .progress-ring__circle {
            transition: stroke-dashoffset 0.6s ease;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
    </style>
</head>
<body class="flex">
    <!-- Sidebar -->
    <div class="sidebar w-64 text-white p-4 hidden md:block">
        <div class="flex items-center space-x-3 mb-8 p-2">
            <div class="bg-indigo-500 p-2 rounded-lg">
                <i class="fas fa-school text-xl"></i>
            </div>
            <h1 class="text-xl font-bold">Attendance Pro</h1>
        </div>
        
        <nav class="space-y-2">
            <a href="#" class="flex items-center space-x-3 p-3 bg-slate-700 rounded-lg">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="ai-add-student.php" class="flex items-center space-x-3 p-3 hover:bg-slate-700 rounded-lg transition">
                <i class="fas fa-user-plus"></i>
                <span>Add Student</span>
            </a>
            <a href="ai-mark-attendance.php" class="flex items-center space-x-3 p-3 hover:bg-slate-700 rounded-lg transition">
                <i class="fas fa-check-circle"></i>
                <span>Mark Attendance</span>
            </a>
            <a href="ai-view-attendance.php" class="flex items-center space-x-3 p-3 hover:bg-slate-700 rounded-lg transition">
                <i class="fas fa-list-alt"></i>
                <span>View Records</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">📊 Dashboard Overview</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600"><?= date('l, F j, Y') ?></span>
                <a href="ai-logout.php" class="flex items-center space-x-2 bg-red-100 hover:bg-red-200 text-red-600 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Students -->
            <div class="stat-card bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Students</p>
                        <h3 class="text-2xl font-bold mt-1"><?= $totalStudents ?></h3>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-full">
                        <i class="fas fa-users text-indigo-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Present Today -->
            <div class="stat-card bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Present Today</p>
                        <h3 class="text-2xl font-bold mt-1 text-green-600"><?= $presentToday ?></h3>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-user-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Absent Today -->
            <div class="stat-card bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Absent Today</p>
                        <h3 class="text-2xl font-bold mt-1 text-red-600"><?= $absentToday ?></h3>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-user-times text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Attendance Rate -->
            <div class="stat-card bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Attendance Rate</p>
                        <h3 class="text-2xl font-bold mt-1"><?= $attendanceRate ?>%</h3>
                    </div>
                    <div class="relative w-12 h-12">
                        <svg class="w-full h-full" viewBox="0 0 36 36">
                            <path
                                d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#e6e7eb"
                                stroke-width="3"
                            />
                            <path
                                d="M18 2.0845
                                a 15.9155 15.9155 0 0 1 0 31.831
                                a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#10b981"
                                stroke-width="3"
                                stroke-dasharray="<?= $attendanceRate ?>, 100"
                            />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-chart-pie text-blue-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">📅 Recent Attendance</h2>
                <a href="ai-view-attendance.php" class="text-sm text-indigo-600 hover:underline">View All</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        $recent = $conn->query("
                            SELECT s.name, a.date, a.status 
                            FROM attendance a
                            JOIN students s ON a.student_id = s.id
                            ORDER BY a.date DESC LIMIT 5
                        ");
                        while($row = $recent->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $row['name'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $row['date'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?= $row['status'] == 'Present' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <a href="ai-add-student.php" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center space-x-4">
                <div class="bg-indigo-100 p-3 rounded-full">
                    <i class="fas fa-user-plus text-indigo-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold">Add New Student</h3>
                    <p class="text-sm text-gray-500">Register a new student</p>
                </div>
            </a>
            
            <a href="ai-mark-attendance.php" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center space-x-4">
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold">Mark Attendance</h3>
                    <p class="text-sm text-gray-500">Record today's attendance</p>
                </div>
            </a>
            
            <a href="ai-view-attendance.php" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex items-center space-x-4">
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-list-alt text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold">View Records</h3>
                    <p class="text-sm text-gray-500">Check attendance history</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Mobile Bottom Nav -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white shadow-lg border-t border-gray-200 flex justify-around py-3 px-2">
        <a href="#" class="flex flex-col items-center text-indigo-600">
            <i class="fas fa-tachometer-alt"></i>
            <span class="text-xs mt-1">Dashboard</span>
        </a>
        <a href="ai-add-student.php" class="flex flex-col items-center text-gray-600">
            <i class="fas fa-user-plus"></i>
            <span class="text-xs mt-1">Add</span>
        </a>
        <a href="ai-mark-attendance.php" class="flex flex-col items-center text-gray-600">
            <i class="fas fa-check-circle"></i>
            <span class="text-xs mt-1">Mark</span>
        </a>
        <a href="ai-view-attendance.php" class="flex flex-col items-center text-gray-600">
            <i class="fas fa-list-alt"></i>
            <span class="text-xs mt-1">View</span>
        </a>
    </div>
</body>
</html>