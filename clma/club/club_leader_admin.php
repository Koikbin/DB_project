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

        function leader_review($activitySn, $reviewStatus) {
            global $pdo;
            try {
                
                $pdo->beginTransaction();

                // Your SQL update statement with parameterized input and FOR UPDATE clause
                $query = "
                    UPDATE activity
                    SET leader_review = :reviewStatus
                    WHERE sn = :activitySn
                    RETURNING *;  -- Optional: returns the updated row for further processing
                ";

                // Prepare and execute the query with user input
                $statement = $pdo->prepare($query);
                $statement->bindParam(':activitySn', $activitySn, PDO::PARAM_INT);
                $statement->bindParam(':reviewStatus', $reviewStatus, PDO::PARAM_STR);
                $statement->execute();

                // Fetch the updated row
                $updatedRow = $statement->fetch(PDO::FETCH_ASSOC);

                // Call cancellation_handling if reviewStatus is '否決'
                if ($reviewStatus === '否決') {
                    cancellation_handling($activitySn);
                }

                // Commit the transaction
                $pdo->commit();

                // Close the database connection
                $pdo = null;

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

        // Example usage
        // $activitySn = 106804;
        // $reviewStatus = '否決';

        // $updatedRow = leader_review($activitySn, $reviewStatus);

        ?>



    

</div>
</body>
</html>
