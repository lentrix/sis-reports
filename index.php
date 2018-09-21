<?php
include 'db.php';
include 'functions.php';
$sems = getSems();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css">
    <title>Assessment Report</title>
</head>
<body>
    <div class="container">
        <h1>Student Assessment Report</h1>

        <form method="post">
            <div class="form-group">
                <label for="sem_code">Select Semester</label>
                <select name="sem_code" id="sem_code" class="form-control">
                    <?php foreach($sems as $sem): ?>

                    <option value="<?= $sem->sem_code ?>"><?= $sem->sem ?></option>

                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-lg">Generate Report</button>
            </div>
        </form>

        <?php if(isset($_POST['sem_code'])): ?>

            <?php $rows = generate($_POST['sem_code']); ?>
            <?php unlink('assessment_report.csv'); ?>
            <?php 
                $semName = getSemName($_POST['sem_code']);

                $file = fopen('assessment_report.csv', 'w');

                fputcsv($file, ['Student Assessment for ' . $semName]);
                fputcsv($file, [
                    'ID No.', 'Last Name', 'First Name', 'MI', 'Town', 'Tuition Fees', 'Misc. Fee',
                    'Lab Fees', 'Energy Fee', 'Affiliation Fee', 'Seminar Fee', 'RLE Fee', 
                    'Old Account', 'Others', 'Total', 'Rate', 'Units'
                ]);
            ?>

            <?php 
            foreach($rows as $row):
                $units = computeUnits($row->idnum, $_POST['sem_code'])->units;
                $tuition = $row->rate * $units;
                $fees = getFees($row->idnum, $_POST['sem_code']);

                $misc = getAmount('misc', $fees);
                $rle = getAmount('RLE Fee', $fees);
                $energy = getAmount('Energy Fee', $fees);
                $seminar = getAmount('Seminar Fee', $fees);
                $lab = getAmount('Skills Lab Fee', $fees);
                $affiliation = getAmount('Affiliation Fee', $fees);
                $old = getAmount('old', $fees);

                $total = $tuition + $misc + $rle + $seminar + $lab + $affiliation + $old;

                fputcsv($file,[
                    $row->idnum, $row->lname, $row->fname, $row->mi, $row->addt,
                    $tuition, $misc, $lab, $energy, $affiliation, $seminar, $rle, $old,
                    0,$total, $row->rate, $units
                ]);
            endforeach;

            fclose($file);
            ?>

            <p>
                Report generated. <br>
                <a href="assessment_report.csv" download>Download Report</a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>