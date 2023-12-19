<!DOCTYPE html>
<html>
<head>
<title>社團資訊查詢</title>

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
    
    <h1>社團資訊查詢</h1>
    
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
                    Where c.name_ch LIKE :club_name_ch and semester LIKE :semester
                    LIMIT 100;';
                    // WHERE semester = :semester or c.name_ch LIKE '%' || :club_name_ch || '%'
            // Select c."ID", c.semester, c.name_ch, c.name_en, c.email, c.website, c.type, c.status, s.name, s.email, i.name, i.email, o.name, o.email 
            //         From club as c
            //         --Join student as s on "leader_ID" = s."ID"
            //         --Join instructor as i on "club_instructor_ID" = i."ID"
            //         --Join osa_admin as o on "osa_instructor_ID" = o."ID"
            //         Where semester = :semester or name_ch = :club_name_ch;';
            // Where c.name_ch LIKE '%' || :club_name_ch || '%'

            try {
                $statement = $pdo->prepare($sql);
                $like_club_name_ch = '%' . $club_name_ch . '%';
                $like_semester = '%' . $semester . '%';
                $statement->bindParam(':semester', $like_semester);
                $statement->bindParam(':club_name_ch', $like_club_name_ch);
                $statement->execute();
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);

                echo "社團名稱搜尋： ".$club_name_ch;
                echo "<br>學期搜尋： ".$semester;
                // var_dump($results);
                // print_r($results); 
                if ($results) {
                    echo "<table>";
				    echo "<tr><th>社團編號</th><th>學期</th><th>社團中文名稱</th><th>社團英文名稱</th><th>電子郵箱</th><th>社團網站</th><th>社團類別</th><th>社團狀態</th></tr>";
                    foreach ($results as $row) {
                        // echo $row['ID']. $row['semester'];
                        echo "<tr><td>" . $row['ID'] . "</td><td>" . $row['semester'] . "</td><td>" . $row['name_ch'] . "</td><td>" . $row['name_en'] . "</td><td>" . $row['email'] . "</td><td>" . $row['website'] . "</td><td>" . $row['type'] . "</td><td>" . $row['status'] . "</td></tr>";
                    }
                    echo "</table>";
                    // return $result;
                    // echo $results["name_ch"];
                } else {
                    echo "<br>無此社團資訊";
                }
            } catch (PDOException $e) {
                echo "<br>Error executing query: " . $e->getMessage();
            }
        }
    

    ?>


</div>
</body>
</html>
