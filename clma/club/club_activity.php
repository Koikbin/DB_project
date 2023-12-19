<!DOCTYPE html>
<html>
<head>
<title>社團活動查詢</title>
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
    <p>社團所有活動：</p>
    <?php

        function club_all_activity_query($clubId) {
            echo "get id: ". $clubId . "<br>";
            global $pdo;
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
                        act.sn;
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
        // Example usage
        $leaderId = 'B10000001';
        $results = leader_all_activity_query($leaderId);  
        if ($results !== false) {
            print_r($results);
        }
    ?>
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
        // Example usage
        // $activitySn = 106804;
        $results = basic_activity_info($activitySn);

        if ($results !== false) {
            print_r($results);
        }
    ?>

</div>
</body>
</html>
