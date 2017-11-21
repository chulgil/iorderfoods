<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
header('Content-Type: application/xml');
header('Access-Control-Allow-Origin: *');
$folderName = $_GET['folderName'];
$fileName = $_GET['fileName'];
$target_dir = 'Notification/' . $folderName . '/' . $fileName;
if (file_exists($target_dir)) {
    echo file_get_contents($target_dir);
}
?>
