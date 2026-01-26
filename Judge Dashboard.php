
<?php
session_start();

// ================= DATABASE CONNECTION =================
$conn = mysqli_connect("localhost", "root", "", "lawyercasepro");
if (!$conn) die("Database Connection Failed: " . mysqli_connect_error());

// ================= UPDATE CASE =================
$message = '';
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
        $message = "<p style='color:green;'>Case ID $id updated successfully!</p>";
    } else {
        $message = "<p style='color:red;'>Error updating case: " . mysqli_error($conn) . "</p>";
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
            <a href="./LawyerCaseManagement_Landing Page.html" class="text-red-500 font-semibold">Logout</a>
        </div>
    </div>
</header>

<main class="pt-28 max-w-7xl mx-auto px-6">

<section class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-xl font-semibold mb-3">Judge Dashboard Overview</h2>
    <p class="text-gray-600 mb-4">
        View case histories, update case fields directly, and monitor case progress.
    </p>
    <?= $message; ?>

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
    </form>
</section>

<!-- ================= CASE TABLE ================= -->
<section class="bg-white shadow rounded-xl p-6 mb-10">
    <h2 class="text-lg font-semibold mb-4">All Cases</h2>
    <table class="w-full border text-sm">
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

</body>
</html>

<?php mysqli_close($conn); ?>
