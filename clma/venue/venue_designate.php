<!DOCTYPE html>
<html>
<head>

<title>指定場地查詢</title>
<!-- <link rel="icon" href="icon_path" type="./img/icon.png"> -->
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
        <h1>指定場地查詢</h1>
        
        <form action="#" method="post">
            <label for="message">輸入欲查詢之場地資訊</label><br>
            <p>建築名稱：<input type="text" id="input" name="building_name"><br></p>
            <label for="message"></label>
            <p>空間名稱：
                <input type="text" id="input" name="room_name"><br></p> 
            <label for="message">日期（例：2023-11-10）：</label>
                <input type="text" id="input" name="query_date">
            
            <button type="submit" style="margin-left: 5em">查詢</button>
        </form>
        <?php
            function query_for_designated_venue($building_name, $room_name, $query_date, $inputExist)
            {
                global $pdo;
                if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST" && $inputExist == True) {
                    echo "搜索建築： " . $building_name . "<br>";
                    echo "搜索空間： " . $room_name . "<br>";
                    echo "搜索日期： " . $query_date . "<br>";

                    $sql_venue_info = 'Select v.*
                                    From venue as v
                                    Where v.building_name LIKE :building_name and v.room_name LIKE :room_name
                                    LIMIT 20;';
                                    // Where v.building_name = :building_name and v.room_name = :room_name;';
                    try{
                        $statement = $pdo->prepare($sql_venue_info);
                    }catch(PDOException $e) {
                        echo "<br>00Error executing query: " . $e->getMessage();
                    }
                    try {
                        $statement = $pdo->prepare($sql_venue_info);
                        $like_building_name = '%'.$building_name.'%';
                        $like_room_name = '%'.$room_name.'%';
                        $statement->bindParam(':building_name', $like_building_name);
                        $statement->bindParam(':room_name', $like_room_name);
                        $statement->execute();
                        
                        $result_venue_info = $statement->fetchAll(PDO::FETCH_ASSOC);
                    }catch (PDOException $e) {
                        echo "<br>11Error executing query: " . $e->getMessage();
                    }
                    $sql_designated_venue = "Select ts.date, ts.slot as start_time, ts.slot + '1 hour'::interval as end_time, ts.status
                                            From time_slot as ts
                                            Where ts.building_name LIKE :building_name and ts.room_name LIKE :room_name
                                            and ts.date between date_trunc('week', :query_date::date)
                                            and date_trunc('week', :query_date::date) + '6 days'::interval
                                            Order by ts.building_name, ts.room_name, ts.date, ts.slot;";

                    try {
                        $statement = $pdo->prepare($sql_designated_venue);
                        $like_building_name = '%'.$building_name.'%';
                        $like_room_name = '%'.$room_name.'%';
                        $statement->bindParam(':building_name', $like_building_name);
                        $statement->bindParam(':room_name', $like_room_name);
                        
                        $statement->bindParam(':query_date', $query_date);
                        $statement->execute();
                        $result_designated_venue = $statement->fetchAll(PDO::FETCH_ASSOC);
                    }catch (PDOException $e) {
                        echo "<br>22Error executing query: " . $e->getMessage();
                    }
                    $sql_equipment = "Select e.item, e.fee
                                            From equipment as e
                                            Where e.building_name LIKE :building_name and e.room_name LIKE :room_name;";
                    try {
                        $statement = $pdo->prepare($sql_equipment);
                        $like_building_name = '%'.$building_name.'%';
                        $like_room_name = '%'.$room_name.'%';
                        $statement->bindParam(':building_name', $like_building_name);
                        $statement->bindParam(':room_name', $like_room_name);

                        $statement->execute();
                        $result_equipment = $statement->fetchAll(PDO::FETCH_ASSOC);
                    }catch (PDOException $e) {
                        echo "<br>Error executing query: " . $e->getMessage();
                    }
                    // print_r($result_equipment); 
                    $result = array(
                    'venue_info' => $result_venue_info,
                    'designated_venue' => $result_designated_venue,
                    'equipment' => $result_equipment
                    );
                    return $result;
                }
            }
        ?>
        <?php
            $inputExist = True;
            if(empty($_POST["building_name"]) && empty($_POST["room_name"]) && empty($_POST["query_date"]) ){
                $inputExist = False;
            }
            else{
                $inputExist = True;
            }
            if(empty($_POST["building_name"])){
                $building_name = "";
            }
            else{
                $building_name = $_POST["building_name"];
            }
            if(empty($_POST["room_name"])){
                $room_name = "";
            }
            else{
                $room_name = $_POST["room_name"];
            }
            if(empty($_POST["query_date"])){
                $query_date = "";
            }
            else{
                $query_date = $_POST["query_date"];
            }
            $result = query_for_designated_venue($building_name, $room_name, $query_date, $inputExist);
            // var_dump($result);
            if ($result) {
                echo "<table>";
                echo "<tr><th>建築名稱</th><th>空間名稱</th><th>樓層</th><th>空間類型</th><th>空間大小（坪）</th><th>容納人數</th><th>場地管理人編號</th><th>租借費用</th></tr>";
                
                $venue_info = $result['venue_info'];
                foreach ($venue_info as $row) {
                    echo "<tr><td>".$row['building_name']."</td><td>".$row['room_name']."</td><td>".$row['floor']."</td><td>".$row['type']."</td><td>".$row['size']."</td><td>".$row['capacity']."</td><td>".$row['venue_admin_ID']."</td><td>".$row['fee']."</td></tr>";
                }
                echo "</table>";

                
                $designated_venue_info = $result['designated_venue'];
                // var_dump($designated_venue_info);
                echo "<table>";
                echo "<tr><th>日期</th><th>開始時間</th><th>結束時間</th><th>狀態</th></tr>";
                foreach ($designated_venue_info as $row) {
                    echo "<tr><td>".$row['date']."</td><td>".$row['start_time']."</td><td>".$row['end_time']."</td><td>".$row['status']."</td></tr>";
                }
                echo "</table>";

                
                $equipment_info = $result['equipment'];
                // var_dump($equipment_info);
                echo "<table>";
                echo "<tr><th>設備</th><th>費用</th></tr>";
                foreach ($equipment_info as $row) {
                    echo "<tr><td>".$row['item']."</td><td>".$row['fee']."</td></tr>";
                }
                echo "</table>";
            } else {
                echo "<br>無此場地資訊";
            }
        ?>
    </section>
</div>
</body>
</html>
