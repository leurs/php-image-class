# php-image-class

<p>A PHP class to handle images.</p>

<h2>Overview</h2>

<pre><code>&lt;?php
  // create a new instance
  $image = new Image('demo.jpg',100);
  
  // handle the image
  $image
      ->crop(200,200)
      ->sepia()
      ->save('demo.jpg')
      ->show();
?&gt;</code></pre>
