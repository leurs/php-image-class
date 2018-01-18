# PHP Image Class

A PHP class to handle images.

## Overview

```php
<?php
  // create a new instance
  $image = new Image('demo.jpg',100);
  
  // handle the image
  $image
      ->crop(200,200)
      ->sepia()
      ->save('demo.jpg')
      ->show();
?>
```

## Requirements

- PHP 7.x
- [GD Library](http://php.net/manual/en/book.image.php)
