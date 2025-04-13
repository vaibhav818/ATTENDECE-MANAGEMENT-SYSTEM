<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ai-login.php");
    exit();
}

include 'ai-db.php';

$date = date("Y-m-d");
$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['status'] as $student_id => $status) {
        $student_id = $conn->real_escape_string($student_id);
        $status = $conn->real_escape_string($status);
        
        // Check if attendance exists
        $check_sql = "SELECT id FROM attendance WHERE student_id = '$student_id' AND date = '$date'";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows > 0) {
            // Update existing
            $update_sql = "UPDATE attendance SET status = '$status' WHERE student_id = '$student_id' AND date = '$date'";
            if (!$conn->query($update_sql)) {
                $error = "Error updating attendance: " . $conn->error;
            }
        } else {
            // Insert new
            $insert_sql = "INSERT INTO attendance (student_id, date, status) VALUES ('$student_id', '$date', '$status')";
            if (!$conn->query($insert_sql)) {
                $error = "Error marking attendance: " . $conn->error;
            }
        }
    }
    
    if (empty($error)) {
        $success = "✅ Attendance marked successfully for " . date('F j, Y');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📝 Mark Attendance - Attendance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: 
                linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                url('https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        .attendance-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
        }
        .student-card {
            transition: all 0.3s ease;
        }
        .student-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .present {
            border-left: 4px solid #10B981;
        }
        .absent {
            border-left: 4px solid #EF4444;
        }
    </style>
</head>
<body class="p-4 md:p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-white flex items-center">
                    <i class="fas fa-calendar-check mr-3"></i>
                    Mark Attendance
                </h1>
                <p class="text-white/80 mt-1"><?= date('l, F j, Y') ?></p>
            </div>
            <a href="ai-dashboard.php" class="mt-4 md:mt-0 flex items-center text-white hover:text-blue-200 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>

        <!-- Status Messages -->
        <?php if ($success): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg flex items-center animate-fadeIn">
                <i class="fas fa-check-circle text-xl mr-3 text-green-500"></i>
                <div>
                    <p class="font-medium"><?= $success ?></p>
                    <p class="text-sm">Attendance records have been updated.</p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle text-xl mr-3 text-red-500"></i>
                <div>
                    <p class="font-medium"><?= $error ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Attendance Form -->
        <div class="attendance-card rounded-xl shadow-lg overflow-hidden">
            <div class="bg-indigo-600 px-6 py-4">
                <h2 class="text-white font-medium flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    Student Attendance
                </h2>
            </div>

            <form method="POST" class="p-6">
                <div class="grid grid-cols-1 gap-4 max-h-[70vh] overflow-y-auto pr-2">
                    <?php
                    $students = $conn->query("SELECT * FROM students ORDER BY name ASC");
                    while ($row = $students->fetch_assoc()):
                        $attendance_sql = "SELECT status FROM attendance WHERE student_id = {$row['id']} AND date = '$date'";
                        $attendance_result = $conn->query($attendance_sql);
                        $current_status = $attendance_result->num_rows > 0 
                            ? $attendance_result->fetch_assoc()['status'] 
                            : 'Present';
                    ?>
                    <div class="student-card bg-white p-4 rounded-lg shadow-sm <?= $current_status == 'Present' ? 'present' : 'absent' ?>">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="bg-indigo-100 p-3 rounded-full">
                                    <i class="fas fa-user text-indigo-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-medium"><?= htmlspecialchars($row['name']) ?></h3>
                                    <p class="text-sm text-gray-500">Roll: <?= htmlspecialchars($row['roll_number']) ?> | Class: <?= htmlspecialchars($row['class']) ?></p>
                                </div>
                            </div>
                            <select name="status[<?= $row['id'] ?>]" 
                                    class="border rounded px-3 py-1 focus:ring-2 focus:ring-indigo-200 <?= $current_status == 'Present' ? 'bg-green-50' : 'bg-red-50' ?>">
                                <option value="Present" <?= $current_status == 'Present' ? 'selected' : '' ?>>Present</option>
                                <option value="Absent" <?= $current_status == 'Absent' ? 'selected' : '' ?>>Absent</option>
                            </select>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button type="submit" 
                            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Save Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Dynamic status color change
        document.querySelectorAll('select[name^="status"]').forEach(select => {
            select.addEventListener('change', function() {
                const card = this.closest('.student-card');
                card.classList.remove('present', 'absent');
                if (this.value === 'Present') {
                    card.classList.add('present');
                    this.classList.add('bg-green-50');
                    this.classList.remove('bg-red-50');
                } else {
                    card.classList.add('absent');
                    this.classList.add('bg-red-50');
                    this.classList.remove('bg-green-50');
                }
            });
        });
    </script>
</body>
</html>