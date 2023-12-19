<!DOCTYPE html>
<html>
<head>
<title>社團帳戶管理</title>

<!-- <link rel="icon" href="icon_path" type="./img/icon.png"> -->

</head>
<body>
<link rel="stylesheet" href="style_admin.css?v=<?php echo time(); ?>">
<?php include('../nav/nav_venue_admin.php');?>
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
    
    <h1>社團帳戶管理</h1>
    <h3>查詢社團帳戶餘額</h3>
    <form action="#" method="post">
        <p><label for="message">輸入社團名稱：</label>
            <input type="text" id="input" name="club_name_search">
        <button type="submit" style="margin-left: 5em">確認</button></p>
    </form>

    <h3>社團帳戶加值/扣款</h3>
    <form action="#" method="post">
        <label for="message">輸入社團名稱：</label>
            <input type="text" id="input" name="club_name_update"><br>
        <label for="message">輸入加值/扣款金額（若是扣款請輸入負數）：</label>
            <input type="text" id="input" name="amount"><br>
        <p><label for="message">備註：</label>
            <input type="text" id="input" name="note">
        <button type="submit" style="margin-left: 5em">確認</button></p>
    </form>
    <?php
        function stored_value_card_query($clubId) {
            global $pdo;
            // echo "get id for search: " . $clubId . "<br>";
            try {
                // Start a transaction
                $pdo->beginTransaction();

                // Your SQL query with parameterized input and FOR UPDATE clause
                $query = "
                    SELECT svc.\"club_ID\", svc.semester, svc.balance, svc.note, date_trunc('second', svc.time_stamp) as time_stamp
                    FROM stored_value_card AS svc
                    WHERE svc.\"club_ID\" = :clubId
                    ORDER BY svc.time_stamp
                    FOR UPDATE;
                ";

                // Prepare and execute the query with user input
                $statement = $pdo->prepare($query);
                $statement->bindParam(':clubId', $clubId, PDO::PARAM_STR);
                $statement->execute();

                // Fetch the results
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                // Commit the transaction
                $pdo->commit();

                // Close the database connection
                $pdo = null;

                // Return the results or perform further processing
                if ($results !== false) {
                    echo "<table>";
                    echo "<tr><th>社團編號</th><th>學期</th><th>餘額</th><th>備註</th><th>時間</th></tr>"; 
                    foreach($results as $row){
                        // print_r($row);
                        echo "<tr><td>" . $row['club_ID'] . "</td><td>" . $row['semester'] . "</td><td>" . $row['balance'] . "</td><td>" . $row['note'] . "</td><td>" . $row['time_stamp'] . "</td></tr>"; 
                    }
                    echo "</table>";
                }
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
        


        if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST"){
            // $clubId = ;
            $club_name_ch = $_POST['club_name_search'];
        }
        if ($club_name_ch == Null){
            Null;
        }
        else{
            $stmt1 = $pdo->prepare('SELECT c."ID" FROM public.club as c WHERE name_ch = :club_name_ch;');
            $stmt1->bindParam(':club_name_ch', $club_name_ch);
            $stmt1->execute();
            $row = $stmt1->fetch(PDO::FETCH_ASSOC);
            $clubId_search = $row['ID'];
            echo "社團名稱： " . $club_name_ch . "<br>";

            $clubId = $clubId_search;
            $results = stored_value_card_query($clubId_search);
            if ($results !== false) {
                echo "<table>";
                echo "<tr><th>社團編號</th><th>學期</th><th>餘額</th><th>備註</th><th>時間</th></tr>"; 
                foreach($results as $row){
                    // print_r($row);
                    echo "<tr><td>" . $row['club_ID'] . "</td><td>" . $row['semester'] . "</td><td>" . $row['balance'] . "</td><td>" . $row['note'] . "</td><td>" . $row['time_stamp'] . "</td></tr>"; 
                }
                echo "</table>";
            }
        }

        
        // stored_value_card_query($clubId);
    
        function stored_value_card_update($clubId, $semester, $amount, $note) {
            global $pdo;
            echo "get info." . $clubId . $semester . $note . "<br>";
            echo "get amount: " . $amount . "<br>";
            echo "get club id: " . $clubId . "<br>";
            try {
                // Replace these with your actual database connection details
                // Establish a PostgreSQL database connection
                // $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
                // Start a transaction
                $pdo->beginTransaction();

                // Your SQL query with parameterized input and FOR UPDATE clause
                $query = "
                    WITH OriginalBalance AS (
                        SELECT svc.balance as ob
                        FROM stored_value_card AS svc
                        WHERE svc.\"club_ID\" = :clubId
                        ORDER BY EXTRACT(EPOCH FROM CURRENT_TIMESTAMP - svc.time_stamp)
                        LIMIT 1
                    )

                    INSERT INTO stored_value_card (\"club_ID\", \"semester\", \"balance\", \"note\")
                    SELECT :clubId, :semester, COALESCE((SELECT ob FROM OriginalBalance), 0) + :amount, :note
                    FROM OriginalBalance
                    FOR UPDATE;
                ";


                // Prepare and execute the query with user input
                $statement = $pdo->prepare($query);
                $statement->bindParam(':clubId', $clubId, PDO::PARAM_STR);
                $statement->bindParam(':semester', $semester, PDO::PARAM_STR);
                $statement->bindParam(':amount', $amount, PDO::PARAM_INT);
                $statement->bindParam(':note', $note, PDO::PARAM_STR);
                $statement->execute();

                // Commit the transaction
                $pdo->commit();
                echo "Update successfully.";
                stored_value_card_query($clubId);
                // Close the database connection
                $pdo = null;

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
        // Example usage
        // $clubId = 'C627';
        $semester = '112-1';
        // $amount = 100;
        // $note = '加值';
        if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST"){
            // $clubId = ;
            $club_name_ch = $_POST['club_name_update'];
            $amount = $_POST['amount'];
            $note = $_POST['note'];
        }
        $stmt1 = $pdo->prepare('SELECT c."ID" FROM public.club as c WHERE name_ch = :club_name_ch;');
        $stmt1->bindParam(':club_name_ch', $club_name_ch);
        $stmt1->execute();
        $row = $stmt1->fetch(PDO::FETCH_ASSOC);
        $clubId = $row['ID'];
        echo $club_name_ch. "<br>";

        if (stored_value_card_update($clubId, $semester, $amount, $note)) {
            
            $results = stored_value_card_query($clubId);
            echo $results;
        } else {
            echo "Update failed.";
        }
    ?>
</div>
</body>
</html>
