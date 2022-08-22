<?php
session_start();
$result = array('success' => false);
if(isset($_SESSION['username'])){
    $result['success'] = true;
}
echo json_encode($result);