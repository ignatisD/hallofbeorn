<?php
/**
 * Created by PhpStorm.
 * User: iggi
 * Date: 13/10/2017
 * Time: 9:24 πμ
 */
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Document</title>
    </head>
    <body>
        <form action="./" method="POST">
            <input type="text" value="http://hallofbeorn.com/LotR?CardSet=The%20Hunt%20for%20Gollum" name="url" />
            <button type="submit">Submit</button>
        </form>
        <?php
        if(!empty($_POST["url"])){
            require_once "WebCrawler.php";
            $url = $_POST["url"];
            $crawler = new WebCrawler($url, 1);
            $findImages = $crawler->retrieveImages();
            if(!empty($findImages["success"])){
                foreach($findImages["images"] as $image){
                    if(!empty($image["src"]) && !empty($image["label"])){
                        $folder = 'photos';
                        $crawler->saveImage($image["src"], $image["label"], $folder);
                        echo '<img src="'.$folder.'/'.$image["label"].'" alt="'.$image["label"].'" />';
                    }
                }
            }else{
                echo "<pre>";
                echo json_encode($findImages, JSON_PRETTY_PRINT);
                echo "</pre>";
            }
        }
        ?>
    </body>
</html>