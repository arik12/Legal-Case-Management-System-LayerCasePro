<?php
session_start();

// ================= DATABASE CONNECTION =================
$conn = mysqli_connect("localhost", "root", "", "lawyercasepro");
if (!$conn) die("Database Connection Failed");

// ================= LOAD / SEARCH CASES =================
$cases = [];
$searchTerm = '';

if (isset($_POST['load_cases'])) {
    $result = mysqli_query($conn, "SELECT * FROM judgeDashboard ORDER BY id DESC");
    while ($row = mysqli_fetch_assoc($result)) {
        $cases[] = $row;
    }
}

if (isset($_POST['search_case'])) {
    $searchTerm = $_POST['search_term'];
    $search = mysqli_real_escape_string($conn, $searchTerm);

    $result = mysqli_query($conn, "
        SELECT * FROM judgeDashboard 
        WHERE caseNo LIKE '%$search%' 
        OR advocateName LIKE '%$search%' 
        OR clientName LIKE '%$search%'
        ORDER BY id DESC
    ");

    while ($row = mysqli_fetch_assoc($result)) {
        $cases[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Client Dashboard | LawyerCasePro</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- HEADER -->
<header class="bg-white shadow fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold text-blue-600">
            <i class="fas fa-scale-balanced"></i> LawyerCasePro
        </h1>
        <div class="flex items-center gap-6">
            <span class="text-gray-600">Client Panel</span>
            <a href="./Client Dashboard.php" class="text-red-500 font-semibold">Logout</a>
        </div>
    </div>
</header>

<main class="pt-28 max-w-7xl mx-auto px-6 flex-grow w-full">

<!-- INFO -->
<section class="bg-white shadow rounded-xl p-6 mb-6">
    <h2 class="text-xl font-semibold mb-2">Client Case Overview</h2>
    <p class="text-gray-600">
        View your case details entered by the judge.  
        All information is read-only.
    </p>
</section>

<!-- LOAD + SEARCH -->
<section class="bg-white shadow rounded-xl p-6 mb-6">

    <form method="POST" class="mb-4">
        <button type="submit" name="load_cases"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Load Cases
        </button>
    </form>

    <form method="POST" class="flex flex-col md:flex-row gap-3 items-center">
        <input type="text" name="search_term"
            placeholder="Search by Case No, Advocate, or Client"
            value="<?= htmlspecialchars($searchTerm); ?>"
            class="border rounded-lg px-4 py-2 w-full md:w-1/2">

        <button type="submit" name="search_case"
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
            Search Cases
        </button>
    </form>

</section>

<!-- CASE TABLE -->
<section class="bg-white shadow rounded-xl p-6 mb-10">
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
            <?php if (!empty($cases)): ?>
                <?php foreach ($cases as $case): ?>
                <tr>
                    <td class="border px-2 py-1"><?= $case['caseNo']; ?></td>
                    <td class="border px-2 py-1"><?= $case['caseType']; ?></td>
                    <td class="border px-2 py-1"><?= $case['clientName']; ?></td>
                    <td class="border px-2 py-1"><?= $case['advocateName']; ?></td>
                    <td class="border px-2 py-1"><?= $case['courtName']; ?></td>
                    <td class="border px-2 py-1">
                        <?= date('d M Y', strtotime($case['hearingDate'])); ?>
                    </td>
                    <td class="border px-2 py-1">
                        <span class="px-2 py-1 rounded text-xs text-white
                            <?= $case['status']=='Open'?'bg-green-600':'bg-yellow-500'; ?>">
                            <?= $case['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
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

<!-- FOOTER -->
<footer class="bg-white border-t mt-16">
    <div class="max-w-7xl mx-auto px-6 py-4 text-center text-sm text-gray-500">
        © 2026 LawyerCasePro. All rights reserved.
    </div>
</footer>



</body>
</html>

<?php mysqli_close($conn); ?>
