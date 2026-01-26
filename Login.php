<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "lawyercasepro");
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data safely
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Basic validation
    if ($email == '' || $password == '' || $role == '') {
        echo "<script>alert('All fields are required');</script>";
        exit;
    }

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
        echo "<script>
                window.location.href = './Admin Dashboard.php';
              </script>";
        exit;

    case 'Advocate':
        echo "<script>
                window.location.href = './Advocate Dashboard.php';
              </script>";
        exit;

    case 'Judge':
        echo "<script>
                window.location.href = './Judge Dashboard.php';
              </script>";
        exit;

    case 'Client':
        echo "<script>
                window.location.href = './Client Dashboard.php';
              </script>";
        exit;

    default:
        echo "<script>
                alert('Invalid role! Try again...');
                window.location.href = './Login.php';
              </script>";
        exit;
}


        } else {
            echo "<script>alert('Incorrect password');</script>";
        }

    } else {
        echo "<script>alert('Account not found!');</script>";
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
               <a href="index.html" class="flex items-center space-x-2 text-2xl font-bold text-[#0060df]">
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

               <form class="space-y-5" method="POST" action="./Login.php">

                    <!-- Email -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                         <input type="email" name="email" placeholder="Enter your email"
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
                              <option value="Advocate">Advocate</option>
                              <option value="Admin">Admin</option>
                              <option value="Judge">Judge</option>
                              <option value="Client">Client</option>
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
   <footer class="bg-white border-t mt-16">
    <div class="max-w-7xl mx-auto px-6 py-4 text-center text-sm text-gray-500">
        © 2026 LawyerCasePro. All rights reserved.
    </div>
</footer>

</body>

</html>
