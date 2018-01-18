# PHP Image Class

## Overview

```php
<?php
  // create a new instance
  $image = new Image('demo.jpg',100);
  
  // handle the image
  $image
      ->crop(200,200)
      ->flip('horizontal')
      ->colorize('#ff0000')
      ->sharpen()
      ->save('new.jpg')
      ->show();
?>
```

## Features

- Read and write JPG, PNG and GIF
- Get information about the image (aspect ratio, width, height, mime type, ...)
- Manipulate the image (crop, resize, flip, ...)
- Apply filters on the image (brighten, sharpen, opacity, colorize, ...)
- Draw on the image (border, ellipse, rectangle, ...)
- Chainable methods

## Requirements

- PHP 7.x
- [GD Extension](http://php.net/manual/en/book.image.php)

## Installation

Include the file manually.

```php
<?php
  require_once 'src/image.class.php';
?>
```
## Usage

### Loading an image

You can load an image by passing it directly to the instance, or by using a method.

```php
<?php
  // method 1
  $image = new Image('demo.jpg',100);
  
  // method 2
  $image = new Image();
  
  $image
      ->setImage('demo.jpg')
      ->setOutputQuality(100);
?>
```

### Saving an image

You can

## License

Licensed under the [MIT License](https://opensource.org/licenses/MIT).
