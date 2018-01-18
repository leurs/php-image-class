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
     
     switch ($this->mimeType) {
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
   * @access protected
   */
   protected function render()
   {
     switch ($this->mimeType) {
       case 'image/gif':
         $image = $this->renderGIF();
         break;
       case 'image/jpeg':
         $image = $this->renderJPEG();
         break;
       case 'image/png':
         $image = $this->renderPNG();
         break;
     }
   }
   
  /**
   * @access protected
   */
   protected function renderGIF
   {
      ob_start();
      
      imagesavealpha($this->outputImage, true);
     
      imagegif($this->outputImage, null);
     
      $image = ob_get_contents();
     
      ob_end_clean;
     
      return $image;
   }

  /**
   * @access protected
   */
   protected function renderJPEG
   {
      ob_start();
     
      imageinterlace($this->outputImage, true);
     
      imagejpeg($this->outputImage, null, $this->outputQuality);
     
      $image = ob_get_contents();
     
      ob_end_clean;
     
      return $image;
   }

  /**
   * @access protected
   */
   protected function renderPNG
   {
      ob_start();
     
      imagesavealpha($this->outputImage, true);
     
      imagepng($this->outputImage, null, round(9*$this->outputQuality/100));
     
      $image = ob_get_contents();
     
      ob_end_clean;
     
      return $image;
   }
   
  /**
   * @param string $filename
   *
   * @access public
   */
   public function save($filename)
   {
      $image = $this->render();
     
      if (!file_put_contents($filename,$image)) {
          throw new Exception('Unable to write the image to the file');
      }
     
      return $this;
   }
   
  /**
   * @param string $filename
   *
   * @access public
   */   
   public function download($filename)
   {
      $image = $this->render();
     
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Content-Description: File Transfer');
      header('Content-Length: '.strlen($image));
      header('Content-Transfer-Encoding: Binary');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="'.$filename.'"');
     
      echo $image;
     
      return $this;
   }
   
  /**
   * @access public
   */
   public function show()
   {
      $image = $this->render();
     
      header('Content-Type: '.$this->mimeType);
     
      echo $image;
     
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
