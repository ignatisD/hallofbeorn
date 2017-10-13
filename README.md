## Hall of Beorn Crawler

Usage: 
```php
require_once "WebCrawler.php";
$url = 'http://hallofbeorn.com/LotR?CardSet=The%20Hunt%20for%20Gollum';
$crawler = new WebCrawler($url);
$findImages = $crawler->retrieveImages();
// etc...

```

Alternatively:
```php
require_once "WebCrawler.php";
$crawler = new WebCrawler();
$url = 'http://hallofbeorn.com/LotR?CardSet=The%20Hunt%20for%20Gollum';
$findImages = $crawler->retrieveImages($url);
// etc...
```

Then:
```php
if(!empty($findImages["success"]) && sizeof($findImages["images) > 0){
    $crawler->downloadImages($findImages["images"]);
}
```

Or:
```php
if(!empty($findImages["success"])){
    foreach($findImages["images"] as $image){
        if(!empty($image["src"]) && !empty($image["label"])){
            $folder = 'photos';
            $crawler->saveImage($image["src"], $image["label"], $folder);
        }
    }
}
```

### Methods: 
 
 - `retrieveImages($url = null)`
 - `downloadImages($images = array(), $name = "dowload.zip")`
 - `saveImage($src, $name = '', $folder = "photos")`
 
 ----------------------------------------
 
 Good luck Lefteris