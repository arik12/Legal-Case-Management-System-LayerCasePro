


<?php
session_start();


// ================= AUTH GUARD =================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Advocate') {
    header("Location: ./Login.php");
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "lawyercasepro");
if (!$conn) die("Database Connection Failed: " . mysqli_connect_error());

$message = '';
$messageType = 'error';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_advocate'])) {
    $clientName    = $_POST['clientName'];
    $caseNo        = $_POST['caseNo'];
    $caseType      = $_POST['caseType'];
    $courtName     = $_POST['courtName'];
    $oppositeParty = $_POST['oppositeParty'];

    if ($clientName && $caseNo && $caseType && $courtName) {

        // Check if case exists in advocateDashboard
        $check = mysqli_query($conn, "SELECT * FROM advocateDashboard WHERE caseNo='$caseNo'");
        if (mysqli_num_rows($check) > 0) {
            $message = "Case number already exists!";
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

            $message = "Case added successfully!";
            $messageType = 'success';
        }

    } else {
        $message = "All fields are required!";
    }
}

// ================= SEARCH CASES =================
$searchTerm = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_case'])) {
    $searchTerm = $_POST['search_term'];
    $searchTermEscaped = mysqli_real_escape_string($conn, $searchTerm);
    $sql = "SELECT * FROM advocateDashboard 
            WHERE caseNo LIKE '%$searchTermEscaped%' 
            OR clientName LIKE '%$searchTermEscaped%' 
            OR oppositeParty LIKE '%$searchTermEscaped%'
            ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM advocateDashboard ORDER BY id DESC";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Advocate Dashboard | LawyerCasePro</title>
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
            <span class="text-gray-600">Advocate Panel</span>
            <span class="flex items-center gap-2 font-semibold text-blue-700">
                <i class="fas fa-user-circle text-lg"></i>
                <?= htmlspecialchars($_SESSION['fullName'] ?? 'Guest'); ?>
            </span>
            <a href="./Logout.php" class="text-red-500 font-semibold">Logout</a>
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
            <button type="submit" name="add_advocate" class="bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 w-full">
                Add Advocate
            </button>
        </div>

    </form>
</div>

<!-- ================= SEARCH + DOWNLOAD ================= -->
<div class="bg-white shadow rounded-xl p-6 mb-4">
    <form method="POST" class="flex flex-col md:flex-row gap-3 items-center">
        <input type="text" name="search_term" placeholder="Search by Case No, Client, or Opposite Party"
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
                <td class="p-3"><?php echo !empty($case['nextHearing']) ? date('d M Y', strtotime($case['nextHearing'])) : '—'; ?></td>
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
        doc.text('Advocate Dashboard - Case Records Report', pageWidth / 2, 22, { align: 'center' });

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
                // Status column color (last column, index 6: Case No, Type, Client, Opposite Party, Court, Next Hearing, Status)
                if (data.section === 'body' && data.column.index === 6) {
                    const value = data.cell.raw;
                    if (value === 'Open') {
                        data.cell.styles.fillColor = [59, 130, 246];
                        data.cell.styles.textColor = 255;
                    } else if (value && value !== '—') {
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

        doc.save('LawyerCasePro_AdvocateDashboard_' + now.toISOString().slice(0, 10) + '.pdf');
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







