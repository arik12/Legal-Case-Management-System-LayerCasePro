<?php
// Start session (used for login/auth)
session_start();

// ================= AUTH GUARD =================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ./Login.php");
    exit;
}

// Database connection (XAMPP: localhost, root, no password)
$conn = mysqli_connect("localhost", "root", "", "lawyercasepro");

// Check database connection
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Message variable for success/error feedback
$message = '';
$messageType = 'error';

// Check if the "Add Case" form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_case'])) {

    // Collect form data
    $clientName   = $_POST['clientName'];
    $caseNo       = $_POST['caseNo'];
    $caseType     = $_POST['caseType'];
    $courtName    = $_POST['courtName'];
    $status       = $_POST['status'];
    $advocateName = $_POST['advocate_name'];

    // Check if all fields are filled
    if ($clientName && $caseNo && $caseType && $courtName && $status && $advocateName) {

        // Check if the case number already exists in adminDashboard
        $check = mysqli_query($conn, "SELECT * FROM adminDashboard WHERE caseNo='$caseNo'");

        if (mysqli_num_rows($check) > 0) {
            // If case number already exists
            $message = "Case number already exists!";
        } else {

            // Insert case data into adminDashboard table
            mysqli_query($conn, "INSERT INTO adminDashboard 
                (clientName, caseNo, caseType, courtName, status, advocateName)
                VALUES ('$clientName','$caseNo','$caseType','$courtName','$status','$advocateName')");

            // Insert the same case data into judgeDashboard table
            mysqli_query($conn, "INSERT INTO judgeDashboard 
                (clientName, caseNo, caseType, courtName, status, advocateName)
                VALUES ('$clientName','$caseNo','$caseType','$courtName','$status','$advocateName')");

            // Success message
            $message = "Case added successfully!";
            $messageType = 'success';
        }

    } else {
        // If any field is empty
        $message = "All fields are required!";
    }
}

// ================= SEARCH CASES =================
$searchTerm = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_case'])) {
    $searchTerm = $_POST['search_term'];
    $searchTermEscaped = mysqli_real_escape_string($conn, $searchTerm);
    $sql = "SELECT * FROM adminDashboard 
            WHERE caseNo LIKE '%$searchTermEscaped%' 
            OR clientName LIKE '%$searchTermEscaped%' 
            OR advocateName LIKE '%$searchTermEscaped%'
            ORDER BY id DESC";
} else {
    // Default: show all cases
    $sql = "SELECT * FROM adminDashboard ORDER BY id DESC";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | LawyerCasePro</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<!-- PDF Generation Libraries -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>


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
            <span class="flex items-center gap-2 font-semibold text-blue-700">
                <i class="fas fa-user-circle text-lg"></i>
                <?= htmlspecialchars($_SESSION['fullName'] ?? 'Guest'); ?>
            </span>
            <a href="./Logout.php" class="text-red-500 font-semibold">Logout</a>
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

    <!-- ================= ALERT BOX ================= -->
    <?php if ($message): ?>
    <div class="mb-4 flex items-start gap-3 rounded-lg border px-4 py-3 text-sm
         <?= $messageType === 'success'
              ? 'bg-green-50 border-green-300 text-green-700'
              : 'bg-red-50 border-red-300 text-red-700'; ?>">
        <i class="fas <?= $messageType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?> mt-0.5"></i>
        <span><?= htmlspecialchars($message); ?></span>
    </div>
    <?php endif; ?>

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
            <button type="submit" name="add_case" class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 w-full">
                Add Case
            </button>
        </div>

    </form>
</div>

<!-- ================= SEARCH + DOWNLOAD ================= -->
<div class="bg-white shadow rounded-xl p-6 mb-4">
    <form method="POST" class="flex flex-col md:flex-row gap-3 items-center">
        <input type="text" name="search_term" placeholder="Search by Case No, Client, or Advocate"
            value="<?= htmlspecialchars($searchTerm); ?>"
            class="border rounded-lg px-4 py-2 w-full md:w-1/2">
        <button type="submit" name="search_case" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Search Cases
        </button>
        <!-- Download PDF Button -->
        <button type="button" id="downloadPdfBtn" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center gap-2 transition duration-300">
            <i class="fas fa-download"></i> Download
        </button>
    </form>
</div>

<!-- CASES TABLE -->
<div class="bg-white shadow rounded-xl p-6" id="printableSection">
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

<!-- ================= PDF DOWNLOAD SCRIPT ================= -->



<script>


document.getElementById('downloadPdfBtn').addEventListener('click', function() {
    const table = document.querySelector('#printableSection table');

    if (!table) {
        alert('No content to download.');
        return;
    }

    const btn = this;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
    btn.disabled = true;

    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();

        // ===== Extract real headers & rows from the actual table =====
        const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
        const rows = Array.from(table.querySelectorAll('tbody tr')).map(tr =>
            Array.from(tr.querySelectorAll('td')).map(td => td.textContent.trim())
        );

        const now = new Date();
        const dateString = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

        // ===== Title / Header =====
        doc.setFontSize(18);
        doc.setTextColor(0, 102, 204);
        doc.text('LawyerCasePro', pageWidth / 2, 15, { align: 'center' });

        doc.setFontSize(11);
        doc.setTextColor(102, 102, 102);
        doc.text('Admin Dashboard - Case Records Report', pageWidth / 2, 22, { align: 'center' });

        doc.setFontSize(9);
        doc.setTextColor(153, 153, 153);
        doc.text(`Generated on: ${dateString} at ${timeString}`, pageWidth / 2, 28, { align: 'center' });

        doc.setDrawColor(0, 102, 204);
        doc.setLineWidth(0.5);
        doc.line(15, 32, pageWidth - 15, 32);

        // ===== Table =====
        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 38,
            theme: 'grid',
            headStyles: { fillColor: [0, 102, 204], textColor: 255, fontStyle: 'bold' },
            styles: { fontSize: 9, cellPadding: 3 },
            didParseCell: function(data) {
                // Status column color (last column, index 6)
                if (data.section === 'body' && data.column.index === 6) {
                    const value = data.cell.raw;
                    if (value === 'Open') {
                        data.cell.styles.fillColor = [59, 130, 246];
                        data.cell.styles.textColor = 255;
                    } else if (value) {
                        data.cell.styles.fillColor = [239, 68, 68];
                        data.cell.styles.textColor = 255;
                    }
                }
            },
            didDrawPage: function() {
                doc.setFontSize(8);
                doc.setTextColor(102, 102, 102);
                doc.text(
                    '© 2026 LawyerCasePro. All rights reserved. | www.lawyercasepro.com',
                    pageWidth / 2,
                    pageHeight - 10,
                    { align: 'center' }
                );
            }
        });

        doc.save('LawyerCasePro_AdminDashboard_' + now.toISOString().slice(0, 10) + '.pdf');
    } catch (error) {
        console.error('PDF generation error:', error);
        alert('Error generating PDF. Please try again.');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
});



</script>









</body>
</html>

<?php mysqli_close($conn); ?>
