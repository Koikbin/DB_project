<!DOCTYPE html>
<html>
<head>
<title>社團帳戶查詢</title>

<!-- <link rel="icon" href="icon_path" type="./img/icon.png"> -->

</head>
<body>
<link rel="stylesheet" href="style_club.css?v=<?php echo time(); ?>">
<nav>
  <ul class="inline-block-nav">
    <li><a href="../board_student.php">首頁</a></li>
    
  </ul>
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
    
    <h1>社團記點查詢</h1>
    
    <form action="#" method="post">
        <label for="message">輸入欲查詢之社團名稱：</label>
            <input type="text" id="input" name="club_name"><br>
        <label for="message">輸入欲查詢之學期（例：112-1）：</label>
            <input type="text" id="input" name="semester">
        <button type="submit" style="margin-left: 5em">查詢</button>
    </form>
    <!-- <form action="#" method="post">
        <label for="message">輸入欲查詢之學期（例：112-1）：</label>
            <input type="text" id="input" name="semester" required>
        <button type="submit" style="margin-left: 5em">查詢</button>
    </form> -->
    <?php

        // $clubName = $_POST["club_name"]; // affect by the 'name="account" above
        $inputExist = True;
        if(empty($_POST["club_name"]) && empty($_POST["semester"])){
            $inputExist = False;
        }
        else{
            $inputExist = True;
        }
        if(empty($_POST["club_name"])){
            $club_name_ch = "";
        }
        else{
            $club_name_ch = $_POST["club_name"];
        }
        if(empty($_POST["semester"])){
            $semester = "";
        }
        else{
            $semester = $_POST["semester"];
        }
        
        
        // echo $club_name_ch;

        if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST" && $inputExist == True) {
            // $club_name_ch = $_POST['club_name_ch'];
            
            

            $sql = 'Select c."ID", c.semester, c.name_ch, c.name_en, c.email, c.website, c.type, c.status
                    From club as c
                    Where c.name_ch = :club_name_ch and semester = :semester
                    LIMIT 100;';


            

            try {
                $stmt = $pdo->prepare('SELECT c."ID" FROM public.club as c WHERE name_ch = :club_name_ch;');
                $stmt->bindParam(':club_name_ch', $club_name_ch);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $ID = $row['ID'];

                // echo $club_name_ch;
                // echo $ID;





                $statement = $pdo->prepare($sql);
                // $like_club_name_ch = '%' . $club_name_ch . '%';
                // $like_semester = '%' . $semester . '%';
                $statement->bindParam(':semester', $semester);
                $statement->bindParam(':club_name_ch', $club_name_ch);
                $statement->execute();
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                echo "社團名稱搜尋： ".$club_name_ch;
                echo "<br>學期搜尋： ".$semester;
                
                // var_dump($results);
                // print_r($results); 
            } catch (PDOException $e) {
                echo "<br>Error executing query: " . $e->getMessage();
            }
        }       
    ?>
    <?php
        function stored_value_card_query($clubId) {
            global $pdo;
            try {
                // echo "get club_id: ". $clubId . "<br>";
                // // Start a transaction

                // $pdo->beginTransaction();
                
                // Your SQL query with parameterized input and FOR UPDATE clause
                $query = '
                    SELECT *
                    FROM stored_value_card AS svc
                    WHERE svc."club_ID" = :clubId
                    ORDER BY svc.time_stamp
                    FOR UPDATE;
                ';
                

                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                try {
                    $statement = $pdo->prepare($query);
                    if (!$statement->bindParam(':clubId', $clubId, PDO::PARAM_STR)) {
                        // Handle bindParam failure
                        echo "Binding parameter failed.";
                    }
                    $statement->execute();
                    // ... rest of your code ...
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }

        
                
                // Fetch the results
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                // print_r($results);
                if ($results) {
                    echo "<table>";
                    echo "<tr><th>社團編號</th><th>學期</th><th>餘額</th><th>備註</th><th>時間戳記</th></tr>";
                    foreach ($results as $row) {
                        // echo $row['ID']. $row['semester'];
                        echo "<tr><td>" . $row['club_ID'] . "</td><td>" . $row['semester'] . "</td><td>" . $row['balance'] . "</td><td>" . $row['note'] . "</td><td>" . $row['time_stamp'] . "</td></tr>";
                    }
                    echo "</table>";
                    // return $result;
                    // echo $results["name_ch"];
                } else {
                    echo "<br>無此社團資訊";
                }
                // Commit the transaction
                $pdo->commit();

                // Close the database connection
                $pdo = null;

                // Return the results or perform further processing
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
        $clubId = $ID;
        // $_POST['club_id']; // Replace with your actual user input handling
        $results = stored_value_card_query($clubId);

        // if ($results !== false) {
        //     print_r($results);
        // }
        
    ?>



</div>
</body>
</html>
