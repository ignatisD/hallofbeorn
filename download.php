<?php
/**
 * Created by PhpStorm.
 * User: iggi
 * Date: 13/10/2017
 * Time: 1:07 μμ
 */
if(empty($_POST["set"])) {
    die("Which set?");
}
require_once "WebCrawler.php";
$baseOrFullUrl = "http://hallofbeorn.com/LotR?CardSet=";
$crawler = new WebCrawler($baseOrFullUrl, $_POST["set"]);
$findImages = $crawler->retrieveImages();
if(empty($findImages["success"])){
    echo "<pre>";
    echo json_encode($findImages, JSON_PRETTY_PRINT);
    echo "</pre>";
}
if(sizeof($findImages["images"]) > 0){
    $crawler->downloadImages($findImages["images"]);
}else{
    echo "No images found...";
}