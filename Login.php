<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "lawyercasepro");
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// $message holds the text shown in the on-page alert box.
// $messageType controls its color ('error' | 'success').
$message = '';
$messageType = 'error';

// Coming here right after a successful registration
if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $message = "Registration successful! You can now log in.";
    $messageType = 'success';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data safely
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Basic validation
    if ($email == '' || $password == '' || $role == '') {
        $message = "All fields are required.";
    } else {

        // SQL Query
        $sql = "SELECT * FROM register WHERE email='$email' AND role='$role'";
        $result = mysqli_query($conn, $sql);

        // Check query execution
        if (!$result) {
            die("Query Failed: " . mysqli_error($conn));
        }

        // Check user exists
        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {

                // Store session data
                $_SESSION['email'] = $user['email'];
                $_SESSION['fullName'] = $user['fullName'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                switch ($user['role']) {
                    case 'Admin':
                        header("Location: ./Admin Dashboard.php");
                        exit;
                    case 'Advocate':
                        header("Location: ./Advocate Dashboard.php");
                        exit;
                    case 'Judge':
                        header("Location: ./Judge Dashboard.php");
                        exit;
                    case 'Client':
                        header("Location: ./Client Dashboard.php");
                        exit;
                    default:
                        $message = "Invalid role! Please try again.";
                }

            } else {
                $message = "Incorrect password.";
            }

        } else {
            $message = "Account not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Login | LawyerCasePro</title>
     <script src="https://cdn.tailwindcss.com"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-blue-50 text-gray-800">

     <!-- Header -->
     <header class="bg-white shadow-md fixed w-full z-40 py-4">
          <div class="container mx-auto flex justify-between items-center px-6">
               <a href="./LawyerCaseManagement_Landing Page.html" class="flex items-center space-x-2 text-2xl font-bold text-[#0060df]">
                    <i class="fas fa-balance-scale"></i>
                    <span>LawyerCasePro</span>
               </a>
               <a href="./Register.php"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Register
               </a>
          </div>
     </header>

     <!-- Login Section -->
     <section class="pt-32 pb-20">
          <div class="container mx-auto px-6 max-w-md bg-white shadow-lg rounded-2xl p-8">
               <h2 class="text-3xl font-bold text-center text-[#0060df] mb-6">Welcome Back</h2>

               <!-- ================= ALERT BOX ================= -->
               <?php if ($message): ?>
               <div class="mb-6 flex items-start gap-3 rounded-lg border px-4 py-3 text-sm
                    <?= $messageType === 'success'
                         ? 'bg-green-50 border-green-300 text-green-700'
                         : 'bg-red-50 border-red-300 text-red-700'; ?>">
                    <i class="fas <?= $messageType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?> mt-0.5"></i>
                    <span><?= htmlspecialchars($message); ?></span>
               </div>
               <?php endif; ?>

               <form class="space-y-5" method="POST" action="./Login.php">

                    <!-- Email -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                         <input type="email" name="email" placeholder="Enter your email"
                              value="<?= isset($email) ? htmlspecialchars($email) : ''; ?>"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0060df] focus:outline-none"
                              required>
                    </div>

                    <!-- Password -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                         <input type="password" name="password" placeholder="Enter your password"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0060df] focus:outline-none"
                              required>
                    </div>

                    <!-- Role -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Login As</label>
                         <select name="role"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0060df] focus:outline-none"
                              required>
                              <option value="">-- Select Role --</option>
                              <option value="Advocate" <?= (isset($role) && $role === 'Advocate') ? 'selected' : ''; ?>>Advocate</option>
                              <option value="Admin" <?= (isset($role) && $role === 'Admin') ? 'selected' : ''; ?>>Admin</option>
                              <option value="Judge" <?= (isset($role) && $role === 'Judge') ? 'selected' : ''; ?>>Judge</option>
                              <option value="Client" <?= (isset($role) && $role === 'Client') ? 'selected' : ''; ?>>Client</option>
                         </select>
                    </div>

                    <button type="submit"
                         class="w-full bg-[#0060df] text-white py-3 rounded-full font-semibold hover:bg-[#0047a0] transition">
                         Login
                    </button>

               </form>

               <p class="text-center text-sm text-gray-600 mt-6">
                    Don’t have an account?
                    <a href="./Register.php" class="text-[#0060df] font-semibold hover:underline">Register here</a>
               </p>
          </div>
     </section>

     <!-- Footer -->
     <footer class="bg-[#001f3f] text-white py-6 text-center mt-16 rounded-t-3xl">
          <p class="text-sm">&copy; 2026 LawyerCasePro. All rights reserved.</p>
     </footer>

</body>

</html>
