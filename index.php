<?php

session_start();

$ROOT = $_SERVER["DOCUMENT_ROOT"];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatoora Dashboard</title>


    <link rel="stylesheet" href="sccs/styles.css">
    <link rel="stylesheet" href="sccs/common.css">
    <link rel="stylesheet" href="sccs/forms.css">
    <link rel="stylesheet" href="sccs/table.css">
    <link rel="stylesheet" href="sccs/modal.css">
    <link rel="stylesheet" href="sccs/index.css">
    <link rel="stylesheet" href="sccs/onboarding-modal.css">
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
</head>

<body>
    <div class="main">
        <h1>Fatoora API Trigger</h1>


        <div class="buttons">
            <div class="btn btn__large onboarding" onclick="showOnboardingModal()">OnBoard</div>
            <div class="btn btn__large simplified" onclick="showReportingModal()">Simplified Invoice</div>
            <div class="btn btn__large standard" onclick="showClearanceModal()">Standard Invoice</div>
            <div class="btn btn__large bulkReporting" onclick="showBulkReportingModal()">Bulk Reporting</div>
            <div class="btn btn__large bulkClearing" onclick="showBulkClearanceModal()">Bulk Clearance</div>
        </div>
    </div>

    <script src="js/config.js"></script>

    <?php include $ROOT . '/fatoora/modals/onboarding-modal.php'; ?>
    <?php include $ROOT . '/fatoora/modals/clearance-modal.php'; ?>
    <?php include $ROOT . '/fatoora/modals/reporting-modal.php'; ?>
    <?php include $ROOT . '/fatoora/modals/bulk-reporting-modal.php'; ?>
    <?php include $ROOT . '/fatoora/modals/bulk-clearance-modal.php'; ?>

    <script src="js/script.js"></script>
</body>

</html>