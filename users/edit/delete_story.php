<?php
session_start();

include '../../bin/db.php';  
include '../../bin/classes.php';  

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $storyId = $_POST['story_id'];


    $auth = new Auth($conn);
    $result = $auth->deleteStory($storyId);

    $_SESSION['message'] = $result;
    header("Location: ../list.php");
    exit();
}
?>
