<?php

class Image{

	/**
	 * $inputImage
	 *
	 * @access protected
	 * @var object
	 */
	protected $inputImage;

	/**
	 * $outputImage
	 *
	 * @access protected
	 * @var object
	 */
	protected $outputImage;

	/**
	 * $outputQuality
	 *
	 * @access protected
	 * @var int
	 */
	protected $outputQuality;

	/**
	 * $mimeType
	 *
	 * @access protected
	 * @var string
	 */
	protected $mimeType;

	/**
	 * $mimeTypes
	 *
	 * @access protected
	 * @var array
	 */
	protected $mimeTypes = array(
		'gif' => 'image/gif',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'png' => 'image/png'
	);

	/**
	 * $exif
	 *
	 * @access protected
	 * @var array
	 */
	protected $exif;

	/**
	 * __construct($inputImage = null,$quality = null)
	 *
	 * @param string $inputImage				the source image
	 * @param int $outputQuality 				the quality of the output
	 * @access public
	 */
	public function __construct($inputImage = null,$outputQuality = null){

		if(!is_null($inputImage)){

			$this->inputImage = $inputImage;

		};

		if(!is_null($outputQuality)){

			$this->outputQuality = $outputQuality;

		};

		if($this->inputImage){

			return $this->createImage();

		};

	}

	/**
	 * __destruct()
	 *
	 * @access public
	 */
	public function __destruct(){

		imagedestroy($this->outputImage);

	}

	/**
	 * setImage($inputImage = null)
	 *
	 * @param string $inputImage 			the source image
	 * @return object
	 * @access public
	 */
	public function setImage($inputImage = null){

		if(is_null($inputImage)){

			throw new Exception('No file selected');
		};

		$this->inputImage = $inputImage;

		return $this;
	}

	/**
	 * setOutputQuality($outputQuality = 100)
	 *
	 * @param int $outputQuality 			the quality of the output
	 * @return object
	 * @access public
	 */
	public function setOutputQuality($outputQuality = 100){

		$this->outputQuality = $outputQuality;

		return $this;

	}

	/**
	 * createImage()
	 *
	 * @return object
	 * @access public
	 */
	public function createImage(){

		$info = getimagesize($this->inputImage);

		if($info === false){

			throw new Exception('Invalid image');

		};

		$this->mimeType = $info['mime'];

		switch($this->mimeType){

			case 'image/gif':

				$this->outputImage = imagecreatefromgif($this->inputImage);

			break;

			case 'image/jpeg':

				$this->outputImage = imagecreatefromjpeg($this->inputImage); 

				$this->exif = @exif_read_data($this->inputImage);

			break;

			case 'image/png':

				$this->outputImage = imagecreatefrompng($this->inputImage);

			break;

		};

		if(!$this->outputImage){

			throw new Exception('Unsupported image file');

		};

		imagepalettetotruecolor($this->outputImage);

		return $this;

	}

	/**
	 *	+-----------------------------------------------------------------------+
	 * 	| RENDER FUNCTIONS 														|
	 *	+-----------------------------------------------------------------------+
	 */

	/**
	 * download($filename)
	 *
	 * @param string $filename 					the filename to which the image must be downloaded
	 * @return object
	 * @access public
	 */
	public function download($filename){

		$image = $this->render();

		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		
		header('Content-Description: File Transfer');
		
		header('Content-Length: '.strlen($image));
		
		header('Content-Transfer-Encoding: Binary');
		
		header('Content-Type: application/octet-stream');
		
		header("Content-Disposition: attachment; filename=\"$filename\"");
		
		echo $image;

		return $this;

	}

	/**
	 * render()
	 *
	 * @return object
	 * @access protected
	 */
	protected function render(){

		switch($this->mimeType){

			case 'image/gif':

				$image = $this->renderGIF();

			break;

			case 'image/jpeg':

				$image = $this->renderJPEG();

			break;

			case 'image/png':

				$image = $this->renderPNG();

			break;

		};

		return $image;

	}

	/**
	 * renderGIF()
	 *
	 * @return object
	 * @access protected
	 */
	protected function renderGIF(){

		ob_start();

		imagesavealpha(
			$this->outputImage,
			true
		);

		imagegif(
			$this->outputImage,
			null
		);

		$image = ob_get_contents();

		ob_end_clean();

		return $image;

	}

