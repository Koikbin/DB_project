<!DOCTYPE html>
<html>
<head>
<title>社團活動查詢</title>

<!-- <link rel="icon" href="icon_path" type="./img/icon.png"> -->

</head>
<body>
<link rel="stylesheet" href="style_club.css?v=<?php echo time(); ?>">
<?php include('../nav/nav_club.php');?>
</nav>
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
    
    <h1>社團活動查詢</h1>
    <?php
        session_start();
        if (isset($_SESSION['ID'])) {
            $ID = $_SESSION['ID'];
            // Use $yourVariable as needed
        }
        echo $ID;
    ?>
    <!-- <form action="#" method="post">
        <label for="message">輸入欲查詢之社團名稱：</label>
            <input type="text" id="input" name="club_name"><br>
        <label for="message">輸入欲查詢之學期（例：112-1）：</label>
            <input type="text" id="input" name="semester">
        <button type="submit" style="margin-left: 5em">查詢</button>
    </form> -->
    <p>社團所有活動：</p>
    <?php
        function leader_all_activity_query($leaderId) {
            global $pdo;

            // echo "get id; " . $leaderId . "<br>";

            try {
                // Start a transaction
                $pdo->beginTransaction();

                // Your SQL query with parameterized input and FOR UPDATE clause
                $query = "
                    WITH club_ AS (
                        SELECT club.\"ID\"
                        FROM club
                        WHERE club.\"leader_ID\" = :leaderId
                    )
                    SELECT
                        act.sn,
                        act.\"club_ID\",
                        act.\"student_in_charge_ID\",
                        act.title,
                        act.type,
                        act.participant,
                        act.budget,
                        act.proposal,
                        act.review_status,
                        act.review_deadline,
                        act.payment_status,
                        act.payment_deadline,
                        act.leader_review,
                        act.instructor_review,
                        act.osa_admin_review,
                        act.venue_admin_review
                    FROM
                        activity AS act
                    WHERE
                        act.\"club_ID\" IN (SELECT * FROM club_)
                    ORDER BY
                        act.sn
                ";

                // Prepare and execute the query with user input
                $statement = $pdo->prepare($query);
                $statement->bindParam(':leaderId', $leaderId, PDO::PARAM_STR);
                $statement->execute();

                // Fetch the results
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);

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
        $leaderId = $ID;
        // Example usage
        $results = leader_all_activity_query($leaderId);

    
        if ($results !== false) {
            // print_r($results);
    
            echo "<table>";
            echo "<tr><th>活動</th><th>社團</th><th>負責人</th><th>名稱</th><th>類型</th><th>人數</th><th>預算</th><th>企劃</th><th>審核狀態</th><th>審核期限</th><th>付款狀態</th><th>付款期限</th><th>社長審核</th><th>指導人審核</th><th>課外組審核</th><th>場地管理審核</th><th>查看場地設備</th><th>審核</th></tr>"; 
            foreach($results as $row){
                // print_r($row);
                // echo "<tr><td>" . htmlspecialchars($key) . "</td><td>" . htmlspecialchars($value) . "</td></tr>";
                // $url = "activity_equipment.php";
                $activitySn = $row['sn'];
                $url_equip = "activity_equipment.php?activitySn=" . urlencode($activitySn);
                $linkText_equip = "場地設備";
                $link_equip = "<a href='" . htmlspecialchars($url_equip) . "'>" . htmlspecialchars($linkText_equip) . "</a>";
                
                $url_admin = "club_leader_admin.php?activitySn=" . urlencode($activitySn);
                $linkText_admin = "審核";
                $link_admin = "<a href='" . htmlspecialchars($url_equip) . "'>" . htmlspecialchars($linkText_admin) . "</a>";
                // session_start();
                // $_SESSION['activitySn'] = $activitySn;
                // echo "<td>" . $row['sn'] . "</td>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                $row['link_equip'] = $link_equip;
                $row['link_admin'] = $link_admin;
                echo "<td>" . $row['link_equip'] . "</td>";
                echo "<td>" . $row['link_admin'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    ?>



    

</div>
</body>
</html>
