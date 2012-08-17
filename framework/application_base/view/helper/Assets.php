<?php

/**
 * Assets Helper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage BaseApplication
 **/

namespace View\Helper;

use KoolDevelop\Configuration;

/**
 * Assets Helper
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage BaseApplication
 **/
class Assets extends \Helper
{

    /**
     * Files that are loaded
     * @var string[]
     */
    private $Files = array();

    /**
     * HTML output JS
     * @var string[]
     */
    private $OutputJS = array();

    /**
     * HTML output CSS
     * @var string[]
     */
    private $OutputCSS = array();


    /**
     * Add javascript
     *
     * @param string  $src    Source
     * @param boolean $force  Force loading
     * @param boolean $inline Load script inline
     *
     * @return void
     */
    public function script($src, $force = false, $inline = false) {
        if ($force OR !in_array($src, $this->Files)) {
            $this->Files[] = $src;
            $js_file = Configuration::getInstance('core')->get('path.public_html', APP_PATH . DS . "public_html") . DS . $src;
            if ($inline AND file_exists($js_file)) {
                $this->OutputJS[] = '<script type="text/javascript">' . file_get_contents($js_file) . '</script>' . "\n";
            } else {
                $this->OutputJS[] = '<script type="text/javascript" src="' . htmlspecialchars($src) . '"></script>' . "\n";
            }
        }
    }

    /**
     * Add CSS
     *
     * @param string  $src    Source
     * @param string  $media  Media
     * @param boolean $force  Force loading
     * @param boolean $inline Load script inline
     *
     * @return void
     */
    public function css($src, $media = 'all', $force = false, $inline = false) {
        if ($force OR !in_array($src, $this->Files)) {
            $this->Files[] = $src;
            $css_file = Configuration::getInstance('core')->get('path.public_html', APP_PATH . DS . "public_html") . DS . $src;
            if ($inline AND file_exists($css_file)) {
                $this->OutputCSS[] = '<style type="text/css" media="' . htmlspecialchars($media) . '">' . file_get_contents($css_file) . '</style>' . "\n";
            } else {
                $this->OutputCSS[] = '<link type="text/css" rel="stylesheet" media="' . htmlspecialchars($media) . '" href="' . htmlspecialchars($src) . '" />' . "\n";
            }
        }
    }

    /**
     * Add inline Javascript
     *
     * @param string $source Source
     * @param string $media  Media
     *
     * @return void
     */
    public function inlineScript($source) {
        $this->OutputJS[] = '<script type="text/javascript">' . $source . '</script>' . "\n";
    }

    /**
     * Add inline CSS
     *
     * @param string $source Source
     * @param string $media  Media
     *
     * @return void
     */
    public function inlineStyle($source, $media = 'all') {
        $this->OutputCSS[] = '<style type="text/css" media="' . htmlspecialchars($media) . '">' . $source . '</style>' . "\n";
    }

    /**
     * Render HTML for Javascript
     *
     * @return void
     */
    public function outputScript() {
        foreach ($this->OutputJS as $output) {
            echo $output;
        }
        $this->OutputJS = array();
    }

    /**
     * Render HTML for CSS
     *
     * @return void
     */
    public function outputStyle() {
        foreach ($this->OutputCSS as $output) {
            echo $output;
        }
        $this->OutputCSS = array();
    }

}