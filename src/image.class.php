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
  public function __construct($inputImage = null, $outputQuality = null)
  {
    if (!is_null($inputImage)) {
      $this->inputImage = $inputImage;
    }
    
    if (!is_null($outputQuality)) {
      $this->outputQuality = $outputQuality;
    }
  }

  /**
   * @param string $inputImage
   *
   * @return object
   *
   * @access public
   */
  public function setImage($inputImage)
  {
    if(is_null($inputImage)) {
      throw new Exception('You must provide an image');
    }
   
    $this->inputImage = $inputImage;
   
    return $this;
  }
  
  /**
   * @param int $outputQuality
   *
   * @return object
   *
   * @access public
   */
  public function setOutputQuality($outputQuality)
  {
    if(is_null($outputQuality)) {
      throw new Exception('You must set an output quality');
    }
    
    if(!is_int($outputQuality)) {
      throw new Exception('The output quality must be an integer');
    }
    
    $this->outputQuality = $outputQuality;
    
    return $this;
  }

   /**
    * @access public
    */
   public function createImage()
   {
     $info = getimagesize($this->inputImage);
     
     if ($info === false) {
       throw new Exception('You must provide a valid image');
     }
     
     $this->mimeTYpe = $info['mime'];
     
     switch($this->mimeType) {
       default:
         throw new Exception('Unsupported image type');
         break;
       case 'image/gif':
         $this->outputImage = imagecreatefromgif($this->inputImage);
         break;
       case 'image/jpeg':
         $this->outputImage = imagecreatefromjpeg($this->inputImage);
         break;
       case 'image/png':
         $this->outputImage = imagecreatefrompng($this->inputImage);
         break;    
     }
     
     imagepalletetotruecolor($this->outputImage);
     
     return $this;
   }
 
  /**
   * @access public
   */
  public function __destruct()
  {
    imagedestroy($this->outputImage);
  }
 }
 ?>
