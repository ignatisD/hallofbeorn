<?php
/**
 * Created by PhpStorm.
 * User: Iggi
 * Date: 14/10/2017
 * Time: 07:33
 */

ini_set('max_execution_time', 1200); // 20 minutes
ini_set('memory_limit','1G');
if(empty($_POST["set"])) {
    header("Content-Type: application/json");
    $data = array();
    $data["success"] = false;
    $data["reason"] = "You need to specify a set.";
    die(json_encode($data));
}
if(empty($_POST["action"]) || !in_array($_POST["action"], ["show", "pdf", "pdftest", "download"])) {
    header("Content-Type: application/json");
    $data = array();
    $data["success"] = false;
    $data["reason"] = "You need to specify a valid action.";
    die(json_encode($data));
}
require_once "WebCrawler.php";
$action = $_POST["action"];
$baseOrFullUrl = "http://hallofbeorn.com/LotR?CardSet=";
$crawler = new WebCrawler($baseOrFullUrl, $_POST["set"]);
$data = $crawler->retrieveImages();
if(empty($data["success"])){
    die(json_encode($data));
}
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
        $testImages = array (
            array (
                'src' => 'photos/Bilbo-Baggins_(x1).jpg',
                'label' => 'Bilbo-Baggins_(x1).jpg',
            ),
            array (
                'src' => 'photos/Dúnedain-Mark_(x3).jpg',
                'label' => 'Dúnedain-Mark_(x3).jpg',
            ),
            array (
                'src' => 'photos/Campfire-Tales_(x3).jpg',
                'label' => 'Campfire-Tales_(x3).jpg',
            ),
            array (
                'src' => 'photos/Mustering-the-Rohirrim_(x3).jpg',
                'label' => 'Mustering-the-Rohirrim_(x3).jpg',
            ),
            array (
                'src' => 'photos/Rivendell-Minstrel_(x3).jpg',
                'label' => 'Rivendell-Minstrel_(x3).jpg',
            ),
            array (
                'src' => 'photos/Song-of-Kings_(x3).jpg',
                'label' => 'Song-of-Kings_(x3).jpg',
            ),
            array (
                'src' => "photos/Strider's-Path_(x3).jpg",
                'label' => "Strider's-Path_(x3).jpg",
            ),
            array (
                'src' => 'photos/The-Eagles-Are-Coming_(x3).jpg',
                'label' => 'The-Eagles-Are-Coming_(x3).jpg',
            ),
            array (
                'src' => 'photos/Westfold-Horse-Breaker_(x3).jpg',
                'label' => 'Westfold-Horse-Breaker_(x3).jpg',
            ),
            array (
                'src' => 'photos/Winged-Guardian_(x3).jpg',
                'label' => 'Winged-Guardian_(x3).jpg',
            ),
            array (
                'src' => 'photos/False-Lead_(x2).jpg',
                'label' => 'False-Lead_(x2).jpg',
            ),
            array (
                'src' => 'photos/Flooding_(x2).jpg',
                'label' => 'Flooding_(x2).jpg',
            ),
            array (
                'src' => 'photos/Goblintown-Scavengers_(x2_x1).jpg',
                'label' => 'Goblintown-Scavengers_(x2_x1).jpg',
            ),
            array (
                'src' => 'photos/Hunters-from-Mordor_(x5_x2).jpg',
                'label' => 'Hunters-from-Mordor_(x5_x2).jpg',
            ),
            array (
                'src' => "photos/Old-Wives'-Tales_(x3_x1).jpg",
                'label' => "Old-Wives'-Tales_(x3_x1).jpg",
            ),
            array (
                'src' => 'photos/River-Ninglor_(x2).jpg',
                'label' => 'River-Ninglor_(x2).jpg',
            ),
            array (
                'src' => 'photos/Signs-of-Gollum_(x4).jpg',
                'label' => 'Signs-of-Gollum_(x4).jpg',
            ),
            array (
                'src' => 'photos/The-East-Bank_(x2).jpg',
                'label' => 'The-East-Bank_(x2).jpg',
            ),
            array (
                'src' => 'photos/The-Eaves-of-Mirkwood_(x3).jpg',
                'label' => 'The-Eaves-of-Mirkwood_(x3).jpg',
            ),
            array (
                'src' => 'photos/The-Old-Ford_(x2_x0).jpg',
                'label' => 'The-Old-Ford_(x2_x0).jpg',
            ),
            array (
                'src' => 'photos/The-West-Bank_(x2).jpg',
                'label' => 'The-West-Bank_(x2).jpg',
            ),
            array (
                'src' => 'photos/The-Hunt-Begins-1A_(x1).jpg',
                'label' => 'The-Hunt-Begins-1A_(x1).jpg',
            ),
            array (
                'src' => 'photos/The-Hunt-Begins-1B_(x1).jpg',
                'label' => 'The-Hunt-Begins-1B_(x1).jpg',
            ),
            array (
                'src' => 'photos/A-New-Terror-Abroad-2A_(x1).jpg',
                'label' => 'A-New-Terror-Abroad-2A_(x1).jpg',
            ),
            array (
                'src' => 'photos/A-New-Terror-Abroad-2B_(x1).jpg',
                'label' => 'A-New-Terror-Abroad-2B_(x1).jpg',
            ),
            array (
                'src' => 'photos/On-the-Trail-3A_(x1).jpg',
                'label' => 'On-the-Trail-3A_(x1).jpg',
            ),
            array (
                'src' => 'photos/On-the-Trail-3B_(x1).jpg',
                'label' => 'On-the-Trail-3B_(x1).jpg',
            )
        );
        $crawler->createPDF($testImages);
}