	/**
	 * renderJPEG()
	 *
	 * @return object
	 * @access protected
	 */
	protected function renderJPEG(){

		ob_start();

		imageinterlace(
			$this->outputImage,
			true
		);

		imagejpeg(
			$this->outputImage,
			null,
			$this->outputQuality
		);

		$image = ob_get_contents();

		ob_end_clean();

		return $image;

	}

	/**
	 * renderPNG()
	 *
	 * @return object
	 * @access protected
	 */
	protected function renderPNG(){

		ob_start();

		imagesavealpha(
			$this->outputImage,
			true
		);

		imagepng(
			$this->outputImage,
			null,
			round(9*$this->outputQuality/100)
		);

		$image = ob_get_contents();

		ob_end_clean();

		return $image;

	}

	/**
	 * save($filename)
	 *
	 * @param string filename					the filename to which the image must be saved
	 * @return object
	 * @access public
	 */
	public function save($filename){

		$image = $this->render();

		if(!file_put_contents($filename,$image)){

			throw new Exception('Unable to write image to file');

		};

		return $this;
		
	}

	/**
	 * show()
	 *
	 * @return object
	 * @access public
	 */
	public function show(){

		$image = $this->render();

		header('Content-Type: '.$this->mimeType);

		echo $image;

		return $this;

	}

	/**
	 *	+-----------------------------------------------------------------------+
	 * 	| INFO FUNCTIONS 														|
	 *	+-----------------------------------------------------------------------+
	 */

	/**
	 * getAspectRatio()
	 *
	 * @return int
	 * @access public
	 */
	public function getAspectRatio(){

		return (int) $this->getWidth()/$this->getHeight();

	}

	/**
	 * getExifData()
	 *
	 * @return array
	 * @access public
	 */
	public function getExifData(){

		if(isset($this->exif)){

			return $this->exif;

		}else{

			return false;

		};

	}

	/**
	 * getHeight()
	 *
	 * @return int
	 * @access public
	 */
	public function getHeight(){

		return (int) imagesy($this->outputImage);

	}

	/**
	 * getMimeType()
	 *
	 * @return string
	 * @access public
	 */
	public function getMimeType(){

		return $this->mimeType;

	}

	/**
	 * getOrientation()
	 *
	 * @return string
	 * @access public
	 */
	public function getOrientation(){

		if($this->getWidth() > $this->getHeight()){

			return 'landscape';

		};

		if($this->getWidth() < $this->getHeight()){

			return 'port
		};

		return 'square';

	}rait';


	/**
	 * getWidth()
	 *
	 * @return int
	 * @access public
	 */
	public function getWidth(){

		return (int) imagesx($this->outputImage);

	}

	/**
	 *	+-----------------------------------------------------------------------+
	 * 	| IMAGE MANIPULATION FUNCTIONS 											|
	 *	+-----------------------------------------------------------------------+
	 */

	/**
	 * crop()
	 *
	 * @param int $x1 							the starting point (x)
	 * @param int $y1 							the sarting point (y)
	 * @param int $x2 							the ending point (x)
	 * @param int $y2 							the ending point (y)
	 * @access public
	 */
	public function crop($x1,$y1,$x2,$y2){

		$this->outputImage = imagecrop(
			$this->outputImage,
			array(
				'x'			=> min(self::range($x1,0,$this->getWidth()),self::range($x2,0,$this->getWidth())),
				'y'			=> min(self::range($y1,0,$this->getHeight()),self::range($y2,0,$this->getHeight())),
				'width'		=> abs(self::range($x2,0,$this->getWidth())-self::range($x1,0,$this->getWidth())),
				'height'	=> abs(self::range($y2,0,$this->getHeight())-self::range($y1,0,$this->getHeight()))
			)
		);

		return $this;

	}

