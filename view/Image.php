<?php
/**
 * Image Utility
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage View
 **/

namespace KoolDevelop\View;

/**
 * Image Utility
 * 
 * Image Utility class can be used for manipulating
 * images
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage View
 **/
class Image
{
    /**
     * Width in imagetype response
     */
    const IMAGEINFO_WIDTH = 0;
    
    /**
     * Height in imagetype response
     */
    const IMAGEINFO_HEIGHT = 1;    
    
    /**
     * Type in imageheight response
     */
    const IMAGEINFO_TYPE = 2;
       
    /**
     * Ratio shortest sise
     */
    const RATIO_SHORTEST = 0;
    
    /**
     * Ratio longest side
     */
    const RATIO_LONGEST = 1;
    
    /**
     * Keep no ratio
     */
    const RATIO_NONE = 2;
    
    /**
     * GD Image 
     * @var resource 
     */
    private $GDImage;
    
    /**
     * Image Size as returned from getimagesize()
     * @var mixed[]
     */
    private $ImageSize;
    
    /**
     * Location of original image
     * @var string
     */
    private $Filename;
    
    /**
     * Width of current image
     * @var int
     */
    private $Width;
    
    /**
     * Height of current image
     * @var int
     */
    private $Height;
    
    /**
     * Get current GD image resource
     * 
     * @return resource
     */
    public function getGDImage() {
        return $this->GDImage;
    }

    /**
     * Get current Width
     * 
     * @return int
     */
    public function getWidth() {
        return $this->Width;
    }

    /**
     * Get current Height
     * 
     * @return int
     */
    public function getHeight() {
        return $this->Height;
    }

        
    /**
     * Set file to use
     *      
     * Load file for manipulation
     * 
     * @param string $filename Filename
     * 
     * @return \KoolDevelop\View\Image Self
     */
    public function file($filename) {
        
        if (!file_exists($filename)) {
            throw new \KoolDevelop\Exception\FileNotFoundException(__f('Image file not found', 'kooldevelop'));
        }

        if (false === ($this->ImageSize = @getimagesize($filename))) {
            throw new \KoolDevelop\Exception\Exception(__f('Imagefile is not an actual image', 'kooldevelop'));
        }
        
        // JPEG
        if ($this->ImageSize[self::IMAGEINFO_TYPE] == IMAGETYPE_JPEG) {            
            if (false == ($this->GDImage = imagecreatefromjpeg($filename))) {
                throw new \KoolDevelop\Exception\Exception(__f('Error reading JPEG image', 'kooldevelop'));
            }            
            
        // PNG
        } else if ($this->ImageSize[self::IMAGEINFO_TYPE] == IMAGETYPE_PNG) {            
            if (false == ($this->_img = imagecreatefrompng($filename))) {
                throw new \KoolDevelop\Exception\Exception(__f('Error reading PNG image', 'kooldevelop'));
            }

        // Unsupported image format
        } else {
            throw new \KoolDevelop\Exception\Exception(__f('Unsupported image format', 'kooldevelop'));
        }
        
        $this->Width = $this->ImageSize[self::IMAGEINFO_WIDTH];
        $this->Height = $this->ImageSize[self::IMAGEINFO_HEIGHT];
        $this->Filename = $filename;
        
        return $this;
        
    }
    
    
    
    /**
     * Resize image
     * 
     * Resize image to given width and height. One of the dimensions can be
     * omited to automaticly calculate the other based on ratio. If both dimensions
     * are given the ratio is based on the $ratio parameter.
     * 
     * @param int $width  Width
     * @param int $height Height
     * @param int $ratio  Ratio to use
     * 
     * @return \KoolDevelop\View\Image Self
     */
    public function resize($width = null, $height = null, $ratio = self::RATIO_LONGEST) {
        
        if ($this->GDImage === null) {
            throw new \KoolDevelop\Exception\Exception(__f('No image loaded', 'v'));
        }
        
        if (($width === null) AND ($height === null)) {
            throw new \KoolDevelop\Exception\Exception(__f('Both width and height ommited', 'kooldevelop'));
        }
        
        // Width based on height
        if ($width === null) {
            $ratio = $this->Height / $height;
            $width = floor($this->Width / $ratio);
            
        // Height based on width
        } else if ($height === null) {
            $ratio = $this->Width / $width;
            $height = floor($this->Height / $ratio);
            
        // Keep ratio
        } else if (($ratio == self::RATIO_LONGEST) OR ($ratio == self::RATIO_SHORTEST)) {
            $ratio_h = $this->Height / $height;
            $ratio_w = $this->Width / $width; 
            
            if ($ratio == self::RATIO_LONGEST) {
                $ratio = ($ratio_h > $ratio_w) ? $ratio_h : $ratio_w;
            } else {
                $ratio = ($ratio_h < $ratio_w) ? $ratio_h : $ratio_w;
            }
            
            $width = floor($this->Width / $ratio);
            $height = floor($this->Height / $ratio);
        }
        
        $image = imagecreatetruecolor($width, $height);
        imagecopyresampled($image, $this->GDImage, 0, 0, 0, 0, $width, $height, $this->Width, $this->Height);
        
        $this->Width = $width;
        $this->Height = $height;
        imagedestroy($this->GDImage);
        $this->GDImage = $image;
        
        return $this;
        
    }
    
