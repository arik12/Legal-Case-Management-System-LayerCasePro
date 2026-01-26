<?php
session_start();

// ================= DATABASE CONNECTION =================
$conn = mysqli_connect("localhost", "root", "", "lawyercasepro");
if (!$conn) die("Database Connection Failed: " . mysqli_connect_error());

// ================= FORM SUBMISSION =================
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clientName   = $_POST['clientName'];
    $caseNo       = $_POST['caseNo'];
    $caseType     = $_POST['caseType'];
    $courtName    = $_POST['courtName'];
    $status       = $_POST['status'];
    $advocateName = $_POST['advocate_name'];

    if ($clientName && $caseNo && $caseType && $courtName && $status && $advocateName) {

        // Check if case number exists
        $check = mysqli_query($conn, "SELECT * FROM adminDashboard WHERE caseNo='$caseNo'");
        if (mysqli_num_rows($check) > 0) {
            $message = "<p style='color:red;'>Case number already exists!</p>";
        } else {

            // Insert into adminDashboard
            mysqli_query($conn, "INSERT INTO adminDashboard 
                (clientName, caseNo, caseType, courtName, status, advocateName)
                VALUES ('$clientName','$caseNo','$caseType','$courtName','$status','$advocateName')"); 

                 mysqli_query($conn, "INSERT INTO judgeDashboard 
                (clientName, caseNo, caseType, courtName, status, advocateName)
                VALUES ('$clientName','$caseNo','$caseType','$courtName','$status','$advocateName')");

          

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
<title>Admin Dashboard | LawyerCasePro</title>
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
            <span class="text-gray-600">Admin Panel</span>
            <a href="./LawyerCaseManagement_Landing Page.html" class="text-red-500 font-semibold">Logout</a>
        </div>
    </div>
</header>

<main class="pt-28 max-w-7xl mx-auto px-6">

<!-- INSTRUCTIONS & EXAMPLE -->
<section class="bg-white shadow rounded-xl p-6 mb-10">
    <h2 class="text-xl font-semibold mb-3">How to Use the Admin Dashboard</h2>
    <p class="text-gray-600 mb-4">
        Welcome to LawyerCasePro Admin Dashboard.
        Here you can add new cases, assign advocates, and monitor all legal cases in one place.
        All data will automatically be sent to the Judge Dashboard for review.
    </p>

    <ul class="list-disc list-inside text-gray-700 mb-6 space-y-1">
        <li><strong>Client Name:</strong> Full name of the client.</li>
        <li><strong>Case Number:</strong> Official case or reference number.</li>
        <li><strong>Case Type:</strong> Nature of the case (Civil, Criminal, Family, etc.).</li>
        <li><strong>Court Name:</strong> Name of the court where the case is filed.</li>
        <li><strong>Advocate Name:</strong> Assigned advocate for the case.</li>
        <li><strong>Status:</strong> Current condition of the case (Pending, Running, Closed).</li>
    </ul>

    <div class="bg-gray-50 border rounded-lg p-4">
        <h3 class="text-lg font-semibold mb-2">Example Case Entry</h3>
        <table class="w-full text-sm text-left border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2">Client Name</th>
                    <th class="border px-3 py-2">Case Number</th>
                    <th class="border px-3 py-2">Case Type</th>
                    <th class="border px-3 py-2">Court Name</th>
                    <th class="border px-3 py-2">Advocate Name</th>
                    <th class="border px-3 py-2">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border px-3 py-2">Rahim Uddin</td>
                    <td class="border px-3 py-2">CIV-2025-014</td>
                    <td class="border px-3 py-2">Civil</td>
                    <td class="border px-3 py-2">Dhaka Judge Court</td>
                    <td class="border px-3 py-2">John Doe</td>
                    <td class="border px-3 py-2">Pending</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<h1 class="text-2xl font-bold mb-6">Add New Case</h1>

<!-- ADD CASE FORM -->
<div class="bg-white shadow rounded-xl p-6 mb-8">
    <?php if($message) echo $message; ?>

    <form class="grid grid-cols-1 md:grid-cols-3 gap-4" method="POST">

        <div>
            <label class="block text-sm font-medium mb-1">Client Name</label>
            <input type="text" name="clientName" class="border rounded-lg px-4 py-2 w-full" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Case Number</label>
            <input type="text" name="caseNo" class="border rounded-lg px-4 py-2 w-full" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Case Type</label>
            <input type="text" name="caseType" class="border rounded-lg px-4 py-2 w-full" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Court Name</label>
            <input type="text" name="courtName" class="border rounded-lg px-4 py-2 w-full" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Advocate Name</label>
            <input type="text" name="advocate_name" class="border rounded-lg px-4 py-2 w-full" placeholder="Enter advocate name" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Status</label>
            <input type="text" name="status" class="border rounded-lg px-4 py-2 w-full" required>
        </div>

        <div class="flex items-end">
            <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 w-full">
                Add Case
            </button>
        </div>

    </form>
</div>

<!-- CASES TABLE -->
<div class="bg-white shadow rounded-xl p-6">
    <h2 class="text-lg font-semibold mb-4">All Cases</h2>
    <table class="w-full text-sm border-collapse">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-3 text-left">ID</th>
                <th class="p-3 text-left">Client</th>
                <th class="p-3 text-left">Case No</th>
                <th class="p-3 text-left">Type</th>
                <th class="p-3 text-left">Court</th>
                <th class="p-3 text-left">Advocate</th>
                <th class="p-3 text-left">Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT * FROM adminDashboard ORDER BY id DESC";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0):
            while ($case = mysqli_fetch_assoc($result)):
        ?>
            <tr class="border-t">
                <td class="p-3"><?php echo $case['id']; ?></td>
                <td class="p-3"><?php echo htmlspecialchars($case['clientName']); ?></td>
                <td class="p-3"><?php echo $case['caseNo']; ?></td>
                <td class="p-3"><?php echo $case['caseType']; ?></td>
                <td class="p-3"><?php echo $case['courtName']; ?></td>
                <td class="p-3"><?php echo $case['advocateName']; ?></td>
                <td class="p-3">
                    <?php if ($case['status'] === 'Open'): ?>
                        <span class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Open</span>
                    <?php else: ?>
                        <span class="bg-red-500 text-white px-2 py-1 rounded text-xs"><?php echo $case['status']; ?></span>
                    <?php endif; ?>
                </td>
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

<!-- FOOTER -->
<footer class="bg-white border-t mt-16">
    <div class="max-w-7xl mx-auto px-6 py-4 text-center text-sm text-gray-500">
        © 2026 LawyerCasePro. All rights reserved.
    </div>
</footer>

</body>
</html>

<?php mysqli_close($conn); ?>


