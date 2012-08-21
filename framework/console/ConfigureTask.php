<?php
/**
 * Configure Console Task
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage International
 **/

namespace KoolDevelop\Console;


/**
 * Configure Console Task
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package KoolDevelop
 * @subpackage International
 **/
class ConfigureTask implements \KoolDevelop\Console\ITask
{
    
    private $DefaultClasses = array(
        '\\KoolDevelop\\Router',
        '\\KoolDevelop\\ErrorHandler',
        '\\KoolDevelop\\View\\View',
        '\\KoolDevelop\\View\\Element',
        '\\KoolDevelop\\International\\I18n',
        '\\KoolDevelop\\International\\L10n',
        '\\KoolDevelop\\Model\\ContainerModel',
    );
    
	/**
	 * Find index of section in file contents, create
	 * new section if it doesn't exist
	 *
	 * @param string   $section       Section name
	 * @param string[] $file_contents File contents, exploded on newline
	 *
	 * @return void
	 */
	private function findSectionIndex($section, &$file_contents) {

		foreach($file_contents as $index => &$row) {
			if (trim($row) == '[' . $section . ']') {
				return $index;
			}
		}
		
		$file_contents[] = '';
		$file_contents[] = '[' . $section . ']';
		return \count($file_contents)-1;
	}

	/**
	 * Check if option is already in section
	 *
	 * @param string   $option        Option name
	 * @param int      $section_index Section index from findSectionIndex
	 * @param string[] $file_contents File contents, exploded on newline
	 *
	 * @return boolean Option exists
	 */
	private function hasOption($option, $section_index, &$file_contents) {
		
		$file_contents_size = count($file_contents);

		// If section is at file end, we know that there are no options
		if (($section_index+1) >= $file_contents_size) {
			return false;
		}

		for($x =($section_index+1); $x < $file_contents_size; $x++) {


			// Start of new section
			if (substr($file_contents[$x], 0, 1) == '[') {
				return false;
			}

			// Find property in the form of
			// property = ...
			if (\preg_match('/^(;?)' . $option . '(\s*)=/', $file_contents[$x])) {
				return true;
			}


			// Find property in the form of
			// property[] = ...
			if (\preg_match('/^(;?)' . $option . '\[\](\s*)=/', $file_contents[$x])) {
				return true;
			}

		}

		return false;
		
	}

    /**
	 * Process configution file, adding new options
	 * 
	 * @param mixed[][] $options       Options collected by processClasses
	 * @param string[]  $file_contents File contents, exploded on newline
	 * 
	 * @return void
	 */
	private function processFile(&$options, &$file_contents) {

		foreach($options as $section => &$section_options) {
			$section_index = $this->findSectionIndex($section, $file_contents);
			foreach($section_options as $property => &$section_option) {

				/* @var $section_option \KoolDevelop\Configuration\IConfigurableOption */
				if (!$this->hasOption($property, $section_index, $file_contents)) {

					$n_option = array(
						'[' . $section . ']'
					);

					if ($section_option->getDocumentation() != '') {
						$n_option[] = '; ' . $section_option->getDocumentation();
					}

					$n_option[] = ($section_option->getRequired() ? '' : ';') . $property . '=' . $section_option->getDefault();

					array_splice($file_contents, $section_index, 1, $n_option);

				}
			}
		}
		
	}

    /**
     * Process collected classnames
     * 
     * @param string[] $classnames Classnames
     * 
     * @return void
     */
    private function processClasses($classnames) {

		// Here options will be stored
		// $options[<file>][<section>][option] = \KoolDevelop\Configuration\IConfigurableOption
        $options = array();
        
        // Copy array of classes to process
        $classes_to_process = $classnames;

        while(count($classes_to_process) > 0) {
            $class = array_shift($classes_to_process);
            $refl = new \ReflectionClass($class); 
            if ($refl->implementsInterface('\\KoolDevelop\\Configuration\\IConfigurable')) {                
                
                // Add dependable classes
                foreach($class::getDependendClasses() as $dependend_class) {
                    if (!in_array($dependend_class, $classnames)) {
                        $classes_to_process[] = $dependend_class;
                        $classnames[] = $dependend_class;
                    }
                }
                
                // Add parent class to list of classes to inspect
                if (false !== ($parent = $refl->getParentClass())) {
                    $parent_class_name = '\\' . $parent->getName();
                    if (!in_array($parent_class_name, $classnames)) {
                        $classes_to_process[] = $parent_class_name;
                        $classnames[] = $parent_class_name;
                    }
                }                
            }
        }
        
        foreach($classnames as $class) {
            $refl = new \ReflectionClass($class); 
            if ($refl->implementsInterface('\\KoolDevelop\\Configuration\\IConfigurable')) {                
                $configuration_options = $class::getConfigurationOptions();
                for($x = count($configuration_options)-1; $x >= 0; $x--) {
                    $this->processOption($configuration_options[$x], $options);
                }
            }
        }

		foreach($options as $file => &$file_options) {
			if (file_exists(CONFIG_PATH . DS . $file . '.ini')) {
				$file_contents = file(CONFIG_PATH . DS . $file . '.ini' , FILE_IGNORE_NEW_LINES);
			} else {
				$file_contents = array();
			}
			$this->processFile($file_options, $file_contents);

			file_put_contents(
				CONFIG_PATH . DS . $file . '.ini',
				join("\n", $file_contents)
			);

		}

    }

    /**
     * Process Configuration option and add it to the total list of options
     * 
     * @param \KoolDevelop\Configuration\IConfigurableOption $option  Option
     * @param string[][][]                                   $options Total list op options
     * 
     * @return void
     */
    private function processOption(\KoolDevelop\Configuration\IConfigurableOption $option, &$options) {
        
        $file = $option->getFile();
        list($section, $property) = explode('.', $option->getOption());
                
        // Check if file exists
        if (!isset($options[$file])) {
            $options[$file] = array(
                $section => array(
                    $property => array()
                )
            );
            
        // Check if section exists
        } else if (!isset($options[$file][$section])) {
            $options[$file][$section] = array(
                $property => array()
            );
            
        // Check if option exists            
        } else if (!isset($options[$file][$section][$property])) {
            $options[$file][$section][$property] = array();
        }

		// Always store the last version of the option
        $options[$file][$section][$property] = $option;
    }
    
    /**
     * Default function for task
     * 
     * @return void
     */
    public function index() {
        
        $this->processClasses($this->DefaultClasses);
        
    }
    
}