	/**
	 * fit($maxWidth,$maxHeight)
	 *
	 * @param int $maxWidth 					the maximum width of the image
	 * @param int $maxHeight 					the maximum height of the image
	 * @return object
	 * @access public
	 */
	public function fit($maxWidth,$maxHeight){

		if($maxWidth >= $this->getWidth() && $maxHeight >= $this->getHeight()){

			return $this;

		};

		switch($this->getOrientation()){

			case 'portrait':

				$width = $maxHeight*$this->getAspectRatio();

				$height = $maxHeight;

			break;

			case 'landscape':

				$width = $maxWidth;

				$height = $maxWidth/$this->getAspectRatio();

			break;

			case 'square':

				$width = $maxWidth;

				$height = $maxHeight;

			break;

		};

		if($width > $maxWidth){

			$width = $maxWidth;

			$height = $width/$this->getAspectRatio();

		};

		if($height > $maxHeight){

			$height = $maxHeight;

			$width = $height*$this->getAspectRatio();

		};

		return $this->resize($width,$height);

	}


	/**
	 * flip($direction = 'horizontal')
	 *
	 * @param string $direction 				the direction of the flip of the image
	 * @return object
	 * @access public
	 */
	public function flip($direction = 'horizontal'){

		switch($direction){

			default:
			case 'horizontal':

				imageflip(
					$this->outputImage,
					IMG_FLIP_HORIZONTAL
				);

			break;

			case 'vertical':

				imageflip(
					$this->outputImage,
					IMG_FLIP_VERTICAL
				);

			break;

			case 'both':

				imageflip(
					$this->outputImage,
					IMG_FLIP_BOTH
				);

			break;

		};

		return $this;

	}

	/**
	 * orientate()
	 *
	 * @return object
	 * @access public
	 */
	public function orientate(){

		if($exif = $this->getExifData() === false){

			return $this;

		};

		switch($exif['orientation']){

			case 2:

				$this->flip('horizontal');

			break;

			case 3:

				$this->rotate(180);

			break;

			case 4:

				$this->flip('vertical');

			break;

			case 5:

				$this->rotate(90)->flip('vertical');

			break;

			case 6:

				$this->rotate(90);

			break;

			case 7:

				$this->rotate(90)->flip('horizontal');

			break;

			case 8:

				$this->rotate(-90);

			break;

		};

		return $this;

	}

	/**
	 * resize($width,$height)
	 *
	 * @param int $width 						the width of the resized image
	 * @param int $height 						the height of the resized image
	 * @return object
	 * @access public
	 */
	public function resize($width,$height){

		if((!$width && !$height) || ($width == $this->getWidth() && $height == $this->getHeight())){

			return $this;

		};

		if($width && !$height){

			$height = $width/$this->getAspectRatio();

		};

		if(!$width && $height){

			$width = $height*$this->getAspectRatio();

		};

		$image = imagecreatetruecolor($width,$height);

		$transparent = imagecolorallocatealpha($image,0,0,0,127);

		imagefill($image,0,0,$transparent);

		imagecopyresampled(
			$image,
			$this->outputImage,
			0,
			0,
			0,
			0,
			$width,
			$height,
			$this->getWidth(),
			$this->getHeight()
		);

		$this->outputImage = $image;

		return $this;	

	}

	/**
	 * rotate($angle,$backgroundColor = 'transparent')
	 *
	 * @param int $angle 						the angle of the rotation
	 * @param string $backgroundColor 			the background color of the rotated image
	 * @return object
	 * @access public
	 */
	public function rotate($angle,$backgroundColor = 'transparent'){
	
		$this->outputImage = imagerotate(
			$this->outputImage,
			-self::range($angle,-360,360),
			$this->allocateColor($backgroundColor)
		);

		return $this;

	}

