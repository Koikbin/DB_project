<!-- 
<link rel="stylesheet" href="nav.css"> -->
<style>
    <?php
        $clma = "/clma"
    ?>
    <?php include 'nav.css'; ?>
</style>
<nav>
  <ul class="inline-block-nav">
    <li><a href= "<?php echo $clma;?>/board_student.php">首頁</a></li>
    <li><a href="<?php echo $clma;?>/venue/venue_designate.php">指定場地查詢</a></li>
    <li><a href="<?php echo $clma;?>/venue/venue_condition.php">條件場地查詢</a></li>
    <li><a href="<?php echo $clma;?>/club/participation.php">社團參與查詢</a></li>
    <li><a href="<?php echo $clma;?>/club/penalty.php" class="search-link">社團記點狀況查詢</a></li>
    <li><a href="<?php echo $clma;?>/club/club_account.php" class="search-link">社團帳戶動態查詢</a></li>
    <li><a href="<?php echo $clma;?>/club/club_activity.php" class="search-link">社團所有活動查詢</a></li>
    <li><a href="./club/club_activity.php" class="search-link">場地管理</a></li>
        
  </ul>
</nav>