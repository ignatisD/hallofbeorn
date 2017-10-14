<?php
/**
 * Created by PhpStorm.
 * User: Iggi
 * Date: 14/10/2017
 * Time: 07:33
 */

if(empty($_POST["set"])) {
    header("Content-Type: application/json");
    $data = array();
    $data["success"] = false;
    $data["reason"] = "You need to specify a set.";
    die(json_encode($data));
}
if(empty($_POST["action"]) || !in_array($_POST["action"], ["show", "pdf", "download"])) {
    header("Content-Type: application/json");
    $data = array();
    $data["success"] = false;
    $data["reason"] = "You need to specify a valid action.";
    die(json_encode($data));
}
require_once "WebCrawler.php";
$baseOrFullUrl = "http://hallofbeorn.com/LotR?CardSet=";
$crawler = new WebCrawler($baseOrFullUrl, $_POST["set"]);
$data = $crawler->retrieveImages();
if(empty($data["success"])){
    die(json_encode($data));
}
$action = $_POST["action"];
switch($action){
    case "show":
        echo json_encode($data);
        break;
    case "download":
        $crawler->downloadImages($data["images"]);
        break;
    case "pdf":
        $crawler->createPDF($data["images"]);
        break;
    default:
        echo "Error";
}