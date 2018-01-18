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

## Features

- Read from JPG, PNG and GIF
- Write to JPG, PNG and GIF
- Get the MIME type of an image
- Get the orientation of an image
- Save, download and/or show the image
- Manipulation: crop, resize, rotate, ...
- Filters: blur, colorize, opacity, sepia, ...
- Drawing: border, ellipse, rectangle, ...
- Chainable methods
-

## Requirements

- PHP 7.x
- [GD Library](http://php.net/manual/en/book.image.php)

## Installation

Include the library manually.

```php
<?php
	require_once 'image.class.php';
?>
```

## Usage

### Initiating

You can add an image and output quality directly to the class instance or use separate methods.

```php
<?php
	// first method
	$image = new Image('demo.jpg',100);
  
	// second method
	$image = new Image();
  
	$image
		->setImage('demo.jpg')
      	->setOutputQuality(100);
?>
```

### Saving, downloading and showing an image

You can save the image to the server, force a download of the image or just show the image on the screen.

```php
<?php
	// create an instance
  	$image = new Image('demo.jpg',100);
  
	// save the image to the server
	$image->save('new.jpg');
  
  	// download the image to the computer
  	$image->download('new.jpg');
  
  	// show the image on the screen
  	$image->show();
?>
```

## License

Licensed under the [MIT License](http://opensource.org/licenses/MIT).
