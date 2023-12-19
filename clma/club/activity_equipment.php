<!DOCTYPE html>
<html>
<head>

<title>社團資訊系統</title>
<link rel="stylesheet" href="style_club.css">
<!-- <link rel="icon" href="icon_path" type="./img/icon.png"> -->
</head>
<body>
<div class="container">
    
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
    
    <h1>社團資訊系統：</h1>
    <?php

        if (isset($_GET['activitySn'])) {
            $activitySn = $_GET['activitySn'];
            // Now, you can use $activitySn as needed
        }
        // echo $activitySn;
    ?>
    <br></br>
    <div class="link-container">
    </div>
    <?php
        function basic_activity_info($activitySn) {
            global $pdo;
            try {
                

                // Start a transaction
                $pdo->beginTransaction();

                // Your SQL query with parameterized input and FOR UPDATE clause
                $query = "
                    SELECT
                        act.sn,
                        act.title,
                        MIN(uts.date) AS start_date,
                        MAX(uts.date) AS end_date,
                        act.payment_status,
                        act.review_status
                    FROM
                        activity AS act
                    LEFT JOIN
                        use_time_slot AS uts ON act.sn = uts.activity_sn
                    WHERE
                        act.sn = :activitySn
                    GROUP BY
                        act.sn,
                        act.title;
                ";

                // Prepare and execute the query with user input
                $statement = $pdo->prepare($query);
                $statement->bindParam(':activitySn', $activitySn, PDO::PARAM_INT);
                $statement->execute();

                // Fetch the results
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                // Commit the transaction
                $pdo->commit();
                // Close the database connection
                // $pdo = null;
                return $results;
            } catch (PDOException $e) {
                // An error occurred, rollback the transaction
                if ($pdo) {
                    $pdo->rollBack();
                }
                echo "Error: " . $e->getMessage();
                // Close the database connection
                // $pdo = null;
                return false;
            }
        }
        // Example usage
        // $activitySn = 106804;
        
        function activity_venue_equipment_info($activitySn) {
            global $pdo;
            // echo "活動編號：" . $activitySn;
            try {
                // Start a transaction
                $pdo->beginTransaction();
                // Your SQL query with parameterized input and FOR UPDATE clause
                $query = "
                SELECT uts.activity_sn, uts.date, min(uts.slot) as start_time, max(uts.slot) + interval '1 hour' as end_time, uts.building_name, uts.room_name, ue.item
                FROM use_time_slot AS uts
                LEFT JOIN use_equipment AS ue ON uts.activity_sn = ue.activity_sn
                AND uts.building_name = ue.building_name
                AND uts.room_name = ue.room_name
                WHERE uts.activity_sn = :activitySn
                group by uts.activity_sn, uts.building_name, uts.room_name, ue.item, uts.date;
                ";
                // Prepare and execute the query with user input
                $statement = $pdo->prepare($query);
                $statement->bindParam(':activitySn', $activitySn, PDO::PARAM_INT);
                $statement->execute();

                // Fetch the results
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);


                
                // echo $results; 
                // Commit the transaction
                $pdo->commit();
                // Close the database connection
                $pdo = null;

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
    <?php
        $result_basic = basic_activity_info($activitySn);   
        $result_act_ven = activity_venue_equipment_info($activitySn);
        // echo $results_act_ven;

        // $result_basic = basic_activity_info($activitySn);
        // echo $result_basic;
        if ($result_act_ven !== false) {
            // echo "<br><br>basic2";
            // print_r($result_basic);
            // echo "<br><br>basic1<br>";
            // print_r($result_act_ven);
            
        }

        if ($result_basic) {
            echo "<table>";
            echo "<tr><th>社團編號</th><th>建築</th><th>空間</th><th>日期</th><th>開始時間</th><th>結束時間</th></tr>";
            // foreach ($results as $row) {
            //     // echo $row['ID']. $row['semester'];
            //     echo "<tr><td>" . $row['ID'] . "</td><td>" . $row['semester'] . "</td><td>" . $row['name_ch'] . "</td><td>" . $row['name_en'] . "</td><td>" . $row['email'] . "</td><td>" . $row['website'] . "</td><td>" . $row['type'] . "</td><td>" . $row['status'] . "</td></tr>";
            // }
            foreach($result_basic as $row){
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            // return $result;
            // echo $results["name_ch"];
        } 

        if ($result_act_ven) {
            echo "<table>";
            echo "<tr><th>社團編號</th><th>建築</th><th>空間</th><th>日期</th><th>開始時間</th><th>結束時間</th><th>設備</th></tr>";
            // foreach ($results as $row) {
            //     // echo $row['ID']. $row['semester'];
            //     echo "<tr><td>" . $row['ID'] . "</td><td>" . $row['semester'] . "</td><td>" . $row['name_ch'] . "</td><td>" . $row['name_en'] . "</td><td>" . $row['email'] . "</td><td>" . $row['website'] . "</td><td>" . $row['type'] . "</td><td>" . $row['status'] . "</td></tr>";
            // }
            foreach($result_act_ven as $row){
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            // return $result;
            // echo $results["name_ch"];
        } 
    ?>
    <?php
        


    ?>


</div>
</body>
</html>
