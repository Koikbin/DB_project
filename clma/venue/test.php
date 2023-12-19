<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Date Selection</title>
    <script>
        function updateDays() {
            var year = document.getElementById('year').value;
            var month = document.getElementById('month').value;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_days.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (this.status == 200) {
                    document.getElementById('day').innerHTML = this.responseText;
                }
            };
            xhr.send('year=' + year + '&month=' + month);
        }
    </script>
</head>
<body>
    <form action="#" method="post">
        <label for="year">20022年：</label>
        <select id="year" name="year" onchange="updateDays()">
            <?php
            $currentYear = date("Y");
            for ($year = $currentYear; $year <= $currentYear + 1; $year++) {
                echo "<option value='$year'>$year</option>";
            }
            ?>
        </select>

        <label for="month">月份：</label>
        <select id="month" name="month" onchange="updateDays()">
            <?php
            for ($month = 1; $month <= 12; $month++) {
                $monthPadded = str_pad($month, 2, "0", STR_PAD_LEFT);
                echo "<option value='$monthPadded'>$monthPadded</option>";
            }
            ?>
        </select>

        <label for="day">日期：</label>
        <select id="day" name="day">
            <!-- Day options will be populated by JavaScript/AJAX -->
        </select>

        <input type="submit" value="Search">
    </form>
</body>
</html>