    /**
     * Crop image to given dimensions
     * 
     * Use offset_x and offset_y to determine the offset that is used for
     * cropping, null is use center, positive values are from the top left 
     * position, negative positions are from the bottom right position. As -0 is 
     * not posible, use true in that case
     *
     * 
     * @param int $width    Width
     * @param int $height   Height
     * @param int $offset_x Offset for Width (X) number, null or true
     * @param int $offset_y Offset for Height (Y) number, null or true
     * 
     * @return \KoolDevelop\View\Image Self
     */
    public function crop($width, $height, $offset_x = null, $offset_y = null) {
        
        if ($this->GDImage === null) {
            throw new \KoolDevelop\Exception\Exception(__f('No image loaded', 'v'));
        }
                
        if ($width > $this->Width) {
            $width = $this->Width;
        }

        if ($height > $this->Height) {
            $height = $this->Height;
        }
        
        if ($offset_x === null) {
            $offset_x = floor(($this->Width - $width) / 2);
        } else if ($offset_x === true) {
            $offset_x = $this->Width - $width;
        } else if ($offset_x < 0) {
            $offset_x = $this->Width - $width - abs($offset_x);
        }
        
        if ($offset_y === null) {
            $offset_y = floor(($this->Height - $height) / 2);
        } else if ($offset_y === true) {
            $offset_y = $this->Height - $height;
        } else if ($offset_y < 0) {
            $offset_y = $this->Height - $height - abs($offset_y);
        }
        
        if (($width + $offset_x) > $this->Width) {
            $offset_x = $this->Width - $width;
        } else if ($offset_x < 0) {
            $offset_x = 0;
        }
        
        if (($height + $offset_y) > $this->Height) {
            $offset_y = $this->Height - $height;
        } else if ($offset_y < 0) {
            $offset_y = 0;
        }
        
        $image = imagecreatetruecolor($width, $height);
        imagecopyresampled($image, $this->GDImage, 0, 0, $offset_x, $offset_y, $width, $height, $width, $height);
        
        $this->Width = $width;
        $this->Height = $height;
        imagedestroy($this->GDImage);
        $this->GDImage = $image;
        
        return $this;
        
    }
    
    /**
     * Fill image to given dimension
     * 
     * Use offset_x and offset_y to determine the offset that is used for
     * positioning in new image, null is use center, positive values are from the top left 
     * position, negative positions are from the bottom right position. As -0 is 
     * not posible, use true in that case
     * 
     * @param int    $width      Width
     * @param int    $height     Height
     * @param string $background Hex RGB or transparent 
     * @param int    $offset_x   Offset for Width (X) number, null or true
     * @param int    $offset_y   Offset for Height (Y) number, null or true
     * 
     * @return \KoolDevelop\View\Image Self
     */
    public function fill($width, $height, $background = '000000', $offset_x = null, $offset_y = null) {
        
        if ($this->GDImage === null) {
            throw new \KoolDevelop\Exception\Exception(__f('No image loaded', 'v'));
        }
                
        if ($width < $this->Width) {
            $width = $this->Width;
        }

        if ($height < $this->Height) {
            $height = $this->Height;
        }
        
        if ($offset_x === null) {
            $offset_x = floor(($width - $this->Width) / 2);
        } else if ($offset_x === true) {
            $offset_x = $width - $this->Width;
        } else if ($offset_x < 0) {
            $offset_x = $width - $this->Width - abs($offset_x);
        }
        
        if ($offset_y === null) {
            $offset_y = floor(($height - $this->Height) / 2);
        } else if ($offset_y === true) {
            $offset_y = $height - $this->Height;
        } else if ($offset_y < 0) {
            $offset_y = $height - $this->Height - abs($offset_y);
        }
                
        if ($this->Width > ($width + $offset_x)) {
            $offset_x = $width - $this->Width;
        } else if ($offset_x < 0) {
            $offset_x = 0;
        }
        
        if ($this->Height > ($height + $offset_y)) {
            $offset_y = $height - $this->Height;
        } else if ($offset_y < 0) {
            $offset_y = 0;
        }
        
        $image = imagecreatetruecolor($width, $height);
        
        if ($background == 'transparent') {
            
            imagealphablending($image, false);
            imagesavealpha($image, true);

            $background_color = imagecolorallocatealpha($image, 0,0,0,127);
            imagefill($image, 0, 0, $background_color);

        } else {
            
            $background_rgb = array(
                base_convert(substr($background, 0, 2), 16, 10),
                base_convert(substr($background, 2, 2), 16, 10),
                base_convert(substr($background, 4, 2), 16, 10)
            );

            // Vul met achtergrondkleur
            $background_color = imagecolorallocate($image, $background_rgb[0], $background_rgb[1], $background_rgb[2]);
            imagefill($image, 0, 0, $background_color);

        }
        
        imagecopyresampled($image, $this->GDImage, $offset_x, $offset_y, 0, 0, $this->Width, $this->Height, $this->Width, $this->Height);
        
        $this->Width = $width;
        $this->Height = $height;
        imagedestroy($this->GDImage);
        $this->GDImage = $image;
        
        
        return $this;
    }
    
    /**
     * Save image to file
     * 
     * @param string $filename Filename
     * @param int    $quality  Quality (0-100)
     * @param int    $type     Imagetype (IMAGETYPE_) , null to use input image type
     * 
     * @return \KoolDevelop\View\Image Self
     */
    public function save($filename, $quality, $type = NULL) {
        
        if ($this->GDImage === null) {
            throw new \KoolDevelop\Exception\Exception(__f('No image loaded', 'v'));
        }        
        
        if ($type === null) {
            $type = $this->ImageSize[self::IMAGEINFO_TYPE];
        }
        
        if ($type == IMAGETYPE_JPEG) {
            imagejpeg($this->GDImage, $filename, $quality);
        } else if ($type == IMAGETYPE_PNG) {
            imagepng($this->GDImage, $filename, ($quality / (100 / 9)));
        } else {
            throw new \KoolDevelop\Exception\Exception(__f('Unsupported image format', 'kooldevelop'));
        }
        
        return $this;
    }
    
}