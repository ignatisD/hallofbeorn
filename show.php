<?php
/**
 * Created by PhpStorm.
 * User: iggi
 * Date: 13/10/2017
 * Time: 1:07 μμ
 */
header("Content-Type: application/json");
if(empty($_POST["set"])) {
    $data = array();
    $data["success"] = false;
    $data["reason"] = "You need to specify a set.";
    die(json_encode($data));
}
require_once "WebCrawler.php";
$baseOrFullUrl = "http://hallofbeorn.com/LotR?CardSet=";
$crawler = new WebCrawler($baseOrFullUrl, $_POST["set"]);
$data = $crawler->retrieveImages();
echo json_encode($data);