	/**
	 * thumbnail($width,$height,$startPoint = 'center')
	 *
	 * @param int $width 						the width of the thumbnail
	 * @param int $height 						the height of the thumbnail
	 * @param string $startPoint				the starting point of the thumbnail
	 * @return object
	 * @access public
	 */
	public function thumbnail($width,$height,$startPoint = 'center'){

		if(($height/$width) > ($this->getHeight()/$this->getWidth())){

			$this->resize(null,$height);

		}else{

			$this->resize($width,null);

		};

		switch($startPoint){

			default:
			case 'center':

				$x1 = floor(($this->getWidth()/2)-($width/2));
				
				$x2 = $width+$x1;
				
				$y1 = floor(($this->getHeight()/2)-($height/2));
				
				$y2 = $height+$y1;

			break;

			case 'top':

				$x1 = floor(($this->getWidth()/2)-($width/2));

				$x2 = $width+$x1;

				$y1 = 0;

				$y2 = $height;

			break;

			case 'right':

			$x1 = $this->getWidth()-$width;
			
			$x2 = $this->getWidth();
			
			$y1 = floor(($this->getHeight()/2)-($height/2));
			
			$y2 = $height+$y1;

			break;

			case 'bottom':

				$x1 = floor(($this->getWidth()/2)-($width/2));
				
				$x2 = $width+$x1;
				
				$y1 = $this->getHeight()-$height;
				
				$y2 = $this->getHeight();

			break;

			case 'left':

				$x1 = 0;
				
				$x2 = $width;
				
				$y1 = floor(($this->getHeight()/2)-($height/2));
				
				$y2 = $height+$y1;

			break;

		};

		return $this->crop($x1,$y1,$x2,$y2);

	}

	/**
	 * watermark($watermark,$position = 'center',$xOffset,$yOffset,$opacity = 1)
	 *
	 * @param string $watermark 				the watermark
	 * @param string $position 					the position of the watermark
	 * @param int $xOffset 						the offset (x)
	 * @param int $yOffset 						the offset (y)
	 * @param int $opacity 						the opacity
	 * @return object
	 * @access public
	 */
	public function watermark($watermark,$position = 'center',$xOffset,$yOffset,$opacity = 1){

		$watermark = new Image($watermark);

		$opacity = (self::range($opacity,0,1)*100);

		switch($position){

			case 'top':

				$x = ($this->getWidth()/2)-($watermark->getWidth()/2)+$xOffset;
			
				$y = $yOffset;
			
			break;
			
			case 'right':
			
				$x = $this->getWidth()-$watermark->getWidth()+$xOffset;
			
				$y = ($this->getHeight()/2)-($watermark->getHeight()/2)+$yOffset;
			
			break;
			
			case 'bottom':
			
				$x = ($this->getWidth()/2)-($watermark->getWidth()/2)+$xOffset;
			
				$y = $this->getHeight()-$watermark->getHeight()+$yOffset;
			
			break;
			
			case 'left':
			
				$x = $xOffset;
			
				$y = ($this->getHeight()/2)-($watermark->getHeight()/2)+$yOffset;
			
			break;
			
			default:
			case 'center':
			
				$x = ($this->getWidth()/2)-($watermark->getWidth()/2)+$xOffset;
			
				$y = ($this->getHeight()/2)-($watermark->getHeight()/2)+$yOffset;
			
			break;

		};

		if($opacity < 100){

			imagealphablending(
				$watermark->outputImage,
				false
			);
      	
      		imagefilter(
      			$watermark->outputImage,
      			IMG_FILTER_COLORIZE,
      			0,
      			0,
      			0,
      			127*((100-$opacity)/100)
      		);

      	};

		imagecopy(
			$this->outputImage,
			$watermark->outputImage,
			$x,
			$y,
			0,
			0,
			$watermark->getWidth(),
			$watermark->getHeight()
		);

		return $this;

	}

	/**
	 *	+-----------------------------------------------------------------------+
	 * 	| DRAW FUNCTIONS 														|
	 *	+-----------------------------------------------------------------------+
	 */

	/**
	 * border($color,$thickness = 1)
	 *
	 * @param string $color 					the color of the border
	 * @param int $thickness 					the thickness of the border
	 * @return object
	 * @access public
	 */
	public function border($color,$thickness = 1){

		$x1 = 0;

		$y1 = 0;

		$x2 = $this->getWidth()-1;

		$y2 = $this->getHeight()-1;

		for($i=0;$i<$thickness;$i++){

			$this->rectangle($x1++,$y1++,$x2--,$y2--,$color);

		};

		return $this;

	}

	/**
	 * canvas($width,$height,$backgroundColor = 'transparent',$mimeType = 'image/jpeg',$outputQuality = 100)
	 *
	 * @param int $width 						the width of the canvas
	 * @param int $height 						the height of the canvas
	 * @param string $backgroundColor 			the background color of the canvas
	 * @param string $mimeType 					the MIME type of the canvas
	 * @param int $outputQuality 				the output quality
	 * @return object
	 * @access public
	 */
	public function canvas($width,$height,$backgroundColor = 'transparent',$mimeType = 'image/jpeg',$outputQuality = 100){

		$this->mimeType = $mimeType;

		$this->outputQuality = $outputQuality;

		$this->outputImage = imagecreatetruecolor($width,$height);

		$color = $this->allocateColor($backgroundColor);

		imagefill($this->outputImage,0,0,$color);

		return $this;

	}

