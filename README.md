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

You can save an image to the server, force to download the image to the computer or show the image on the screen.

```php
<?php
  // save the image to the server
  $image->save('new.jpg');

  // download the image to the computer
  $image->download('new.jpg');
  
  // show the image on the screen
  $image->show();
?>
```

### Utilities

You can get the following information about an image:

- Aspect ratio
- Exif data
- Height
- Mime type
- Orientation
- Width

```php
<?php
  // get the aspect ratio
  $image->getAspectRatio();
  
  // get the exif data
  $image->getExifData();
  
  // get the height
  $image->getHeight();
  
  // get the mime type
  $image->getMimeType();
  
  // get the orientation
  $image->getOrientation();
  
  // get the width
  $image->getWidth();
?>
```

### Manipulation

You can use the following manipulation methods on an image:

- Crop
- Fit
- Flip
- Orientate
- Resize
- Rotate
- Thumbnail
- Watermark

```php
<?php
  // crop an image
  $image->crop($x1, $y1, $x2, $y2)
  
  // fit an image into a given maximum width and/or height
  $image->fit($maxWidth, $maxHeight)
  
  // flip an image
  // $direction can be 'horizontal', 'vertical' or 'both'
  $image->flip($direction)
  
  // orientate an image given the exif data
  $image->orientate()
  
  // resize an image
  $image->resize($width, $height)
  
  // rotate an image
  $image->rotate($angle, $backgroundColor)
  
  // create a thumbnail of an image
  // $startingPoint can be 'top', 'right', 'bottom', 'left' or 'center'
  $image->thumbnail($width, $height, $startingPoint)
  
  // add a watermark to an image
  // $position can be 'top', 'right', 'bottom', 'left' or 'center'
  // $opacity must be between 0 and 1
  $image->watermark($watermark, $position, $xOffset, $yOffset, $opacity)
?>
```

### Filters

You can use the following filter methods on an image:

- Blur
- Brighten
- Colorize
- Contrast
- Darken
- Desaturate
- Emboss
- Invert
- Opacity
- Pixelate
- Sepia
- Sharpen
- Sketch

```php
<?php
  // add blur to an image
  // $type can be 'gaussian' or 'selective'  
  $image->blur($type, $rounds)
  
  // brighten an image
  $image->brighten($percentage)
  
  // colorize an image
  $image->colorize($color)
  
  // add contrast to an image
  $image->contrast($percentage)
  
  // darken an image
  $image->darken($percentage)
  
  // desaturate an image
  $image->desaturate()
  
  // emboss an image
  $image->emboss()
  
  // invert an image
  $image->invert()
  
  // add opacity to an image
  $image->opacity($percentage)
  
  // pixelate an image
  $image->pixelate($size)
  
  // add sepia to an image
  $image->sepia()
  
  // sharpen an image
  $image->sharpen()
  
  // sketch an image
  $image->sketch()
?>
```
  
### Drawing

You can use the following drawing methods on an image:

- Border
- Canvas
- Ellipse
- Line
- Polygon
- Rectangle
- Text

```php
<?php
  // draw a border around an image
  $image->border($color, $thickness)
  
  // draw a canvas
  $image->canvas($width, $height, $backgroundColor, $mimeType, $outputQuality)
  
  // draw an ellipse on an image
  $image->ellipse($x, $y, $width, $height, $color, $thickness)
  
  // draw a line on an image
  $image->line($x1, $y1, $x2, $y2, $color, $thickness)
  
  // draw a polyon on an image
  $image->polygon($vertices, $color, $thickness)
  
  // draw a rectangle on an image
  $image->rectangle($x1, $y1, $x2, $y2, $color, $thickness)
  
  // add text to an image
  $image->text($text, $size, $x, $y, $color)
?>
```

## License

Licensed under the [MIT License](https://opensource.org/licenses/MIT).
