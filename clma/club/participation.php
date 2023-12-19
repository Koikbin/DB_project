<!DOCTYPE html>
<html>
<head>
<title>社團參與查詢</title>

<!-- <link rel="icon" href="icon_path" type="./img/icon.png"> -->

</head>
<body>
<link rel="stylesheet" href="style_club.css?v=<?php echo time(); ?>">

<?php include('../nav.php');?>
  
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
   <?php
        session_start();
        if (isset($_SESSION['ID'])) {
            $ID = $_SESSION['ID'];
            // Use $yourVariable as needed
        }
        // echo $ID;
    ?>
    <h1>社團參與查詢</h1>
    
    <form action="#" method="post">
        <!-- <label for="message">輸入欲查詢之學生學號：</label>
            <input type="text" id="input" name="club_name"><br> -->
        <label for="message">輸入欲查詢之學期（例：112-1）：</label>
            <input type="text" id="input" name="semester">
        <button type="submit" style="margin-left: 5em">查詢</button>
    </form>
    <?php
        function search_join($student_id, $semester)
        {
            // echo "get info: ". $student_id. " ". $semester;
            global $pdo;
            
            $sql_join = 'Select j."club_ID", c.name_ch, j.semester
                                From JOIN_CLUB as j
                                    join club as c on c."ID" = j."club_ID"
                                Where j."student_ID" = :student_id and j.semester like :semester;';
    
      
            try {
                $statement = $pdo->prepare($sql_join);
                $statement->bindParam(':student_id', $student_id);
                $like_semester = "%".$semester."%";
                $statement->bindParam(':semester', $like_semester);
                $statement->execute();
                $result_join = $statement->fetchAll(PDO::FETCH_ASSOC);
            }catch (PDOException $e) {
                echo "Error executing query: " . $e->getMessage();
            }
            // echo $result_join;
            // print_r($result_join);
            if ($result_join) {
                echo "<table>";
                echo "<tr><th>社團編號</th><th>社團名稱</th><th>學期</th></tr>";
                foreach ($result_join as $row) {
                    echo "<tr><td>".$row['club_ID']."</td><td>".$row['name_ch']."</td><td>".$row['semester']."</td></tr>";
                }
                echo "</table>";
            }
                
            return $result_join;
            
        }        
    ?>
    <?php
        if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST"){
            $semester = $_POST['semester'];
        }
        search_join($ID, $semester);
       
        
    ?>


</div>
</body>
</html>
