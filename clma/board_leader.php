<!DOCTYPE html>
<html>
<head>

<title>社團資訊系統</title>
<link rel="stylesheet" href="style_board.css">
<!-- <link rel="icon" href="icon_path" type="./img/icon.png"> -->
</head>
<body>
<div class="container">
    
    <?php
        $host = 'localhost';
        $port = 5432; // remember to replace your own connection port
        $dbname = 'clma'; // remember to replace your own database name 
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
        session_start();
        if (isset($_SESSION['ID'])) {
            $ID = $_SESSION['ID'];
            // Use $yourVariable as needed
        }
        echo $ID;
        $_SESSION['ID'] = $ID;
    ?>
    <br></br>
    <div class="link-container">
        <a href="./club/club_info.php" class="search-link">社團資訊查詢</a>
        <a href="./venue/venue_designate.php" class="search-link">場地查詢</a><br></br><br></br>
        <a href="./venue/venue_condition.php" class="search-link">條件場地查詢</a>
        <a href="./club/participation.php" class="search-link">社團參與查詢</a><br></br><br></br>
        <a href="./club/penalty.php" class="search-link">社團記點狀況查詢</a>
        <a href="./club/club_account.php" class="search-link">社團帳戶動態查詢</a><br></br><br></br>
        <a href="./club/club_leader.php" class="search-link">社團所有活動查詢</a>
        <!-- 上包含審核 -->

    </div>
    <?php
        
    ?>


</div>
</body>
</html>
