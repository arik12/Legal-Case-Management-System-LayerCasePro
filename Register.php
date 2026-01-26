<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "lawyercasepro");
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['fullName'];
    $role = $_POST['role'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($fullName && $role && $email && $contact && $password && $confirmPassword) {

        if ($password !== $confirmPassword) {
            echo "<p style='color:red;'>Passwords do not match!</p>";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Check if email already exists
            $checkQuery = "SELECT * FROM register WHERE email='$email'";
            $checkResult = mysqli_query($conn, $checkQuery);
            if (mysqli_num_rows($checkResult) > 0) {
                echo "<p style='color:red;'>This email is already registered.</p>";
            } else {
                $sql = "INSERT INTO register (fullName, role, email, contact, password)
                        VALUES ('$fullName', '$role', '$email', '$contact', '$hashedPassword')";
                if (mysqli_query($conn, $sql)) {
                    echo "<h3 style='color:green;'>Registration successful! You can now <a href='./Login.html'>Login</a>.</h3>";
                    $_SESSION['email'] = $email;
                    $_SESSION['role'] = $role;
                    $_SESSION['fullName'] = $fullName;
                } else {
                    echo "<p style='color:red;'>Error: " . mysqli_error($conn) . "</p>";
                }
            }
        }
    } else {
        echo "<p style='color:red;'>All fields are required.</p>";
    }

}


?>


<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Register | LawyerCasePro</title>
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
               <a href="./Login.php" class="px-4 py-2 bg-[#0060df] text-white rounded-lg hover:bg-[#0047a0] transition">
                    Login
               </a>
          </div>
     </header>





     <section class="pt-32 pb-20">
          <div class="container mx-auto px-6 max-w-lg bg-white shadow-lg rounded-2xl p-8">
               <h2 class="text-3xl font-bold text-center text-[#0060df] mb-6">Create Your Account</h2>

               <form class="space-y-5" method="POST" action="./Register.php">

                    <!-- Full Name -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                         <input type="text" name="fullName" placeholder="Enter your full name"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0060df] focus:outline-none"
                              required>
                    </div>

                    <!-- Role -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                         <select name="role"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0060df] focus:outline-none"
                              required>
                              <option value="" disabled selected>Select Role</option>
                              <option value="Advocate">Advocate</option>
                              <option value="Admin">Admin</option>
                              <option value="Judge">Judge</option>
                              <option value="Client">Client</option>
                         </select>
                    </div>

                    <!-- Email -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                         <input type="email" name="email" placeholder="Enter your email"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0060df] focus:outline-none"
                              required>
                    </div>

                    <!-- Contact -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                         <input type="tel" name="contact" placeholder="Enter your phone number"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0060df] focus:outline-none"
                              required>
                    </div>

                    <!-- Password -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                         <input type="password" name="password" placeholder="Create password"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0060df] focus:outline-none"
                              required>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                         <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                         <input type="password" name="confirmPassword" placeholder="Confirm password"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#0060df] focus:outline-none"
                              required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                         class="w-full bg-[#0060df] text-white py-3 rounded-full font-semibold hover:bg-[#0047a0] transition">
                         Register
                    </button>
               </form>


               <p class="text-center text-sm text-gray-600 mt-6">
                    Already have an account?
                    <a href="./Login.html" class="text-[#0060df] font-semibold hover:underline">Login here</a>
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
