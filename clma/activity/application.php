<!DOCTYPE html>
<html>
<head>
<title>活動申請</title>

<!-- <link rel="icon" href="icon_path" type="./img/icon.png"> -->
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

<link rel="stylesheet" href="style_activity.css?v=<?php echo time(); ?>">
<!-- <nav>
</nav> -->
<div class="container">
    <?php
        session_start();
    ?>
    <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Store form data in session variables
            $_SESSION['formData'] = array_merge($_SESSION['formData'] ?? [], $_POST);
        }
        if (!isset($_SESSION['request'])) {
            $_SESSION['request'] = array();
        }
    ?>

    <?php
        $host = 'localhost';
        $port = 5432; // remember to replace your own connection port
        $dbname = 'project'; // remember to replace your own database name 
        $user = 'postgres'; // remember to replace your own username 
        $password = trim(file_get_contents('db_password.txt')); // remember to replace your own password 

        $pdo = null;
        try {
            $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Database connection failed: " . $e->getMessage();
        }
    ?>
    
    <h1>活動申請</h1>
    <?php
        
        if (isset($_SESSION['ID'])) {
            $ID = $_SESSION['ID'];
            // Use $yourVariable as needed
        }
        echo $ID;
        $_SESSION['ID'] = $ID;
    ?>
    <h3>基本活動資訊</h3>
    <form action="#" method="post">
        <label for="message">輸入欲申請活動名稱：</label>
            <input type="text" id="input" name="title" value="<?php echo $_SESSION['formData']['title'] ?? ''; ?>">

        <p>類型：
        <select name="type" id="type" value="<?php echo $_SESSION['formData']['type'] ?? ''; ?>">
            <?php
                $selectedType = $_POST['type'] ?? '';
                $type_set = array("社內", "一般");
                foreach ($type_set as $option) {
                    $selected = ($option == $selectedType) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($option) . "' $selected>" . htmlspecialchars($option) . "</option>";
                }
            ?>
            
        </select></p>
        <label for="message">參與人數：</label>
            <input type="text" id="input" name="participant" value="<?php echo $_SESSION['formData']['participant'] ?? ''; ?>"><br>
        <label for="message">預算：</label>
            <input type="text" id="input" name="budget" value="<?php echo $_SESSION['formData']['budget'] ?? ''; ?>"><br>
        <label for="message">企劃書：</label>
            <input type="text" id="input" name="proposal" value="<?php echo $_SESSION['formData']['proposal'] ?? ''; ?>"><br>
        <label for="message">負責人學號：</label>
            <input type="text" id="input" name="studentInChargeId" value="<?php echo $_SESSION['formData']['studentInChargeId'] ?? ''; ?>"><br>
        <label for="message"></label>
        <p>狀態：<select id="status" name="status">
            <?php
            $selectedStatus = $_POST['status'] ?? '';
            $status = array("可借用", "已借用");
            foreach($status as $option){
                echo "<option value='$option'>$option</option>";
            }
            ?>
        </select>
        <label for="message"></label>
        
        <label for="message"></label>
        <?php $budget = $_POST["budget"]; ?>
        <label for="message"></label>
        
        <button type="submit" style="margin-left: 5em">查詢</button></p> 
    </form>
    <h3>場地登記</h3>
    <form action="#" method="post">
        <p>建築名稱：
        <select name="building" id="input">
            <?php
                $selectedBuilding = $_POST['building'] ?? '';
                try {
                    $stmt = $pdo->query("SELECT building_name FROM venue");
                    // $venue = fetchALL(PDO::FETCH_ASSOC);
                    $venue_set = array();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        if (in_array($row['building_name'], $venue_set))
                        {
                            continue;
                        }
                        else{
                            $venue_set[] = $row['building_name'];
                        }
                        $selected = ($row['building_name'] == $selectedBuilding) ? 'selected' : '';
                        // echo "<option value='" . htmlspecialchars($row['building_name']) . "' $selected>" . htmlspecialchars($row['building_name']) . "</option>";
                        echo "<option value='" . $row['building_name'] . "' $selected>" .$row['building_name']. "</option>";
                    }
                    
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            ?>
        </select><button type="submit" style="margin-left: 5em">確認建築</button></p> 
        <p>空間名稱：
        <select name="room" id="input">
            <?php
                $selectedRoom = $_POST['room'] ?? '';
                try {
                    $stmt = $pdo->prepare("SELECT room_name FROM venue where building_name = :building");
                    $stmt->bindParam(':building', $selectedBuilding);
                    $stmt->execute();
                    // $venue = fetchALL(PDO::FETCH_ASSOC);
                    $venue_set = array();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        if (in_array($row['room_name'], $venue_set))
                        {
                            continue;// $venue_set[] = $row['building_name'];
                        }
                        else{
                            $venue_set[] = $row['room_name'];
                        } 
                        $selected = ($row['room_name'] == $selectedRoom) ? 'selected' : '';
                        echo "<option value='" . $row['room_name'] . "' $selected>" .$row['room_name']. "</option>";
                    }
                    
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            ?>
        </select><button type="submit" style="margin-left: 5em">查詢</button></p>
        <?php
            echo "請選擇日期：<br>";
        ?>
        
        <br><label for="year">年份：</label>
        <?php 
            $selectedYear;
            $selectedMonth;
            $selectedDay;
        ?>
        <select id="year" name="year" onchange="updateDays()" value="<?php echo $_SESSION['formData']['year'] ?? ''; ?>">
            <?php
            $currentYear = date("Y");
            $selectedYear = $_POST['year'] ?? '';
            for ($year = $currentYear; $year <= $currentYear + 1; $year++) {
                $yearPadded = str_pad($year, 2, "0", STR_PAD_LEFT);
                
                echo "<option value='$year'>$year</option>";
            }
            ?>
        </select>
        <label for="month">月份：</label>
        <select id="month" name="month" onchange="updateDays()" value="<?php echo $_SESSION['formData']['month'] ?? ''; ?>">
            <?php
            $selectedMonth = $_POST['month'] ?? '';
            for ($month = 1; $month <= 12; $month++) {
                $monthPadded = str_pad($month, 2, "0", STR_PAD_LEFT);
                // $selectedMonth = $monthPadded;
                echo "<option value='$monthPadded'>$monthPadded</option>";
            }
            ?>
        </select>
        <label for="day">日期：</label>
        
        <select id="day" name="day" value="<?php echo $_SESSION['formData']['day'] ?? ''; ?>">
            <?php
                $selectedDay = $_POST['day'];
            ?>
            <!-- Day options will be populated by JavaScript/AJAX -->
        </select>
        
        <!-- <input type="submit" value="確認"> -->
        <label for="message"></label>
        <p>開始時間：
        <select id="start_time" name="start_time" value="<?php echo $_SESSION['formData']['start_time'] ?? ''; ?>">
            <?php
            $selectedStartTime = $_POST['start_time'] ?? '';
            $_SESSION['formData']['start_time_int'] = $selectedStartTime;
            for ($slot = 7; $slot <= 21; $slot++) {
                $slotPadded = str_pad($slot, 2, "0", STR_PAD_LEFT);
                
                echo "<option value='$slot'>$slotPadded</option>";
            }
            $selectedStartTime = str_pad($selectedStartTime, 2, "0", STR_PAD_LEFT);
            $StartTimeForShown = $selectedStartTime . ":00:00";
            $selectedStartTime = str_pad($selectedStartTime, 6, "0", STR_PAD_RIGHT);
            $_SESSION['formData']['start_time'] = $selectedStartTime;
            ?>
        </select>
            點鐘
        </p>

        <label for="message"></label>
        <p>結束時間：
        <select id="end_time" name="end_time" value="<?php echo $_SESSION['formData']['end_time'] ?? ''; ?>">
            <?php
            $selectedEndTime = $_POST['end_time'] ?? '';
            for ($slot = 7; $slot <= 21; $slot++) {
                $slotPadded = str_pad($slot, 2, "0", STR_PAD_LEFT);
                
                echo "<option value='$slot'>$slotPadded</option>";
            }
            // $selectedEndTime = str_pad($selectedEndTime, 2, "0", STR_PAD_LEFT);
            $slotForShown = $selectedSlot . ":00:00";
            $interval = $selectedEndTime - $_SESSION['formData']['start_time_int'];
            // $selectedEndTime = str_pad($selectedEndTime, 6, "0", STR_PAD_RIGHT);
            $_SESSION['formData']['end_time'] = $selectedEndTime;
            $_SESSION['formData']['interval'] = $interval;
            ?>
        </select>
            點鐘
        </p>
        
        <label for="message"></label>
        <p>設備：<select id="equipment" name="equipment" value="<?php echo $_SESSION['formData']['equipment'] ?? ''; ?>">
            <?php
                $selectedRoom = $_POST['room'] ?? '';
                $selectedBuilding =  $_SESSION['formData']['building'];
                try {
                    $stmt = $pdo->prepare("SELECT item FROM equipment where building_name = :building");
                    $stmt->bindParam(':building', $selectedBuilding);
                    $stmt->execute();
                    $set = array();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        if (in_array($row['item'], $set))
                        {
                            continue;// $venue_set[] = $row['building_name'];
                        }
                        else{
                            $set[] = $row['item'];
                        } 
                        $selected = ($row['item'] == $selectedRoom) ? 'selected' : '';
                        echo "<option value='" . $row['item'] . "' $selected>" .$row['item']. "</option>";
                    }
                    
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
            ?>
        </select>
        <button type="submit" style="margin-left: 5em" name="request">確認</button></p> 
        <!-- <button type="submit" name="enter" style="margin-left: 5em">確認</button></p>  -->
    </form>

    <!-- 登記場地 -->
    <?php

        function book_time_slot($activitySn, $buildingName, $roomName, $date, $startTime, $endTime)
        {
            global $pdo;
            // echo "in book time <br>";
            // echo "date: ". $date . "<br>";
            // echo "sta: " .$startTime . "end: " . $endTime . "<br>";
            try {
                // Start a transaction
                // $pdo->beginTransaction();

                // Acquire a lock on the tables to prevent concurrent modifications
                // Acquire a lock on the rows in use_time_slot table
                $query = "
                    WITH tsh AS (
                        SELECT to_char(:startTime::TIME + (INTERVAL '1 hour' * generate_series(0, :endTime - 1)), 'HH24:MI:SS') AS slt
                    )
                    SELECT uts.* 
                    FROM use_time_slot as uts
                    JOIN tsh ON to_char(uts.slot, 'HH24:MI:SS') = tsh.slt
                    WHERE uts.building_name = :buildingName
                    AND uts.room_name = :roomName 
                    AND uts.\"date\" = :date
                    FOR UPDATE;

                    
                ";
                // SELECT * FROM use_time_slot 
                    // WHERE building_name = :buildingName
                    // AND room_name = :roomName 
                    // AND \"date\" = :date 
                    // AND slot in tsh.slt
                    // FOR UPDATE;

                // Prepare the statement
                $statement = $pdo->prepare($query);
                
                $statement->bindParam(':buildingName', $buildingName, PDO::PARAM_STR);
                $statement->bindParam(':roomName', $roomName, PDO::PARAM_STR);
                $statement->bindParam(':date', $date, PDO::PARAM_STR);
                $statement->bindParam(':startTime', $startTime, PDO::PARAM_STR);
                $statement->bindParam(':endTime', $endTime, PDO::PARAM_INT);
                
                $statement->execute();
                // Acquire a lock on the rows in time_slot table
                $query = "
                    WITH tsh AS (
                        SELECT to_char(:startTime::TIME + (INTERVAL '1 hour' * generate_series(0, :endTime - 1)), 'HH24:MI:SS') AS slt
                    )
                    SELECT uts.* 
                    FROM time_slot as uts
                    JOIN tsh ON to_char(uts.slot, 'HH24:MI:SS') = tsh.slt
                    WHERE uts.building_name = :buildingName
                    AND uts.room_name = :roomName 
                    AND uts.\"date\" = :date
                    FOR UPDATE;
                ";
                // Prepare the statement
                $statementInsert = $pdo->prepare($query);
                
                $statementInsert->bindParam(':buildingName', $buildingName, PDO::PARAM_STR);
                $statementInsert->bindParam(':roomName', $roomName, PDO::PARAM_STR);
                $statementInsert->bindParam(':date', $date, PDO::PARAM_STR);
                $statementInsert->bindParam(':startTime', $startTime, PDO::PARAM_STR);
                $statementInsert->bindParam(':endTime', $endTime, PDO::PARAM_INT);
                
                $statement->execute();
                


                // Call check_for_venue to check the venue availability
                // echo "entering check for venue<br>";
                
                $venueAvailable = check_for_venue($buildingName, $roomName, $date, $startTime, $endTime);

                // If the venue is not available, rollback and return false
                if (!$venueAvailable) {
                    
                    $pdo->rollBack();
                    return false;
                }
                else{
                    // echo "venue available<br>";
                }

                // Insert rows into use_time_slot
                $queryInsert = "
                    WITH tsh AS (
                        SELECT :startTime::TIME + (INTERVAL '1 hour' * generate_series(0, :endTime - 1)) AS slt
                    )
                    INSERT INTO use_time_slot
                        (activity_sn, building_name, room_name, \"date\", slot)
                    SELECT
                        :activitySn,
                        :buildingName,
                        :roomName,
                        :date,
                        tsh.slt
                    FROM tsh 
                ";
                
                $queryCheck = "
                    WITH tsh AS (
                        SELECT :startTime::TIME + (INTERVAL '1 hour' * generate_series(0, :endTime - 1)) AS slt
                    )
                    SELECT ts.status, ts.\"date\" || ' ' || ts.slot AS datetime
                    FROM time_slot AS ts
                    JOIN tsh ON to_char(ts.slot, 'HH24MISS') = to_char(tsh.slt, 'HH24MISS')
                    WHERE ts.building_name = :buildingName
                    AND ts.room_name = :roomName
                    AND ts.\"date\" = :date
                    FOR UPDATE NOWAIT;
                ";

                // Prepare and execute the insert query with user input
                $statementInsert = $pdo->prepare($queryInsert);
                $statementInsert->bindParam(':activitySn', $activitySn, PDO::PARAM_INT);
                $statementInsert->bindParam(':buildingName', $buildingName, PDO::PARAM_STR);
                $statementInsert->bindParam(':roomName', $roomName, PDO::PARAM_STR);
                $statementInsert->bindParam(':date', $date);
                $statementInsert->bindParam(':startTime', $startTime, PDO::PARAM_STR);
                $statementInsert->bindParam(':endTime', $endTime);
                $statementInsert->execute();

                // Commit the transaction
                

                // Call venue_status_update to update the venue status
                // echo "aabc:". $buildingName . $roomName . $date . $startTime .$endTime ;
                
                $val = [];
                $startTimeInt = $_SESSION['formData']['start_time_int'];
                for($i = $startTimeInt; $i < $startTimeInt + $endTime; $i++){
                    $val[] = sprintf('%02d0000', $i); 
                }
                foreach($val as $v){
                    if(venue_status_update($buildingName, $roomName, $date, $v, '已借用')){
                        // echo "venue updated<br>";
        
                    }
                    else{
                        // echo "venue not updated<br>";
                    }
                }
                
                // $pdo->commit();
                // Close the database connection
                // $pdo = null;

                return true;
            } catch (PDOException $e) {
                // An error occurred, rollback the transaction
                if ($pdo) {
                    $pdo->rollBack();
                }

                echo "Error: " . $e->getMessage();

                // Close the database connection
                $pdo = null;

                return false;
            }
        }

    ?>

    <!-- 確認場地可用 -->
    <?php
            
        function check_for_venue($buildingName, $roomName, $date, $startTime, $endTime)
        {
            global $pdo;
            try {
                // Start a transaction
                // $pdo->beginTransaction();

                // Check if all statuses are '可借用' and every date + slot is earlier than the current time
                $queryCheck = "
                    WITH tsh AS (
                        SELECT :startTime::TIME + (INTERVAL '1 hour' * generate_series(0, :endTime - 1)) AS slt
                    )
                    SELECT ts.status, ts.\"date\" || ' ' || ts.slot AS datetime
                    FROM time_slot AS ts
                    JOIN tsh ON to_char(ts.slot, 'HH24MISS') = to_char(tsh.slt, 'HH24MISS')
                    WHERE ts.building_name = :buildingName
                    AND ts.room_name = :roomName
                    AND ts.\"date\" = :date
                    FOR UPDATE NOWAIT;
                ";

                // Prepare and execute the check query with user input
                $statementCheck = $pdo->prepare($queryCheck);
                $statementCheck->bindParam(':buildingName', $buildingName);//, PDO::PARAM_STR);
                $statementCheck->bindParam(':roomName', $roomName);//, PDO::PARAM_STR);
                $statementCheck->bindParam(':date', $date);
                $statementCheck->bindParam(':startTime', $startTime);//, PDO::PARAM_STR);
                $statementCheck->bindParam(':endTime', $endTime);//, PDO::PARAM_INT);
                $statementCheck->execute();
                // echo "：" . $buildingName. "<br>". $roomName. $startTime . $endTime;
                // echo "日期：" . $date . "<br>";
                // Fetch the rows for checking
                $rows = $statementCheck->fetchAll(PDO::FETCH_ASSOC);
                // print_r($rows);
                
                // Check if all statuses are '可借用' and every date + slot is earlier than the current time
                foreach ($rows as $row) {
                    if ($row['status'] !== '可借用' || strtotime($date) <= time()) {
                        
                        echo "此時段不可借用" . "<br>";
                        
                        
                        // If any condition is not met, rollback and return false
                        $pdo->rollBack();
                        return false;
                    }
                    else{
                        // echo "沒有擋<br>";
                    }
                }

                // Commit the transaction
                // $pdo->commit();

                return true;
            } catch (PDOException $e) {
                // An error occurred, rollback the transaction
                $pdo->rollBack();
                echo "Error: " . $e->getMessage();
                return false;
            }
        }

    
    ?>

    <!-- 登記器材 -->
    <?php

        function book_equipment($activitySn, $buildingName, $roomName, $date, $startTime, $endTime, $equipmentItem)
        {
            global $pdo;
            try {
                
                // Start a transaction
                // $pdo->beginTransaction();

                // Insert rows into use_equipment in a loop
                $queryInsert = "
                    INSERT INTO use_equipment
                        (activity_sn, building_name, room_name, \"date\", slot, item)
                    VALUES
                        (:activitySn, :buildingName, :roomName, :date, :slot, :equipmentItem)
                ";

                // Prepare and execute the insert query with user input in a loop
                $statementInsert = $pdo->prepare($queryInsert);

                // Loop through the time range and insert rows for each slot
                $currentSlot = $startTime;
                while (strtotime($currentSlot) < strtotime($endTime)) {
                    $statementInsert->bindParam(':activitySn', $activitySn, PDO::PARAM_INT);
                    $statementInsert->bindParam(':buildingName', $buildingName, PDO::PARAM_STR);
                    $statementInsert->bindParam(':roomName', $roomName, PDO::PARAM_STR);
                    $statementInsert->bindParam(':date', $date, PDO::PARAM_STR);
                    $statementInsert->bindParam(':slot', $currentSlot, PDO::PARAM_STR);
                    $statementInsert->bindParam(':equipmentItem', $equipmentItem, PDO::PARAM_STR);
                    $statementInsert->execute();

                    // Increment current slot by 1 hour
                    $currentSlot = date('H:i:s', strtotime($currentSlot) + 3600);
                }

                // Commit the transaction
                // $pdo->commit();

                // Close the database connection
                // $pdo = null;
                // 成功登記器材
                return true;
            } catch (PDOException $e) {
                // An error occurred, rollback the transaction
                if ($pdo) {
                    $pdo->rollBack();
                }

                echo "Error: " . $e->getMessage();

                // Close the database connection
                $pdo = null;

                return false;
            }
        }

    ?>

    <!-- 計算費用 -->
    <?php

        function fee_calculation($activitySn) {
            global $pdo;
            try {
                // echo "費用.活動編號： " . $activitySn . "<br>";
                // $pdo->beginTransaction();

                // Your SQL query with parameterized input and FOR UPDATE clause
                $query = "
                    WITH venue_fee AS (
                        SELECT COALESCE(SUM(vn.fee), 0) AS fee
                        FROM use_time_slot AS uts
                        JOIN venue AS vn ON uts.building_name = vn.building_name
                            AND uts.room_name = vn.room_name
                        WHERE uts.activity_sn = :activitySn
                    ),
                    equipment_fee AS (
                        SELECT COALESCE(SUM(eqm.fee), 0) AS fee
                        FROM use_equipment AS ue 
                        JOIN equipment AS eqm ON ue.building_name = eqm.building_name
                            AND ue.room_name = eqm.room_name
                            AND ue.item = eqm.item
                        WHERE ue.activity_sn = :activitySn
                    )
                    SELECT (venue_fee.fee + equipment_fee.fee) AS total_fee
                    FROM venue_fee, equipment_fee;
                ";

                // Prepare and execute the query with user input
                $statement = $pdo->prepare($query);
                $statement->bindParam(':activitySn', $activitySn, PDO::PARAM_INT);
                $statement->execute();

                // Fetch the results
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                // Commit the transaction
                // $pdo->commit();

                // Close the database connection
                // $pdo = null;
                echo "finished fee calculation<br>";
                return $results;
            } catch (PDOException $e) {
                // An error occurred, rollback the transaction
                if ($pdo) {
                    $pdo->rollBack();
                }

                echo "Error: " . $e->getMessage();

                // Close the database connection
                $pdo = null;

                return false;
            }
        }
    ?>
    <!-- 更新場地狀態 -->
    <?php

        function venue_status_update($buildingName, $roomName, $date, $slot, $newStatus) {
            global $pdo;
            try {
                // echo "upd: " . $buildingName .  $roomName .$date . $slot .$newStatus ;
                // $pdo->beginTransaction();

                // Your SQL update statement with parameterized input and FOR UPDATE clause
                $query = "
                    UPDATE time_slot
                    SET status = :newStatus
                    WHERE building_name = :buildingName
                        AND room_name = :roomName
                        AND date = :date
                        AND slot = :slot
                    RETURNING *;  -- Optional: returns the updated row for further processing
                ";

                // Prepare and execute the query with user input
                $statement = $pdo->prepare($query);
                $statement->bindParam(':buildingName', $buildingName, PDO::PARAM_STR);
                $statement->bindParam(':roomName', $roomName, PDO::PARAM_STR);
                $statement->bindParam(':date', $date, PDO::PARAM_STR);
                $statement->bindParam(':slot', $slot, PDO::PARAM_STR);
                $statement->bindParam(':newStatus', $newStatus, PDO::PARAM_STR);
                $statement->execute();

                // Fetch the updated row if needed
                $updatedRow = $statement->fetch(PDO::FETCH_ASSOC);

                // Commit the transaction
                // $pdo->commit();

                // Close the database connection
                // $pdo = null;
                // echo "<br>morn<br>";
                return true;
            } catch (PDOException $e) {
                // An error occurred, rollback the transaction
                if ($pdo) {
                    $pdo->rollBack();
                }

                echo "Error: " . $e->getMessage();

                // Close the database connection
                $pdo = null;

                return false;
            }
        }

        ?>






    <!-- 更新付款狀態 -->
    <?php

        function activity_payment_status_update($sn, $newPaymentStatus) {
            global $pdo;
            try {
                // echo "get info"
                // $pdo->beginTransaction();

                // Your SQL update statement with parameterized input and FOR UPDATE clause
                $query = "
                    UPDATE activity
                    SET payment_status = :newPaymentStatus
                    WHERE sn = :sn
                    RETURNING *;  -- Optional: returns the updated row for further processing
                ";

                // Prepare and execute the query with user input
                $statement = $pdo->prepare($query);
                $statement->bindParam(':newPaymentStatus', $newPaymentStatus, PDO::PARAM_STR);
                $statement->bindParam(':sn', $sn, PDO::PARAM_INT);
                $statement->execute();

                // Fetch the updated row if needed
                $updatedRow = $statement->fetch(PDO::FETCH_ASSOC);

                // Commit the transaction
                // $pdo->commit();

                // Close the database connection
                // $pdo = null;
                // echo "完成付款更新<br>";
                return $updatedRow;
            } catch (PDOException $e) {
                // An error occurred, rollback the transaction
                if ($pdo) {
                    $pdo->rollBack();
                }

                echo "Error: " . $e->getMessage();

                // Close the database connection
                $pdo = null;

                return false;
            }
        }

    ?>



    <?php
        function apply_for_activity($clubId, $buildingName, $roomName, $date, $semester, $title, $type, $participant, $budget, $proposal, $studentInChargeId, $equipmentInfo, $startTime, $endTime)
        {
            global $pdo;
            
            // echo "<br>get ids: " . "社團：" . $clubId;
            // echo "學期：" . $semester. "活動名稱：". $title. "活動類型：". $type."參與人數：". $participant. "預算：".$budget. "企劃書：". $proposal. "負責人：".$studentInChargeId;
            // echo "開始時間： " . $startTime . " 結束時間：" . $endTime . "<br>";
            // print
            try {
                // Start a transaction
                $pdo->beginTransaction();

                // Acquire a lock on the tables to prevent concurrent modifications
                $pdo->exec('LOCK TABLE activity IN SHARE MODE');
                
                // Check that participant is a positive integer and budget is a nonnegative integer
                if (!is_numeric($participant) || $participant <= 0 || !is_numeric($budget) || $budget < 0) {
                    // If conditions are not met, rollback and return false
                    $pdo->rollBack();
                    return false;
                }
                // Determine review and payment deadlines based on the type
                $currentTimestamp = date('Y-m-d H:i:s');
                $reviewDeadline = ($type == '社內') ? date('Y-m-d H:i:s', strtotime($currentTimestamp . '+ 7 days')) :
                    date('Y-m-d H:i:s', strtotime($currentTimestamp . '+ 14 days'));
                $paymentDeadline = $reviewDeadline;
                // Insert the activity record
                $queryInsert = "
                    INSERT INTO activity
                        (\"club_ID\", semester, title, \"type\", participant, budget, proposal, \"student_in_charge_ID\",
                        review_deadline, payment_deadline)
                    VALUES
                        (:clubId, :semester, :title, :type, :participant, :budget, :proposal, :studentInChargeId,
                        :reviewDeadline, :paymentDeadline)
                    RETURNING sn;
                ";

                // Prepare and execute the insert query with user input
                $statementInsert = $pdo->prepare($queryInsert);
                $statementInsert->bindParam(':clubId', $clubId, PDO::PARAM_STR);
                $statementInsert->bindParam(':semester', $semester, PDO::PARAM_STR);
                $statementInsert->bindParam(':title', $title, PDO::PARAM_STR);
                $statementInsert->bindParam(':type', $type, PDO::PARAM_STR);
                $statementInsert->bindParam(':participant', $participant, PDO::PARAM_INT);
                $statementInsert->bindParam(':budget', $budget, PDO::PARAM_INT);
                $statementInsert->bindParam(':proposal', $proposal, PDO::PARAM_STR);
                $statementInsert->bindParam(':studentInChargeId', $studentInChargeId, PDO::PARAM_STR);
                $statementInsert->bindParam(':reviewDeadline', $reviewDeadline, PDO::PARAM_STR);
                $statementInsert->bindParam(':paymentDeadline', $paymentDeadline, PDO::PARAM_STR);
                $statementInsert->execute();

                // Fetch the generated serial number (sn)
                $activitySn = $statementInsert->fetchColumn();

                // Check if the serial number is valid
                if (!$activitySn) {
                    // If the serial number is not valid, rollback and return false
                    $pdo->rollBack();
                    return false;
                }
                
                // print_r($equipmentInfo);
                // Iterate over the equipment information and call book_time_slot and book_equipment
                foreach ($equipmentInfo as $info) {
                    // print_r($equipmentInfo);
                    // Extract relevant information
                    // list($buildingName, $roomName, $date, $startTime, $endTime, $equipmentItem) = $info;

                    // Call book_time_slot
        
                    if (!book_time_slot($activitySn, $buildingName, $roomName, $date, $startTime, $endTime)) {
                        // If book_time_slot returns false, rollback and return false
                        
                        $pdo->rollBack();
                        return false;
                    }
                    

                    // Call book_equipment if equipmentItem is not empty
                    if (!empty($equipmentItem) && !book_equipment($pdo, $activitySn, $buildingName, $roomName, $date, $startTime, $endTime, $equipmentItem)) {
                        // If book_equipment returns false, rollback and return false
                        $pdo->rollBack();
                        return false;
                    }
                }
               
                // Call fee_calculation to get the amount
                $amount = fee_calculation($activitySn);

                // echo "計算出費用： " . $amount . "<br>";
                // print_r($amount);
                // Update payment_status using activity_payment_status_update
                $payment_update =  activity_payment_status_update($activitySn, ($amount == 0) ? '已付款' : '未付款');
                // echo "更新付款狀況： <br>";
                // print_r($payment_update);
                

                // Commit the transaction
                if ($pdo->commit()) {
                    echo "Transaction committed successfully.";
                } else {
                    echo "Transaction commit failed.";
                }
                // $pdo->commit();
                // Close the database connection
                echo "成功3<br>";
                $pdo = null;
                echo "成功4<br>";
                
                return true;
            } catch (PDOException $e) {
                // An error occurred, rollback the transaction
                if ($pdo) {
                    $pdo->rollBack();
                }
                echo "Error: " . $e->getMessage();
                // Close the database connection
                $pdo = null;
                return false;
            }
        }
    ?>
    <?php
        if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
            // $club_name_ch = $_POST['club_name_ch'];
            // $semester = '112-1';
            // 
            // $title = $_POST['title'];
            // $type = $_POST['type'];
            // $participant = $_POST['participant'];
            // $budget = $_POST['budget'];
            // $proposal = $_POST['proposal'];
            // $studentInChargeId = $_POST['studentInChargeId'];
            
            // $club_name_ch = $_POST['club_name_ch'];
            // $semester = '112-1';
            // $clubId = $ID;
            // $title = $_SESSION['formData']['title'];
            // $type = $_SESSION['formData']['type'];
            // $participant = $_SESSION['formData']['participant'];
            // $budget = $_SESSION['formData']['budget'];
            // $proposal = $_SESSION['formData']['proposal'];
            // $studentInChargeId = $_SESSION['formData']['studentInChargeId'];
            

            // $selectedStartTime = $_SESSION['formData']['start_time'];
            // $selectedEndTime = $_SESSION['formData']['end_time'];
            // $equipment = $_SESSION['formData']['equipment'];
           
            
            // $building = $row['building_name'];
            // $year = $_SESSION['formData']['year'];
            // $month = $_SESSION['formData']['month'];
            // $day = $_SESSION['formData']['day'];

            // echo "stored title: " . $_SESSION['formData']['title'];
            // echo "負責人： " . $studentInChargeId."<br>";
            // echo "<br>選定建築： " . $selectedBuilding . "<br>";
            // echo "選定日期： " . $year . "年" . $month . "月" . $day . "日" ."<br>";
            // echo "選定空間： " . $selectedRoom . "<br>";
            // echo "開始時間： " . $selectedStartTime ."<br>";
            // echo "結束時間： " . $selectedEndTime ."<br>";
            // echo "設備： " . $equipment ."<br>";
            // $interval =  $_SESSION['formData']['interval'];
            // echo "時長： " . $interval;
            // $date = $year . $month . $day;


            if(isset($_POST['request'])) {
                $club_name_ch = $_POST['club_name_ch'];
                $semester = '112-1';
                $clubId = $ID;
                $title = $_SESSION['formData']['title'];
                $type = $_SESSION['formData']['type'];
                $participant = $_SESSION['formData']['participant'];
                $budget = $_SESSION['formData']['budget'];
                $proposal = $_SESSION['formData']['proposal'];
                $studentInChargeId = $_SESSION['formData']['studentInChargeId'];
                

                $selectedStartTime = $_SESSION['formData']['start_time'];
                $selectedEndTime = $_SESSION['formData']['end_time'];
                $equipment = $_SESSION['formData']['equipment'];
            
                
                $building = $row['building_name'];
                $year = $_SESSION['formData']['year'];
                $month = $_SESSION['formData']['month'];
                $day = $_SESSION['formData']['day'];

                echo "活動名稱: " . $_SESSION['formData']['title']."<br>";
                echo "負責人： " . $studentInChargeId."<br>";
                echo "選定建築： " . $selectedBuilding . "<br>";
                echo "選定日期： " . $year . "年" . $month . "月" . $day . "日" ."<br>";
                echo "選定空間： " . $selectedRoom . "<br>";
                echo "開始時間： " . $selectedStartTime ."<br>";
                echo "結束時間： " . $selectedEndTime ."<br>";
                echo "設備： " . $equipment ."<br>";
                $interval =  $_SESSION['formData']['interval'];
                echo "時長： " . $interval;
                $date = $year . $month . $day;
                $venue_request = [
                    'building' => $selectedBuilding,
                    'room' => $selectedRoom,
                    'date' => $date,
                    'start_time' => $selectedStartTime,
                    'end_time' => $selectedEndTime,
                    'equipment' => $equipment,
                ];
                // echo $_SESSION['venue']['equipment'] ?? '';
                // echo $_SESSION['venue'][] = $row;
    
                $_SESSION['request'][] = $venue_request;
                // print_r($_SESSION['request']);
                $equipmentInfo = $_SESSION['request'];
                $apply = apply_for_activity($clubId, $selectedBuilding, $selectedRoom, $date, $semester, $title, $type, $participant, $budget, $proposal, $studentInChargeId, $equipmentInfo, $selectedStartTime, $interval);
                if($apply){
                    echo "成功新增活動<br>";
                }
                else {
                    echo "新增活動失敗<br>";
                }
            }
            
            // print_r($_SESSION);
            // print_r($_SESSION['request']);
            // session_unset();
            
        
            
            // if (isset($_POST['enter'])) {
            //     $_SESSION['enter'] = true;
                
            //     // $apply = apply_for_activity($clubId, $semester, $title, $type, $participant, $budget, $proposal, $studentInChargeId, $equipmentInfo);
            //     // if($apply){
            //     //     echo "成功新增活動<br>";
            //     // }
            //     // else {
            //     //     echo "新增活動失敗<br>";
            //     // }
                
            // }
            // else{

            if (isset($_SESSION['request']) && is_array($_SESSION['request'])) {
            
                echo "<table>";
                echo "<tr><th>建築</th><th>空間</th><th>日期</th><th>開始時間</th><th>結束時間</th><th>設備</th></tr>";
                foreach ($_SESSION['request'] as $item) {
                    echo '<pre>';
                    // print_r($item);
                    echo '</pre>';
                    echo "<tr><td>" . htmlspecialchars($item['building']) . "</td><td>" . htmlspecialchars($item['room']) . "</td><td>" . htmlspecialchars($item['date']) . "</td><td>" . htmlspecialchars($item['start_time']) . "</td><td>" . htmlspecialchars($item['end_time']) . "</td><td>" . htmlspecialchars($item['equipment']) . "</td></tr>";
                }
                // $venue_request = $_SESSION['venue'];
            } 
            // }
            
            
        }
        
    ?>  


</div>
</body>
</html>
