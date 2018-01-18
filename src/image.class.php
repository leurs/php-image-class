<?php
 class Image
 {
  protected $inputImage;
  
  protected $outputImage;
  
  protected $outputQuality;
  
  protected $mimeType;
  
  protected $mimeTypes = [
    'gif' => 'image/gif',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png'
  ];
  
  protected $exif;
  
  public function __construct($inputImage = null, $outputQuality = null) {
    if (!is_null($inputImage)) {
      $this->inputImage = $inputImage;
    }
    
    if (!is_null($outputQuality)) {
      $this->outputQuality = $outputQuality;
    }
  }
  
  public function __destruct() {
    imagedestroy($this->outputImage);
  }
 }
 ?>
