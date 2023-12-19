<!DOCTYPE html>
<html>
<head>

<title>社團資訊系統</title>
<!-- <link rel="stylesheet" href="style.css"> -->
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
    
    <h1>登入系統：</h1>
    <h2 style = "font-size: 1em">NTUIM 112-1 Database Management</h2>

    <form action="#" method="post">
        <label for="message">帳號：</label>
            <input type="text" id="input" name="account" required><br></br>
        <label for="message">密碼：</label>
            <input type="text" id="input" name="password" required><br></br>
        <button type="submit" style="margin-left: 20em">登入</button>
    </form>
    
    <?php
        $account = $_POST["account"]; // affect by the 'name="account" above
        $password = $_POST["password"];
        $accountHead = substr($account, 0, 1);
        $lengthOfAccountInput = strlen($account);
        $lengthStudentID = 9;
        
        $lengthAccount = 1;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
            
            $accountHead = strtolower($accountHead);
            $accountExist = true;
            // if($message == $expectedString)
            // echo "head ". $accountHead . "<br>";
            if($accountHead == "b"){//) {
                // determine whether the account exists
                
                
                // the input is an email account if it is longer
                if($lengthOfAccountInput > $lengthStudentID + 1){
                    
                    $stmt = $pdo->prepare("SELECT exists (SELECT 1 FROM public.student WHERE email = :email LIMIT 1);");
                    $stmt->bindParam(':email', $account);
                    $stmt->execute();
                    $result = $stmt->fetchColumn();

                    
                    $stmt = $pdo->prepare('SELECT s."ID" FROM public.student as s WHERE email = :email;');
                    $stmt->bindParam(':email', $account);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $ID = $row['ID'];
                    // echo $ID;
                    
                }
                else if($lengthOfAccountInput <= $lengthStudentID){
                    // echo "entered<br>";
                    $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.student WHERE "ID" = :ID LIMIT 1);');
                    $account = strtoupper($account);
                    // echo $account;
                    $stmt->bindParam(':ID', $account);
                    $stmt->execute();
                    $result = $stmt->fetchColumn();
                    
                    if($result){
                        $ID = $account;
                    }
                    else{
                        $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.student WHERE "ID" = :ID LIMIT 1);');
                        $account = strtolower($account);
                        // echo $account;
                        $stmt->bindParam(':ID', $account);
                        $stmt->execute();
                        $result = $stmt->fetchColumn();
                        if($result){
                            $ID = $account;
                        }
                    }
                    // echo $ID;
                }

                
                if ($result) {
                    // User exists.
                    // find the password for this account
                    $stmt1 = $pdo->prepare('SELECT s.sys_pw FROM public.student as s WHERE "ID" = :ID;');
                    $stmt1->bindParam(':ID', $ID);
                    $stmt1->execute();
                    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                    $fetchedPassword = $row['sys_pw'];
                    
                    if($password !== $fetchedPassword){
                        echo "帳號或密碼錯誤";
                    }
                    else{
                        // header("Location: board_student.php");
                        // check if the student is the leader of a club
                        $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.club WHERE "leader_ID" = :ID LIMIT 1);');
                        $stmt->bindParam(':ID', $ID);
                        $stmt->execute();
                        $isLeader = $stmt->fetchColumn();
                        if($isLeader){
                            session_start();
                            $_SESSION['ID'] = $ID;
                            header("Location: board_leader.php");
                        }
                        else{
                            session_start();
                            $_SESSION['ID'] = $ID;
                            header("Location: board_student.php");
                        }
                    }
                    // echo $fetchedPassword;
                    // header("Location: user.php");
                } else {
                    echo "帳號不存在";
                }
                exit;
            } else if ($accountHead == "c") {
                $accountHead = strtolower($accountHead);
                echo "received". $account ."<br>";
                // if($message == $expectedString)
                // determine whether the account exists
                // the input is an email account if it is longer
                $lengthID = 9;
                if($lengthOfAccountInput > $lengthID + 1){
                    $stmt = $pdo->prepare("SELECT exists (SELECT 1 FROM public.club WHERE email = :email LIMIT 1);");
                    $stmt->bindParam(':email', $account);
                    $stmt->execute();
                    $result = $stmt->fetchColumn();

                    $stmt = $pdo->prepare('SELECT c."ID" FROM public.club as c WHERE email = :email;');
                    $stmt->bindParam(':email', $account);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $ID = $row['ID'];
                    // echo $ID;
                    
                }
                else if($lengthOfAccountInput <= $lengthID){
                    // echo "entered<br>";
                    $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.club WHERE "ID" = :ID LIMIT 1);');
                    $account = strtoupper($account);
                    // echo $account;
                    $stmt->bindParam(':ID', $account);
                    $stmt->execute();
                    $result = $stmt->fetchColumn();
                    
                    if($result){
                        $ID = $account;
                    }
                    else{
                        $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.club WHERE "ID" = :ID LIMIT 1);');
                        $account = strtolower($account);
                        // echo $account;
                        $stmt->bindParam(':ID', $account);
                        $stmt->execute();
                        $result = $stmt->fetchColumn();
                        if($result){
                            $ID = $account;
                        }
                    }
                    // echo $ID;
                }
                if ($result) {
                    // User exists.
                    // find the password for this account
                    $stmt1 = $pdo->prepare('SELECT c.sys_pw FROM public.club as c WHERE "ID" = :ID;');
                    $stmt1->bindParam(':ID', $ID);
                    $stmt1->execute();
                    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                    $fetchedPassword = $row['sys_pw'];
                    

                    if($password !== $fetchedPassword){
                        echo "帳號或密碼錯誤";
                    }
                    else{
                        session_start();
                        $_SESSION['ID'] = $ID;
                        header("Location: board_club.php");
                    }
                    // echo $fetchedPassword;
                    // header("Location: user.php");
                } else {
                    echo "帳號不存在";
                }
                exit;
            } else if ($accountHead == "i") {
                $accountHead = strtolower($accountHead);
                echo "received". $account ."<br>";
                // if($message == $expectedString)
                // determine whether the account exists
                // the input is an email account if it is longer
                $lengthID = 9;
                if($lengthOfAccountInput > $lengthID + 1){
                    $stmt = $pdo->prepare("SELECT exists (SELECT 1 FROM public.instructor WHERE email = :email LIMIT 1);");
                    $stmt->bindParam(':email', $account);
                    $stmt->execute();
                    $result = $stmt->fetchColumn();

                    $stmt = $pdo->prepare('SELECT va."ID" FROM public.instructor as va WHERE email = :email;');
                    $stmt->bindParam(':email', $account);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $ID = $row['ID'];
                    // echo $ID;
                    
                }
                else if($lengthOfAccountInput <= $lengthID){
                    // echo "entered<br>";
                    $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.instructor WHERE "ID" = :ID LIMIT 1);');
                    $account = strtoupper($account);
                    // echo $account;
                    $stmt->bindParam(':ID', $account);
                    $stmt->execute();
                    $result = $stmt->fetchColumn();
                    
                    if($result){
                        $ID = $account;
                    }
                    else{
                        $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.instructor WHERE "ID" = :ID LIMIT 1);');
                        $account = strtolower($account);
                        // echo $account;
                        $stmt->bindParam(':ID', $account);
                        $stmt->execute();
                        $result = $stmt->fetchColumn();
                        if($result){
                            $ID = $account;
                        }
                    }
                    // echo $ID;
                }

                
                if ($result) {
                    // User exists.
                    // find the password for this account
                    $stmt1 = $pdo->prepare('SELECT va.sys_pw FROM public.instructor as va WHERE "ID" = :ID;');
                    $stmt1->bindParam(':ID', $ID);
                    $stmt1->execute();
                    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                    $fetchedPassword = $row['sys_pw'];
                    
                    if($password !== $fetchedPassword){
                        echo "帳號或密碼錯誤";
                    }
                    else{
                        session_start();
                        $_SESSION['ID'] = $ID;
                        header("Location: board_instructor.php");
                    }
                    // echo $fetchedPassword;
                    // header("Location: user.php");
                } else {
                    echo "帳號不存在";
                }
                exit;
            } else if ($accountHead == "o") {
                $accountHead = strtolower($accountHead);
                echo "received". $account ."<br>";
                // if($message == $expectedString)
                // determine whether the account exists
                // the input is an email account if it is longer
                $lengthID = 9;
                if($lengthOfAccountInput > $lengthID + 1){
                    $stmt = $pdo->prepare("SELECT exists (SELECT 1 FROM public.osa_admin WHERE email = :email LIMIT 1);");
                    $stmt->bindParam(':email', $account);
                    $stmt->execute();
                    $result = $stmt->fetchColumn();

                    $stmt = $pdo->prepare('SELECT va."ID" FROM public.osa_admin as va WHERE email = :email;');
                    $stmt->bindParam(':email', $account);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $ID = $row['ID'];
                    // echo $ID;
                    
                }
                else if($lengthOfAccountInput <= $lengthID){
                    // echo "entered<br>";
                    $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.osa_admin WHERE "ID" = :ID LIMIT 1);');
                    $account = strtoupper($account);
                    // echo $account;
                    $stmt->bindParam(':ID', $account);
                    $stmt->execute();
                    $result = $stmt->fetchColumn();
                    
                    if($result){
                        $ID = $account;
                    }
                    else{
                        $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.osa_admin WHERE "ID" = :ID LIMIT 1);');
                        $account = strtolower($account);
                        // echo $account;
                        $stmt->bindParam(':ID', $account);
                        $stmt->execute();
                        $result = $stmt->fetchColumn();
                        if($result){
                            $ID = $account;
                        }
                    }
                    // echo $ID;
                }

                
                if ($result) {
                    // User exists.
                    // find the password for this account
                    $stmt1 = $pdo->prepare('SELECT va.sys_pw FROM public.osa_admin as va WHERE "ID" = :ID;');
                    $stmt1->bindParam(':ID', $ID);
                    $stmt1->execute();
                    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                    $fetchedPassword = $row['sys_pw'];
                    
                    if($password !== $fetchedPassword){
                        echo "帳號或密碼錯誤";
                    }
                    else{
                        session_start();
                        $_SESSION['ID'] = $ID;
                        header("Location: board_osa.php");
                    }
                    // echo $fetchedPassword;
                    // header("Location: user.php");
                } else {
                    echo "帳號不存在";
                }
                exit;
            } else if ($accountHead == "v") {

                $accountHead = strtolower($accountHead);
                echo "received". $account ."<br>";
                // if($message == $expectedString)
                // determine whether the account exists
                // the input is an email account if it is longer
                $lengthID = 9;
                if($lengthOfAccountInput > $lengthID + 1){
                    $stmt = $pdo->prepare("SELECT exists (SELECT 1 FROM public.venue_admin WHERE email = :email LIMIT 1);");
                    $stmt->bindParam(':email', $account);
                    $stmt->execute();
                    $result = $stmt->fetchColumn();

                    $stmt = $pdo->prepare('SELECT va."ID" FROM public.venue_admin as va WHERE email = :email;');
                    $stmt->bindParam(':email', $account);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $ID = $row['ID'];
                    // echo $ID;
                    
                }
                else if($lengthOfAccountInput <= $lengthID){
                    // echo "entered<br>";
                    $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.venue_admin WHERE "ID" = :ID LIMIT 1);');
                    $account = strtoupper($account);
                    // echo $account;
                    $stmt->bindParam(':ID', $account);
                    $stmt->execute();
                    $result = $stmt->fetchColumn();
                    
                    if($result){
                        $ID = $account;
                    }
                    else{
                        $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.venue_admin WHERE "ID" = :ID LIMIT 1);');
                        $account = strtolower($account);
                        // echo $account;
                        $stmt->bindParam(':ID', $account);
                        $stmt->execute();
                        $result = $stmt->fetchColumn();
                        if($result){
                            $ID = $account;
                        }
                    }
                    // echo $ID;
                }

                
                if ($result) {
                    // User exists.
                    // find the password for this account
                    $stmt1 = $pdo->prepare('SELECT va.sys_pw FROM public.venue_admin as va WHERE "ID" = :ID;');
                    $stmt1->bindParam(':ID', $ID);
                    $stmt1->execute();
                    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                    $fetchedPassword = $row['sys_pw'];
                    
                    if($password !== $fetchedPassword){
                        echo "帳號或密碼錯誤";
                    }
                    else{
                        session_start();
                        $_SESSION['ID'] = $ID;
                        header("Location: board_venue_admin.php");
                    }
                    // echo $fetchedPassword;
                    // header("Location: user.php");
                } else {
                    echo "帳號不存在";
                }
                exit;
            } else if ($accountHead == "s") {
                $accountHead = strtolower($accountHead);
                echo "received". $account ."<br>";
                // if($message == $expectedString)
                // determine whether the account exists
                // the input is an email account if it is longer
                $lengthID = 9;
                if($lengthOfAccountInput > $lengthID + 1){
                    $stmt = $pdo->prepare("SELECT exists (SELECT 1 FROM public.sys_admin WHERE email = :email LIMIT 1);");
                    $stmt->bindParam(':email', $account);
                    $stmt->execute();
                    $result = $stmt->fetchColumn();

                    $stmt = $pdo->prepare('SELECT va."ID" FROM public.sys_admin as va WHERE email = :email;');
                    $stmt->bindParam(':email', $account);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $ID = $row['ID'];
                    // echo $ID;
                    
                }
                else if($lengthOfAccountInput <= $lengthID){
                    // echo "entered<br>";
                    $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.sys_admin WHERE "ID" = :ID LIMIT 1);');
                    $account = strtoupper($account);
                    // echo $account;
                    $stmt->bindParam(':ID', $account);
                    $stmt->execute();
                    $result = $stmt->fetchColumn();
                    
                    if($result){
                        $ID = $account;
                    }
                    else{
                        $stmt = $pdo->prepare('SELECT exists (SELECT 1 FROM public.sys_admin WHERE "ID" = :ID LIMIT 1);');
                        $account = strtolower($account);
                        // echo $account;
                        $stmt->bindParam(':ID', $account);
                        $stmt->execute();
                        $result = $stmt->fetchColumn();
                        if($result){
                            $ID = $account;
                        }
                    }
                    // echo $ID;
                }

                
                if ($result) {
                    // User exists.
                    // find the password for this account
                    $stmt1 = $pdo->prepare('SELECT va.sys_pw FROM public.sys_admin as va WHERE "ID" = :ID;');
                    $stmt1->bindParam(':ID', $ID);
                    $stmt1->execute();
                    $row = $stmt1->fetch(PDO::FETCH_ASSOC);
                    $fetchedPassword = $row['sys_pw'];
                    
                    if($password !== $fetchedPassword){
                        echo "帳號或密碼錯誤";
                    }
                    else{
                        session_start();
                        $_SESSION['ID'] = $ID;
                        header("Location: board_sys.php");
                    }
                    // echo $fetchedPassword;
                    // header("Location: user.php");
                } else {
                    echo "帳號不存在";
                }
                exit;
                
            }


        }
    ?>


</div>
</body>
</html>