	/**
	 * ellipse($x,$y,$width,$height,$color,$thickness = 1)
	 *
	 * @param int $x 							the center point of the ellipse (x)
	 * @param int $y 							the center point of the ellipse (y)
	 * @param int $width 						the width of the ellipse
	 * @param int $height 						the height of the ellipse
	 * @param string $color 					the color of the ellipse
	 * @param int $thickness 					the thickness of the ellipse
	 * @return object
	 * @access public
	 */
	public function ellipse($x,$y,$width,$height,$color,$thickness = 1){

		imagesetthickness(
			$this->outputImage,
			$thickness
		);

		$i = 0;

		while($i++ < ($thickness*2-1)){

			imageellipse(
				$this->outputImage,
				$x,
				$y,
				$width--,
				$height--,
				$this->allocateColor($color)
			);

		};

		return $this;

	}

	/**
	 * line($x1,$y1,$x2,$y2,$color,$thickness = 1)
	 *
	 * @param int $x1 							the start position (x)
	 * @param int $y1 							the start position (y)
	 * @param int $x2 							the end position (x)
	 * @param int $y2 							the end position (y)
	 * @param string $color 					the color of the line
	 * @param int $thickness 					the thickness of the line
	 * @return object
	 * @access public
	 */
	public function line($x1,$y1,$x2,$y2,$color,$thickness = 1){

		imagesetthickness(
      		$this->outputImage,
      		$thickness
      	);

		imageline(
			$this->outputImage,
			$x1,
			$y1,
			$x2,
			$y2,
			$this->allocateColor($color)
		);

    	return $this;

	}

	/**
	 * polygon($vertices,$color,$thickness = 1)
	 *
	 * @param array $vertices 					the vertices
	 * @param string $color 					the color of the polygon
	 * @param int $thickness 					the thickness of the polygon
	 * @return object
	 * @access public
	 */
	public function polygon($vertices,$color,$thickness = 1){

		$points = array();

		foreach($vertices as $vertice){

			$points[] = $vertice['x'];

			$points[] = $vertice['y'];

		};

		imagesetthickness(
			$this->outputImage,
			$thickness
		);

		imagepolygon(
			$this->outputImage,
			$points,
			count($vertices),
			$this->allocateColor($color)
		);

		return $this;
	}

	/**
	 * rectangle($x1,$y1,$x2,$y2,$color,$thickness = 1)
	 *
	 * @param int $x1 							the start position (x)
	 * @param int $y1 							the start position (y)
	 * @param int $x2 							the end position (x)
	 * @param int $y2 							the end position (y)
	 * @param string $color 					the color of the rectangle
	 * @param int $thickness 					the thickness of the rectangle
	 * @return object
	 * @access public
	 */
	public function rectangle($x1,$y1,$x2,$y2,$color,$thickness = 1){

		imagesetthickness(
      		$this->outputImage,
      		$thickness
      	);

		imagerectangle(
			$this->outputImage,
			$x1,
			$y1,
			$x2,
			$y2,
			$this->allocateColor($color)
		);

    	return $this;

	}

	/**
	 * text($text,$size,$x,$y,$color = 'transparent')
	 *
	 * @param string $text 						the text
	 * @param int $size 						the size of the text
	 * @param int $x 							the position of the text (x)
	 * @param int $y 							the position of the text (y)
	 * @param string $color 					the color of the text
	 * @return object
	 * @access public
	 */
	public function text($text,$size,$x,$y,$color){

		$color = $this->allocateColor($color);

		imagestring($this->outputImage,$size,$x,$y,$text,$color);

		return $this;
		
	}

	/**
	 *	+-----------------------------------------------------------------------+
	 * 	| IMAGE FILTER FUNCTIONS 												|
	 *	+-----------------------------------------------------------------------+
	 */

