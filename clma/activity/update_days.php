<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $year = isset($_POST['year']) ? (int)$_POST['year'] : date("Y");
    $month = isset($_POST['month']) ? (int)$_POST['month'] : date("m");


    echo "in update days<br>";
    echo $month. "<br>";
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $dayPadded = str_pad($day, 2, "0", STR_PAD_LEFT);
        echo "<option value='$dayPadded' selected>$dayPadded</option>";
    }
}
?>
