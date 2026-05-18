<?php
session_start();
include 'db.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $sql = "DELETE FROM players WHERE id = $id";
    
    if ($conn->query($sql)) {
        header("Location: index.php?msg=PlayerReleased");
    } else {
        echo "Error releasing player: " . $conn->error;
    }
} else {
    header("Location: index.php");
}
?>