	/**
	 * blur($type = 'gaussian',$rounds = 1)
	 *
	 * @param string $type 						the type of the blur
	 * @param int $rounds						the number of rounds the blur must be applied
	 * @return object
	 * @access public
	 */
	public function blur($type = 'gaussian',$rounds = 1){

		switch($type){

			case 'gaussian':

				$filter = IMG_FILTER_GAUSSIAN_BLUR;

			break;

			case 'selective':

				$filter = IMG_FILTER_SELECTIVE_BLUR;

			break;

		};

		for($i=0;$i<$rounds;$i++){

			imagefilter(
				$this->outputImage,
				$filter
			);

		};

		return $this;

	}

	/**
	 * brighten($percentage)
	 *
	 * @param int $percentage 					the percentage of the brightness that must be applied
	 * @return object
	 * @access public
	 */
	public function brighten($percentage){

		imagefilter(
			$this->outputImage,
			IMG_FILTER_BRIGHTNESS,
			self::range((255*$percentage)/100,0,255)
		);

		return $this;

	}

	/**
	 * colorize($color)
	 *
	 * @param string $color 					the color that must be used
	 * @return object
	 * @access public
	 */
	public function colorize($color){

		$color = self::hexToRGBA($color);

		imagefilter(
			$this->outputImage,
			IMG_FILTER_COLORIZE,
			$color['red'],
			$color['green'],
			$color['blue'],
			$color['alpha']
		);

		return $this;

	}

	/**
	 * contrast($percentage)
	 *
	 * @param int $percentage 					the percentage of the contrast that must be applied
	 * @return object
	 * @access public
	 */
	public function contrast($percentage){

		imagefilter(
			$this->outputImage,
			IMG_FILTER_CONTRAST,
			self::range($percentage,-100,100)
		);

		return $this;

	}

	/**
	 * darken($percentage)
	 *
	 * @param int $percentage 					the percentage of the darken that must be applied
	 * @return object
	 * @access public
	 */
	public function darken($percentage){

		imagefilter(
			$this->outputImage,
			IMG_FILTER_BRIGHTNESS,
			-self::range((255*$percentage)/100,0,255)
		);

		return $this;
	}

	/**
	 * desaturate()
	 *
	 * @return object
	 * @access public
	 */
	public function desaturate(){

		imagefilter(
			$this->outputImage,
			IMG_FILTER_GRAYSCALE
		);

		return $this;

	}

	/**
	 * emboss()
	 *
	 * @return object
	 * @access public
	 */
	public function emboss(){

		imagefilter(
			$this->outputImage,
			IMG_FILTER_EMBOSS
		);

		return $this;

	}

	/**
	 * invert()
	 *
	 * @return object
	 * @access public
	 */
	public function invert(){

		imagefilter(
			$this->outputImage,
			IMG_FILTER_NEGATE
		);

		return $this;

	}

	/**
	 * opacity($percentage)
	 *
	 * @param int $percentage 					the percentage of the opacity that must be applied
	 * @return object
	 * @access public
	 */
	public function opacity($percentage){

		$image = imagecreatetruecolor($this->getWidth(),$this->getHeight());

		$this->rectangle(
			0,
			0,
			$this->getWidth(),
			$this->getHeight(),
			'#ffffff',
			1
		);

		imagefill(
	 		$image,
    		0,
    	   	0,
		   	$this->allocateColor('#ffffff')
		);

	    imagealphablending(
	    	$this->outputImage,
	    	false
	    );

	    imagefilter(
	    	$this->outputImage,
	    	IMG_FILTER_COLORIZE,
	    	0,
	    	0,
	    	0,
	    	127*((100-$percentage)/100)
	    );

	    imagecopy(
	   		$image,
	   		$this->outputImage,
	   		0,
	   		0,
	   		0,
	   		0,
	   		$this->getWidth(),
	   		$this->getHeight()
	   	);

	    $this->outputImage = $image;

	    return $this;

	}

	/**
	 * pixelate($size = 1)
	 *
	 * @param int $size 						the size of the pixels
	 * @return object
	 * @access public
	 */
	public function pixelate($size = 1){

		imagefilter(
			$this->outputImage,
			IMG_FILTER_PIXELATE,
			$size,
			true
		);

		return $this;

	}

