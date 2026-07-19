
<?php
session_start();

// ================= AUTH GUARD =================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Judge') {
    header("Location: ./Login.php");
    exit;
}

// ================= DATABASE CONNECTION =================
$conn = mysqli_connect("localhost", "root", "", "lawyercasepro");
if (!$conn) die("Database Connection Failed: " . mysqli_connect_error());

// ================= UPDATE CASE =================
$message = '';
$messageType = 'error';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_case'])) {
    $id = intval($_POST['id']);
    $advocateName = mysqli_real_escape_string($conn, $_POST['advocateName']);
    $hearingDate = mysqli_real_escape_string($conn, $_POST['hearingDate']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    $updateSQL = "UPDATE judgeDashboard SET 
        advocateName='$advocateName',
        hearingDate='$hearingDate',
        status='$status'
        WHERE id=$id";

    if (mysqli_query($conn, $updateSQL)) {
        $message = "Case ID $id updated successfully!";
        $messageType = 'success';
    } else {
        $message = "Error updating case: " . mysqli_error($conn);
        $messageType = 'error';
    }
}

// ================= LOAD CASES =================
$filteredCases = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['load_cases'])) {
    $result = mysqli_query($conn, "SELECT * FROM judgeDashboard ORDER BY id DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $filteredCases[] = $row;
    }
}

// ================= SEARCH CASES =================
$searchTerm = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search_case'])) {
    $searchTerm = $_POST['search_term'];
    $searchTermEscaped = mysqli_real_escape_string($conn, $searchTerm);
    $result = mysqli_query($conn, "SELECT * FROM judgeDashboard 
        WHERE caseNo LIKE '%$searchTermEscaped%' 
        OR advocateName LIKE '%$searchTermEscaped%' 
        OR clientName LIKE '%$searchTermEscaped%' 
        ORDER BY id DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $filteredCases[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Judge Dashboard | LawyerCasePro</title>
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
            <span class="text-gray-600">Judge Panel</span>
            <span class="flex items-center gap-2 font-semibold text-blue-700">
                <i class="fas fa-user-circle text-lg"></i>
                <?= htmlspecialchars($_SESSION['fullName'] ?? 'Guest'); ?>
            </span>
            <a href="./Logout.php" class="text-red-500 font-semibold">Logout</a>
        </div>
    </div>
</header>

<main class="pt-28 max-w-7xl mx-auto px-6">

<section class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-xl font-semibold mb-3">Judge Dashboard Overview</h2>
    <p class="text-gray-600 mb-4">
        View case histories, update case fields directly, and monitor case progress.
    </p>

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

    <!-- ================= LOAD CASES BUTTON ================= -->
    <form method="POST" class="mb-4">
        <button type="submit" name="load_cases" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Load Cases
        </button>
    </form>

    <!-- ================= SEARCH FORM ================= -->
    <form method="POST" class="flex flex-col md:flex-row gap-3 items-center mb-4">
        <input type="text" name="search_term" placeholder="Search by Case No, Advocate, or Client" 
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
</section>

<!-- ================= CASE TABLE ================= -->
<section class="bg-white shadow rounded-xl p-6 mb-10" id="printableSection">
    <h2 class="text-lg font-semibold mb-4">All Cases</h2>
    <table class="w-full border text-sm" id="caseTable">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1">ID</th>
                <th class="border px-2 py-1">Client Name</th>
                <th class="border px-2 py-1">Case No</th>
                <th class="border px-2 py-1">Case Type</th>
                <th class="border px-2 py-1">Court Name</th>
                <th class="border px-2 py-1">Advocate Name</th>
                <th class="border px-2 py-1">Hearing Date</th>
                <th class="border px-2 py-1">Status</th>
                <th class="border px-2 py-1">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($filteredCases)): ?>
                <?php foreach ($filteredCases as $case): ?>
                <tr>
                    <form method="POST">
                        <td class="border px-2 py-1"><?= $case['id']; ?>
                            <input type="hidden" name="id" value="<?= $case['id']; ?>">
                        </td>
                        <td class="border px-2 py-1"><?= htmlspecialchars($case['clientName']); ?></td>
                        <td class="border px-2 py-1"><?= $case['caseNo']; ?></td>
                        <td class="border px-2 py-1"><?= $case['caseType']; ?></td>
                        <td class="border px-2 py-1"><?= $case['courtName']; ?></td>
                        <td class="border px-2 py-1">
                            <input type="text" name="advocateName" value="<?= htmlspecialchars($case['advocateName']); ?>" class="border rounded px-2 py-1 w-full">
                        </td>
                        <td class="border px-2 py-1">
                            <input type="date" name="hearingDate" value="<?= $case['hearingDate']; ?>" class="border rounded px-2 py-1 w-full">
                        </td>
                        <td class="border px-2 py-1">
                            <select name="status" class="border rounded px-2 py-1 w-full">
                                <option value="Open" <?= $case['status']=='Open'?'selected':''; ?>>Open</option>
                                <option value="On Hearing" <?= $case['status']=='On Hearing'?'selected':''; ?>>On Hearing</option>
                                <option value="Judgement Reserved" <?= $case['status']=='Judgement Reserved'?'selected':''; ?>>Judgement Reserved</option>
                                <option value="Closed" <?= $case['status']=='Closed'?'selected':''; ?>>Closed</option>
                            </select>
                        </td>
                        <td class="border px-2 py-1">
                            <button type="submit" name="update_case" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                Update
                            </button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="p-4 text-center text-gray-500">
                        No cases loaded. Click "Load Cases" to view.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

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
    const table = document.getElementById('caseTable');

    if (!table) {
        alert('No content to download. Please load cases first.');
        return;
    }

    const bodyRows = table.querySelectorAll('tbody tr');
    // If the "no cases loaded" placeholder row is the only row, block download
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

        // ===== Headers (skip the last "Action" column) =====
        const allHeaders = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
        const headers = allHeaders.filter(h => h !== 'Action');

        // ===== Rows: read plain text cells AND input/select values, skip Action column =====
        const rows = Array.from(bodyRows).map(tr => {
            const cells = Array.from(tr.querySelectorAll('td'));
            return cells
                .filter(td => !td.querySelector('button')) // skip the Action cell (has the Update button)
                .map(td => {
                    const input = td.querySelector('input[type="text"], input[type="date"]');
                    const select = td.querySelector('select');
                    if (select) {
                        return select.options[select.selectedIndex]?.text.trim() || '';
                    }
                    if (input) {
                        return input.value.trim();
                    }
                    return td.textContent.trim();
                });
        });

        const now = new Date();
        const dateString = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

        // ===== Title / Header =====
        doc.setFontSize(18);
        doc.setTextColor(0, 102, 204);
        doc.text('LawyerCasePro', pageWidth / 2, 15, { align: 'center' });

        doc.setFontSize(11);
        doc.setTextColor(102, 102, 102);
        doc.text('Judge Dashboard - Case Records Report', pageWidth / 2, 22, { align: 'center' });

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
            styles: { fontSize: 8, cellPadding: 3 },
            didParseCell: function(data) {
                if (data.section === 'body' && data.column.index === statusColIndex) {
                    const value = data.cell.raw;
                    const colorMap = {
                        'Open': [59, 130, 246],
                        'On Hearing': [234, 179, 8],
                        'Judgement Reserved': [168, 85, 247],
                        'Closed': [239, 68, 68]
                    };
                    if (colorMap[value]) {
                        data.cell.styles.fillColor = colorMap[value];
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

        doc.save('LawyerCasePro_JudgeDashboard_' + now.toISOString().slice(0, 10) + '.pdf');
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
