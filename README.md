## Hall of Beorn Crawler

Usage: 
```php
require_once "WebCrawler.php";
$url = 'http://hallofbeorn.com/LotR?CardSet=The%20Hunt%20for%20Gollum';
$crawler = new WebCrawler($url);
$findImages = $crawler->retrieveImages();
if(!empty($findImages["success"])){
    foreach($findImages["images"] as $image){
        if(!empty($image["src"]) && !empty($image["label"])){
            $folder = 'photos';
            $crawler->saveImage($image["src"], $image["label"], $folder);
        }
    }
}

```

Alternatively:
```php
require_once "WebCrawler.php";

$crawler = new WebCrawler();

$url = 'http://hallofbeorn.com/LotR?CardSet=The%20Hunt%20for%20Gollum';

$findImages = $crawler->retrieveImages($url);
// etc...
```

### Methods: 
 
 - `retriveImages($url = null)`
 - `saveImage($src, $name = '', $folder = "photos")`
 
 ----------------------------------------
 
 Good luck Lefteris