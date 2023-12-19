<?php
include 'venue_condition.php';

// Assuming you've already set up the PDO connection ($pdo)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['building'])) {
    $selectedBuilding = $_POST['building'];

    try {
        $sql_room = "SELECT room_name FROM venue WHERE building_name = :building_name;";
        $stmt = $pdo->prepare($sql_room);
        $stmt->bindParam(':building_name', $selectedBuilding);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='" . htmlspecialchars($row['room_name']) . "'>" . htmlspecialchars($row['room_name']) . "111</option>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
