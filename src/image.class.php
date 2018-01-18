<?php
/**
 * class Image
 */
 class Image
 {
  /**
   * @var string
   *
   * @access protected
   */
  protected $inputImage;
 
  /**
   * @var string
   *
   * @access protected
   */  
  protected $outputImage;

  /**
   * @var int
   *
   * @access protected
   */
  protected $outputQuality;
  
  /**
   * @var string
   *
   * @access protected
   */
  protected $mimeType;
  
  /**
   * @var array
   *
   * @access protected
   */
  protected $mimeTypes = [
    'gif' => 'image/gif',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png'
  ];
  
  /**
   * @var array
   *
   * @access protected
   */
  protected $exif = [];
  
  /**
   * @param string $inputImage
   *
   * @param int $outputQuality
   *
   * @access public
   */
  public function __construct($inputImage = null, $outputQuality = null) {
    if (!is_null($inputImage)) {
      $this->inputImage = $inputImage;
    }
    
    if (!is_null($outputQuality)) {
      $this->outputQuality = $outputQuality;
    }
  }
  
  /**
   * @access public
   */
  public function __destruct() {
    imagedestroy($this->outputImage);
  }
 }
 ?>
