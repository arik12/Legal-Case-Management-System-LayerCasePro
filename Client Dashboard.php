<?php
// Start session (used for authentication)
session_start();

// ================= AUTH GUARD =================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Client') {
    header("Location: ./Login.php");
    exit;
}

// ================= DATABASE CONNECTION =================
// Connect to database using XAMPP (localhost)
$conn = mysqli_connect("localhost", "root", "", "lawyercasepro");

// Stop execution if database connection fails
if (!$conn) die("Database Connection Failed");

// ================= LOAD / SEARCH CASES =================
// Array to store case records
$cases = [];

// Variable to keep search text
$searchTerm = '';

// Load all cases when "Load Cases" button is clicked
if (isset($_POST['load_cases'])) {

    // Fetch all cases from judgeDashboard table
    $result = mysqli_query($conn, "SELECT * FROM judgeDashboard ORDER BY id DESC");

    // Store each row into the cases array
    while ($row = mysqli_fetch_assoc($result)) {
        $cases[] = $row;
    }
}

// Search cases when "Search Cases" button is clicked
if (isset($_POST['search_case'])) {

    // Get search input from user
    $searchTerm = $_POST['search_term'];

    // Prevent SQL injection
    $search = mysqli_real_escape_string($conn, $searchTerm);

    // Search by case number, advocate name, or client name
    $result = mysqli_query($conn, "
        SELECT * FROM judgeDashboard 
        WHERE caseNo LIKE '%$search%' 
        OR advocateName LIKE '%$search%' 
        OR clientName LIKE '%$search%'
        ORDER BY id DESC
    ");

    // Store matching records
    while ($row = mysqli_fetch_assoc($result)) {
        $cases[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Page meta information -->
    <meta charset="UTF-8">
    <title>Client Dashboard | LawyerCasePro</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- PDF Generation Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- ================= HEADER ================= -->
<header class="bg-white shadow fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">

        <!-- Project title -->
        <h1 class="text-xl font-bold text-blue-600">
            <i class="fas fa-scale-balanced"></i> LawyerCasePro
        </h1>

        <!-- Panel info and logout -->
        <div class="flex items-center gap-6">
            <span class="text-gray-600">Client Panel</span>
            <span class="flex items-center gap-2 font-semibold text-blue-700">
                <i class="fas fa-user-circle text-lg"></i>
                <?= htmlspecialchars($_SESSION['fullName'] ?? 'Guest'); ?>
            </span>
            <a href="./Logout.php" class="text-red-500 font-semibold">
                Logout
            </a>
        </div>
    </div>
</header>

<main class="pt-28 max-w-7xl mx-auto px-6 flex-grow w-full">

<!-- ================= INFORMATION SECTION ================= -->
<section class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-xl font-semibold mb-2">Client Case Overview</h2>
    <p class="text-gray-600">
        View your case details entered by the judge.
        All information is read-only.
    </p>
</section>

<!-- ================= LOAD & SEARCH SECTION ================= -->
<section class="bg-white shadow rounded-xl p-6 mb-6">

    <!-- Load all cases button -->
    <form method="POST" class="mb-4">
        <button type="submit" name="load_cases"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Load Cases
        </button>
    </form>

    <!-- Search form -->
    <form method="POST" class="flex flex-col md:flex-row gap-3 items-center">

        <!-- Search input field -->
        <input type="text" name="search_term"
            placeholder="Search by Case No, Advocate, or Client"
            value="<?= htmlspecialchars($searchTerm); ?>"
            class="border rounded-lg px-4 py-2 w-full md:w-1/2">

        <!-- Search button -->
        <button type="submit" name="search_case"
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Search Cases
        </button>

        <!-- Download PDF Button -->
        <button type="button" id="downloadPdfBtn"
            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center gap-2 transition duration-300">
            <i class="fas fa-download"></i> Download
        </button>
    </form>

</section>

<!-- ================= CASE TABLE ================= -->
<section class="bg-white shadow rounded-xl p-6 mb-10" id="printableSection">
    <h2 class="text-lg font-semibold mb-4">Case Details</h2>

    <table class="w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">Case No</th>
                <th class="border px-2 py-1">Case Type</th>
                <th class="border px-2 py-1">Client</th>
                <th class="border px-2 py-1">Advocate</th>
                <th class="border px-2 py-1">Court</th>
                <th class="border px-2 py-1">Hearing Date</th>
                <th class="border px-2 py-1">Status</th>
            </tr>
        </thead>

        <tbody>

            <!-- If cases exist, display them -->
             
            <?php if (!empty($cases)): ?>
                <?php foreach ($cases as $case): ?>
                <tr>
                    <td class="border px-2 py-1"><?= $case['caseNo']; ?></td>
                    <td class="border px-2 py-1"><?= $case['caseType']; ?></td>
                    <td class="border px-2 py-1"><?= $case['clientName']; ?></td>
                    <td class="border px-2 py-1"><?= $case['advocateName']; ?></td>
                    <td class="border px-2 py-1"><?= $case['courtName']; ?></td>

                    <!-- Format hearing date -->
                    <td class="border px-2 py-1">
                        <?= !empty($case['hearingDate']) ? date('d M Y', strtotime($case['hearingDate'])) : '—'; ?>
                    </td>

                    <!-- Case status badge -->
                    <td class="border px-2 py-1">
                        <span class="px-2 py-1 rounded text-xs text-white
                            <?= $case['status']=='Open' ? 'bg-green-600' : 'bg-yellow-500'; ?>">
                            <?= $case['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>

            <!-- If no cases loaded -->
            <?php else: ?>
                <tr>
                    <td colspan="7" class="p-4 text-center text-gray-500">
                        No cases loaded. Click "Load Cases" to view.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</section>

</main>

<!-- ================= FOOTER ================= -->
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

    const bodyRows = table.querySelectorAll('tbody tr');
    if (bodyRows.length === 1 && bodyRows[0].querySelector('td[colspan]')) {
        alert('No content to download. Please load cases first.');
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
        const rows = Array.from(bodyRows).map(tr =>
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
        doc.text('Client Dashboard - Case Records Report', pageWidth / 2, 22, { align: 'center' });

        doc.setFontSize(9);
        doc.setTextColor(153, 153, 153);
        doc.text(`Generated on: ${dateString} at ${timeString}`, pageWidth / 2, 28, { align: 'center' });

        doc.setDrawColor(0, 102, 204);
        doc.setLineWidth(0.5);
        doc.line(15, 32, pageWidth - 15, 32);

        // ===== Table =====
        const statusColIndex = headers.indexOf('Status');

        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 38,
            theme: 'grid',
            headStyles: { fillColor: [0, 102, 204], textColor: 255, fontStyle: 'bold' },
            styles: { fontSize: 9, cellPadding: 3 },
            didParseCell: function(data) {
                if (data.section === 'body' && data.column.index === statusColIndex) {
                    const value = data.cell.raw;
                    if (value === 'Open') {
                        data.cell.styles.fillColor = [22, 163, 74]; // green
                        data.cell.styles.textColor = 255;
                    } else if (value && value !== '—') {
                        data.cell.styles.fillColor = [234, 179, 8]; // yellow
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

        doc.save('LawyerCasePro_ClientDashboard_' + now.toISOString().slice(0, 10) + '.pdf');
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

<?php
// Close database connection
mysqli_close($conn);
?>
