<?php
/**
 * Image Helper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage BaseApplication
 **/

namespace View\Helper;

/**
 * Image Helper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage BaseApplication
 **/
class Image extends \Helper
{    
        /**
     * Convert relative output path to an absolute one
     * 
     * @param string   $input     Input image filename
     * @param string   $output    Output image filename
     * 
     * @return string Absolute path, false on errer
     */
    private function _convertOutputPath($input, $output) {
        if ($output[0] == '.') {
            $input_path = str_replace(array('/', '\\'), DS, pathinfo($input, PATHINFO_DIRNAME));
            $path = str_replace(array('/', '\\'), DS, pathinfo($output, PATHINFO_DIRNAME));

            if ($output[0] == '.' AND $output[1] == DS) {
		        $n_path = $input_path . substr($path, 1);
	        } else {
		        $n_path = realpath($input_path . DS . $path);
		        if ($n_path === false) {
			        return false;
		        }
	        }
            $file = pathinfo($output, PATHINFO_BASENAME);
            $output = $n_path . DS . $file;                        
        }
        return $output;
    }
    
    
    /**
     * Convert an image
     * 
     * Converts the input image to output image using an transformation
     * callback. When the output image exists this is used without reapplying
     * the transformation.
     * 
     * The transformation callback recieves the input filename, an absolute
     * path to the output filename and a reference to an \KoolDevelop\View\Image object
     * 
     * Output filename can be relative to the input filename, an absolute path
     * is indicated by starting with the DIRECTORY_SEPARATOR
     * 
     * @param string   $input     Input image filename
     * @param string   $output    Output image filename
     * @param callable $transform Transformation callback
     * 
     * @return string Absolute path to image, false on failure
     */
    public function convert($input, $output, $transform) {
        
        if (false == ($output = $this->_convertOutputPath($input, $output))) {
            return false;
        }
        
        if (file_exists($output)) {
            return $output;
        }
        
        try {            
            $image = new \KoolDevelop\View\Image();
            call_user_func_array($transform, array($input, $output, &$image));
        } catch(\Exception $e) {
            return false;
        }
        
        return $output;
        
    }
    
    /**
     * Render image
     * 
     * See \KoolDevelop\View\Helper\Image::convert for parameters description
     * 
     * @param type $input     Input filename
     * @param type $output    Output filename
     * @param type $transform Transformation callback
     * @param type $html      HTML Output, %FILENAME% is replaced
     * 
     * @return boolean Succes
     */
    public function display($input, $output, $transform, $html = '<img src="%FILENAME%" />') {
        
        if (false === ($output = $this->convert($input, $output, $transform))) {
            return false;
        }
        $img_url = str_replace(APP_PATH . DS . 'public_html' . DS, '', $output);
        echo str_replace('%FILENAME%', $img_url, $html);
    }
    
    
}
