<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "lawyercasepro");
if (!$conn) die("Database Connection Failed: " . mysqli_connect_error());

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clientName    = $_POST['clientName'];
    $caseNo        = $_POST['caseNo'];
    $caseType      = $_POST['caseType'];
    $courtName     = $_POST['courtName'];
    $oppositeParty = $_POST['oppositeParty'];

    if ($clientName && $caseNo && $caseType && $courtName) {

        // Check if case exists in advocateDashboard
        $check = mysqli_query($conn, "SELECT * FROM advocateDashboard WHERE caseNo='$caseNo'");
        if (mysqli_num_rows($check) > 0) {
            $message = "<p style='color:red;'>Case number already exists!</p>";
        } else {

            // Insert into advocateDashboard
            mysqli_query($conn, "INSERT INTO advocateDashboard 
                (caseNo, caseType, clientName, oppositeParty, courtName) 
                VALUES 
                ('$caseNo','$caseType','$clientName','$oppositeParty','$courtName')");

            // Insert into judgeDashboard silently
            mysqli_query($conn, "INSERT INTO judgeDashboard 
                (caseNo, caseType, clientName, courtName, status) 
                VALUES 
                ('$caseNo','$caseType','$clientName','$courtName','Open')");

            $message = "<p style='color:green;'>Case added successfully!</p>";
        }

    } else {
        $message = "<p style='color:red;'>All fields are required!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Advocate Dashboard | LawyerCasePro</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100">

<!-- HEADER -->
<header class="bg-white shadow fixed w-full top-0 z-40">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-2 text-xl font-bold text-blue-600">
            <i class="fas fa-scale-balanced"></i> LawyerCasePro
        </div>
        <div class="flex items-center gap-6">
            <span class="text-gray-600">Advocate Panel</span>
            <a href="./LawyerCaseManagement_Landing Page.html" class="text-red-500 font-semibold">Logout</a>
        </div>
    </div>
</header>


<main class="pt-28 max-w-7xl mx-auto px-6">

<section class="bg-white shadow rounded-xl p-6 mb-10">
    <h2 class="text-xl font-semibold mb-3">How to Use the Advocate Dashboard</h2>
    <p class="text-gray-600 mb-4">
        Welcome to LawyerCasePro Advocate Dashboard.  
        Here you can add, update, and manage advocate information easily.  
        An example entry is provided below to guide you. Thank You!
    </p>

    <ul class="list-disc list-inside text-gray-700 mb-6 space-y-1">
        <li><strong>Advocate Name:</strong> Full name of the advocate.</li>
        <li><strong>Bar Council ID:</strong> Official ID from Bar Council.</li>
        <li><strong>Email:</strong> Advocate's email address.</li>
        <li><strong>Phone Number:</strong> Contact number.</li>
        <li><strong>Specialization:</strong> Field of expertise (Civil, Criminal, Family, etc.).</li>
        <li><strong>Status:</strong> Current availability (Active, Inactive).</li>
    </ul>

    <div class="bg-gray-50 border rounded-lg p-4 overflow-x-auto">
        <h3 class="text-lg font-semibold mb-2">Example Advocate Entry</h3>
        <table class="w-full text-sm text-left border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">Advocate Name</th>
                    <th class="border px-3 py-2">Bar Council ID</th>
                    <th class="border px-3 py-2">Email</th>
                    <th class="border px-3 py-2">Phone Number</th>
                    <th class="border px-3 py-2">Specialization</th>
                    <th class="border px-3 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border px-3 py-2">Abdul Karim</td>
                    <td class="border px-3 py-2">BC-2025-009</td>
                    <td class="border px-3 py-2">abdul.karim@example.com</td>
                    <td class="border px-3 py-2">017XXXXXXXX</td>
                    <td class="border px-3 py-2">Civil Law</td>
                    <td class="border px-3 py-2">Active</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>












<h1 class="text-2xl font-bold mb-6">Manage Cases</h1>



<!-- ADD ADVOCATE FORM -->
<div class="bg-white shadow rounded-xl p-6 mb-8">
    <h2 class="text-lg font-semibold mb-4">Add New Advocate</h2>
    <?php if($message) echo $message; ?>
    
    <form class="grid grid-cols-1 md:grid-cols-3 gap-4" method="POST">
        
        <div>
            <label class="block text-sm font-medium mb-1">Advocate Name</label>
            <input type="text" name="advocateName" class="border rounded-lg px-4 py-2 w-full" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Bar Council ID</label>
            <input type="text" name="barCouncilId" class="border rounded-lg px-4 py-2 w-full" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" class="border rounded-lg px-4 py-2 w-full" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Phone Number</label>
            <input type="tel" name="phone" class="border rounded-lg px-4 py-2 w-full" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Specialization</label>
            <input type="text" name="specialization" class="border rounded-lg px-4 py-2 w-full" placeholder="Civil / Criminal / Family" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <input type="text" name="status" class="border rounded-lg px-4 py-2 w-full" placeholder="Active / Inactive" required>
        </div>

        <div class="flex items-end">
            <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 w-full">
                Add Advocate
            </button>
        </div>

    </form>
</div>









<!-- CASES TABLE -->
<div class="bg-white shadow rounded-xl p-6">
    <h2 class="text-lg font-semibold mb-4">My Case List</h2>
    <table class="w-full text-sm border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 text-left">Case No</th>
                <th class="p-3 text-left">Case Type</th>
                <th class="p-3 text-left">Client</th>
                <th class="p-3 text-left">Opposite Party</th>
                <th class="p-3 text-left">Court</th>
                <th class="p-3 text-left">Next Hearing</th>
                <th class="p-3 text-left">Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM advocateDashboard ORDER BY id DESC";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0):
            while ($case = mysqli_fetch_assoc($result)):
        ?>
            <tr class="border-t">
                <td class="p-3"><?php echo $case['caseNo']; ?></td>
                <td class="p-3"><?php echo $case['caseType']; ?></td>
                <td class="p-3"><?php echo $case['clientName']; ?></td>
                <td class="p-3"><?php echo $case['oppositeParty']; ?></td>
                <td class="p-3"><?php echo $case['courtName']; ?></td>
                <td class="p-3"><?php echo date('d M Y', strtotime($case['nextHearing'])); ?></td>
                <td class="p-3"><?php echo $case['status']; ?></td>
            </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr>
                <td colspan="7" class="p-4 text-center text-gray-500">No cases found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</main>

<footer class="bg-white border-t mt-16">
    <div class="max-w-7xl mx-auto px-6 py-4 text-center text-sm text-gray-500">
        © 2026 LawyerCasePro. All rights reserved.
    </div>
</footer>

</body>
</html>
<?php mysqli_close($conn); ?>