	/**
	 * sepia()
	 *
	 * @return object
	 * @access public
	 */
	public function sepia(){

		imagefilter(
			$this->outputImage,
			IMG_FILTER_GRAYSCALE
		);

		imagefilter(
			$this->outputImage,
			IMG_FILTER_COLORIZE,
			70,
			35,
			0
		);

		return $this;

	}

	/**
	 * sharpen()
	 *
	 * @return object
	 * @access public
	 */
	public function sharpen(){

		$sharpen = array(
			array(0,-1,0),
			array(-1,5,-1),
			array(0,-1,0)
		);

		$divisor = array_sum(array_map('array_sum',$sharpen));

		imageconvolution(
			$this->outputImage,
			$sharpen,
			$divisor,
			0
		);

		return $this;

	}

	/**
	 * sketch()
	 *
	 * @return object
	 * @access public
	 */
	public function sketch(){

		imagefilter(
			$this->outputImage,
			IMG_FILTER_MEAN_REMOVAL
		);

		return $this;

	}

	/**
	 *	+-----------------------------------------------------------------------+
	 * 	| COLOR FUNCTIONS 														|
	 *	+-----------------------------------------------------------------------+
	 */

	/**
	 * fill($color)
	 *
	 * @param string $color 					the color for the fill that must be applied
	 * @return object
	 * @access public
	 */
	public function fill($color){

	    $this->rectangle(
	    	0,0,
	    	$this->getWidth(),
	    	$this->getHeight(),
	    	'#ffffff',
	    	1
	    );

    	imagefill(
    		$this->outputImage,
    		0,
    		0,
    		$this->allocateColor($color)
    	);

    	return $this;

	}

	/**
	 * allocateColor($color)
	 *
	 * @param string $color 					the color that must be allocated
	 * @return array
	 * @access protected
	 */
	protected function allocateColor($color,$opacity = 1){

		$color = self::hexToRGBA($color,$opacity);

		return imagecolorallocatealpha(
			$this->outputImage,
			$color['red'],
			$color['green'],
			$color['blue'],
			$color['alpha']
		);

	}

	/**
	 *	+-----------------------------------------------------------------------+
	 * 	| UTILITY FUNCTIONS 													|
	 *	+-----------------------------------------------------------------------+
	 */

	/**
	 * range($int,$min,$max)
	 *
	 * @param int $int 							the integer
	 * @param int $min 							the minimum value for the integer
	 * @param int $max 							the maximum value for the integer
	 * @return int
	 * @access protected
	 */
	protected static function range($int,$min,$max){

		if($int < $min){

			return $min;

		};

		if($int > $max){

			return $max;

		};

		return $int;

	}

	/**
	 * hexToRGBA($color)
	 *
	 * @param string $color 					the color that must be converted
	 * @return array
	 * @access protected
	 */
	protected static function hexToRGBA($color,$opacity = 1){

		if($color == 'transparent'){

			return array(
				'red'	=> 0,
				'green'	=> 0,
				'blue'	=> 0,
				'alpha'	=> 0
			);

		};

		$hex = preg_replace('/^#/','',$color);

		if(strlen($hex) == 3){

			$color = array(
				'red'	=> $hex[0].$hex[0],
				'green'	=> $hex[1].$hex[1],
				'blue'	=> $hex[2].$hex[2]
			);

		}elseif(strlen($hex) == 6){

			$color = array(
				'red'	=> $hex[0].$hex[1],
				'green'	=> $hex[2].$hex[3],
				'blue'	=> $hex[4].$hex[5]
			);


		}else{

			throw new Exception('Invalid color code');

		};

		$color = array(
			'red'	=> hexdec($color['red']),
			'green'	=> hexdec($color['green']),
			'blue'	=> hexdec($color['blue']),
			'alpha'	=> $opacity
		);

		return array(
			'red'	=> (isset($color['red']) ? self::range($color['red'],0,255) : 0),
			'green'	=> (isset($color['green']) ? self::range($color['green'],0,255) : 0),
			'blue'	=> (isset($color['blue']) ? self::range($color['blue'],0,255) : 0),
			'alpha'	=> (isset($color['alpha']) ? self::range($color['alpha'],0,1) : 1)
		);
	
	
	}

};

?>
