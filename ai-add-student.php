<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ai-login.php");
    exit();
}

include 'ai-db.php';

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $roll = $conn->real_escape_string(trim($_POST['roll']));
    $class = $conn->real_escape_string(trim($_POST['class']));
    $photo = ''; // Will handle photo upload later

    // Validate inputs
    if (empty($name) || empty($roll) || empty($class)) {
        $error = "📝 All fields are required!";
    } else {
        // Check if roll number exists
        $check = $conn->query("SELECT id FROM students WHERE roll_number = '$roll'");
        if ($check->num_rows > 0) {
            $error = "🆔 Roll number already exists!";
        } else {
            // Insert student
            $sql = "INSERT INTO students (name, roll_number, class) VALUES ('$name', '$roll', '$class')";
            if ($conn->query($sql)) {
                $success = "🎉 Student added successfully!";
                // Clear form
                $_POST = array();
            } else {
                $error = "❌ Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>👨‍🎓 Add Student - Attendance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .success-message {
            animation: fadeIn 0.5s ease-out;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto p-4 max-w-4xl">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                <i class="fas fa-user-graduate mr-2 text-indigo-600"></i>
                Add New Student
            </h1>
            <a href="ai-dashboard.php" class="flex items-center space-x-2 text-indigo-600 hover:text-indigo-800 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Dashboard</span>
            </a>
        </div>

        <!-- Success Message -->
        <?php if ($success): ?>
            <div class="success-message mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg flex items-center">
                <i class="fas fa-check-circle text-xl mr-3"></i>
                <div>
                    <p class="font-medium"><?= $success ?></p>
                    <p class="text-sm mt-1">Student has been registered in the system.</p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                <div>
                    <p class="font-medium"><?= $error ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="form-container rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <div class="bg-indigo-600 px-6 py-4">
                <h2 class="text-white font-medium flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Student Information
                </h2>
            </div>
            
            <form method="POST" class="p-6" id="studentForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-user mr-2 text-indigo-600"></i>
                            Full Name
                        </label>
                        <input type="text" id="name" name="name" required 
                               value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 input-focus focus:ring-2 focus:ring-indigo-200 transition">
                    </div>
                    
                    <!-- Roll Number -->
                    <div>
                        <label for="roll" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-id-card mr-2 text-indigo-600"></i>
                            Roll Number
                        </label>
                        <input type="text" id="roll" name="roll" required
                               value="<?= isset($_POST['roll']) ? htmlspecialchars($_POST['roll']) : '' ?>"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 input-focus focus:ring-2 focus:ring-indigo-200 transition">
                    </div>
                    
                    <!-- Class -->
                    <div>
                        <label for="class" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-school mr-2 text-indigo-600"></i>
                            Class/Grade
                        </label>
                        <select id="class" name="class" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 input-focus focus:ring-2 focus:ring-indigo-200 transition">
                            <option value="">Select Class</option>
                            <option value="Class 1" <?= (isset($_POST['class']) && $_POST['class'] == 'Class 1') ? 'selected' : '' ?>>Class 1</option>
                            <option value="Class 2" <?= (isset($_POST['class']) && $_POST['class'] == 'Class 2') ? 'selected' : '' ?>>Class 2</option>
                            <option value="Class 3" <?= (isset($_POST['class']) && $_POST['class'] == 'Class 3') ? 'selected' : '' ?>>Class 3</option>
                            <option value="Class 4" <?= (isset($_POST['class']) && $_POST['class'] == 'Class 4') ? 'selected' : '' ?>>Class 4</option>
                            <option value="Class 5" <?= (isset($_POST['class']) && $_POST['class'] == 'Class 5') ? 'selected' : '' ?>>Class 5</option>
                            <option value="Class 6" <?= (isset($_POST['class']) && $_POST['class'] == 'Class 6') ? 'selected' : '' ?>>Class 6</option>
                            <option value="Class 7" <?= (isset($_POST['class']) && $_POST['class'] == 'Class 7') ? 'selected' : '' ?>>Class 7</option>
                            <option value="Class 8" <?= (isset($_POST['class']) && $_POST['class'] == 'Class 8') ? 'selected' : '' ?>>Class 8</option>
                            <option value="Class 9" <?= (isset($_POST['class']) && $_POST['class'] == 'Class 9') ? 'selected' : '' ?>>Class 9</option>
                            <option value="Class 10" <?= (isset($_POST['class']) && $_POST['class'] == 'Class 10') ? 'selected' : '' ?>>Class 10</option>
                        </select>
                    </div>
                    
                    <!-- Photo Upload (Future Enhancement) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <i class="fas fa-camera mr-2 text-indigo-600"></i>
                            Student Photo (Coming Soon)
                        </label>
                        <div class="mt-1 flex items-center">
                            <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center text-gray-400">
                                <i class="fas fa-user text-xl"></i>
                            </span>
                            <button type="button" disabled class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Upload Photo
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 flex justify-end space-x-3">
                    <button type="reset" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center">
                        <i class="fas fa-undo mr-2"></i>
                        Reset Form
                    </button>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Save Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Simple form validation
        document.getElementById('studentForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const roll = document.getElementById('roll').value.trim();
            const cls = document.getElementById('class').value;
            
            if (!name || !roll || !cls) {
                e.preventDefault();
                alert('Please fill all required fields!');
            }
        });
    </script>
</body>
</html>