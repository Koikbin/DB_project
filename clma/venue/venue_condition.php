<!DOCTYPE html>
<html>
<head>
<title>條件場地查詢</title>
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
<link rel="stylesheet" href="style_venue.css">
<link rel="stylesheet" href="../model.css?v=<?php echo time(); ?>">
<?php include('../nav.php');?>
<div class="container">
    <section>
        
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
        <h1>條件場地查詢</h1> 
        <form action="#" method="post">
            <label for="message">輸入欲查詢之場地資訊</label><br>
            
            
        </form>
        <?php $selectedBuilding1; ?>
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
                                continue;// $venue_set[] = $row['building_name'];
                            }
                            else{
                                $venue_set[] = $row['building_name'];
                            } 
                            $selected = ($row['building_name'] == $selectedBuilding) ? 'selected' : '';
                            echo "<option value='" . $row['building_name'] . "' $selected>" .$row['building_name']. "</option>";
                        }
                        
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                ?>
            </select></p>
            <br><label for="year">年份：</label>
            <?php 
                $selectedYear;
                $selectedMonth;
                $selectedDay;
            ?>
            <select id="year" name="year" onchange="updateDays()">
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
            <select id="month" name="month" onchange="updateDays()">
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
            
            <select id="day" name="day">
                <?php
                    $selectedDay = $_POST['day'];
                ?>
                <!-- Day options will be populated by JavaScript/AJAX -->
            </select>
            <!-- <input type="submit" value="確認"> -->
            <label for="message"></label>
            <p>時段：
            <select id="slot" name="slot">
                <?php
                $selectedSlot = $_POST['slot'] ?? '';
                for ($slot = 7; $slot <= 21; $slot++) {
                    $slotPadded = str_pad($slot, 2, "0", STR_PAD_LEFT);
                    
                    echo "<option value='$slot'>$slotPadded</option>";
                }
                $selectedSlot = str_pad($selectedSlot, 2, "0", STR_PAD_LEFT);
                $slotForShown = $selectedSlot . ":00:00";
                $selectedSlot = str_pad($selectedSlot, 6, "0", STR_PAD_RIGHT);
                ?>
            </select>
                點鐘
            </p>
            
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
            <p>類型：
            <select name="type" id="type">
                <?php
                    $selectedType = $_POST['type'] ?? '';
                    try {
                        $stmt = $pdo->query("SELECT type FROM venue");
                        // $venue = fetchALL(PDO::FETCH_ASSOC);
                        $type_set = array("");
                        $type_set[] = "全部";

                        echo "<option value='全部' $selected>" ."全部". "</option>";
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            if (in_array($row['type'], $type_set))
                            {
                                continue;// $venue_set[] = $row['building_name'];
                            }
                            else{
                                $type_set[] = $row['type'];
                            } 
                            $selected = ($row['type'] == $selectedType) ? 'selected' : '';
                            echo "<option value='" . $row['type'] . "' $selected>" .$row['type']. "</option>";
                        }
                        
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                ?>
            </select></p>
            <label for="message"></label>
            <p>預算：<input type="text" id="input" name="budget"><br></p>
            <?php $budget = $_POST["budget"]; ?>
            <label for="message"></label>
            <p>容納人數：<input type="text" id="capacity" name="capacity">
            <?php $capacity = $_POST["capacity"]; ?>
            <button type="submit" style="margin-left: 5em">查詢</button></p> 
        </form>

        <?php
            function query_under_condition($date, $slot, $fee, $status, $type, $capacity, $building)
            {
                global $pdo;
                if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST") {

                    // echo "get all the info." . $date .$slot . $fee . $status . $type . $capacity . $building;
            
                    if ($type == "全部"){
                        $mergedData = [];
                        // foreach($building_name as $building){
                        $sql_all = "Select v.*, ts.date, ts.slot as start_time, ts.slot + '1 hour'::interval as end_time, ts.status, v.fee
                                    From venue as v
                                    Join time_slot as ts on ts.building_name = v.building_name and ts.room_name = v.room_name
                                    Join equipment as e on e.building_name = v.building_name and e.room_name = v.room_name
                                    Where v.building_name = :building and ts.status = :status and ts.date = :date and ts.slot = :slot and v.fee < :fee
                                    Order by v.building_name, v.room_name, ts.date, ts.slot, e.item;";
                        echo "hello";
                        try {
                            $statement = $pdo->prepare($sql_all);
                            $statement->bindParam(':building', $building);
                            $statement->bindParam(':status', $status);
                            $statement->bindParam(':date', $date);
                            $statement->bindParam(':slot', $slot);
                            $statement->bindParam(':fee', $fee);
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                        }catch (PDOException $e) {
                            echo "Error executing query: " . $e->getMessage();
                        }
                        // echo "hello";
                        // echo $result_all . "<br>";
                        // print_r($result_all);
                        
        
                        if($result){
                            if (count($result) > 0) {
                                foreach ($result as $row) {
                                    $id = $row['id'];
                                    // 如果已经存在相同ID的数据，将新的数据合并到同一列
                                    if (isset($mergedData[$id])) {
                                        foreach ($row as $key => $value) {
                                            // 将每个字段的值合并到一个数组中
                                            $mergedData[$id][$key][] = $value;
                                        }
                                    } else {
                                        // 如果不存在相同ID的数据，直接添加新的数据
                                        $mergedData[$id] = $row;
                                        foreach ($row as $key => $value) {
                                            $mergedData[$id][$key] = [$value];
                                        }
                                    }
                                }
                            }
                        }
                        // }
                    }
                    else{
                        $mergedData = [];
                        
                        try {
                            $sql_type = "Select v.*, ts.date, ts.slot as start_time, ts.slot + '1 hour'::interval as end_time, ts.status, v.fee
                                        From venue as v
                                        Join time_slot as ts on ts.building_name = v.building_name and ts.room_name = v.room_name
                                        Join equipment as e on e.building_name = v.building_name and e.room_name = v.room_name
                                        Where v.building_name = :building and ts.status = :status
                                            and ts.date = :date and ts.slot = :slot and v.type = :type and v.fee < :fee
                                        Order by v.building_name, v.room_name, ts.date, ts.slot, e.item;";
        
                        
                            $statement = $pdo->prepare($sql_type);
                            $statement->bindParam(':building', $building);
                            $statement->bindParam(':status', $status);
                            $statement->bindParam(':date', $date);
                            $statement->bindParam(':slot', $slot);
                            $statement->bindParam(':type', $type);
                            $statement->bindParam(':fee', $fee);
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                        }catch (PDOException $e) {
                            echo "Error executing query: " . $e->getMessage();
                        }
        
                        // echo "hello";
                        // echo $result_type . "<br>";
                        // print_r($result_type);
                        if($result){
                            if (count($result) > 0) {
                                foreach ($result as $row) {
                                    $id = $row['id'];
                                    // 如果已经存在相同ID的数据，将新的数据合并到同一列
                                    if (isset($mergedData[$id])) {
                                        foreach ($row as $key => $value) {
                                            // 将每个字段的值合并到一个数组中
                                            $mergedData[$id][$key][] = $value;
                                        }
                                    } else {
                                        // 如果不存在相同ID的数据，直接添加新的数据
                                        $mergedData[$id] = $row;
                                        foreach ($row as $key => $value) {
                                            $mergedData[$id][$key] = [$value];
                                        }
                                    }
                                    }
                            }
                        }
                        
                        
                    }
                    // print_r($result);
                    
                    if ($result) {
                        echo "<table>";
                        echo "<tr><th>建築名稱</th><th>空間名稱</th><th>樓層</th><th>類型</th><th>空間大小（坪）</th><th>容納人數</th><th>場地管理人編號</th><th>費用</th><th>開始時間</th><th>結束時間</th><th>狀態</th></tr>";
                        foreach ($result as $row) {
                            if($row['floor']  < 0){
                                echo $row['floor'];
                                $row['floor'] = "地下 ". -1*($row['floor']) ." 樓";
                                echo $row['floor'];
                            }
                            // echo $row['ID']. $row['semester'];
                            echo "<tr><td>" . $row['building_name'] . "</td><td>" . $row['room_name'] . "</td><td>" . $row['floor'] . "</td><td>" . $row['type'] . "</td><td>" . $row['size'] . "</td><td>" . $row['capacity'] . "</td><td>" . $row['venue_admin_ID'] . "</td><td>" . $row['fee'] . "</td><td>" . $row['start_time'] . "</td><td>" . $row['end_time'] . "</td><td>" . $row['status'] . "</td></tr>";
                        }
                        echo "</table>";
                        // return $result;
                        // echo $results["name_ch"];
                    } else {
                        // echo "<br>無此社團資訊";
                    }

                    return mergedData;
                }
            }  
            
            
        ?>
        <?php
            
            
                echo "選定建築： " . $selectedBuilding . "<br>";
                echo "選定空間： " . $selectedRoom . "<br>";
                echo "選定時間： " . $selectedYear . "年" . $selectedMonth . "月" . $selectedDay . "日" ."<br>";
                echo "選定時段： " . $slotForShown . "<br>";
                echo "選定狀態： " . $selectedStatus . "<br>";
                echo "選定類型： " . $selectedType . "<br>";
                echo "預算： " . $budget . "<br>";
                echo "容納人數： " . $capacity . "<br>";
                $date = $selectedYear . $selectedMonth . $selectedDay;
                if($budget == Null){
                    $budget = 1000000;
                }
                if($capacity == Null){
                    $capacity = 1000000;
                }
                $results = query_under_condition($date, $slotForShown, $budget, $selectedStatus, $selectedType, $capacity, $selectedBuilding);
                print_r($results);
            
            
        ?>
    </section>
</div>
</body>
</html>


<!-- <button type="submit" style="margin-left: 5em">點擊後選擇空間</button>
        </form>
        
        <form action="#" method="post">
            
            <p>空間名稱：
            <select name="room" id="input">
            <?php
               
               $selectedRoom = $_POST['room'] ?? '';
               try {
                   $sql_room = "SELECT room_name FROM venue
                               Where building_name = :building_name;";
                   $stmt = $pdo->prepare($sql_room);
                   $stmt->bindParam(':building_name', $selectedBuilding);
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
       </select> -->
       <!-- <button type="submit" style="margin-left: 5em">查詢</button></p>  -->
   <!-- </form>
   <form action="#" method="post"> -->