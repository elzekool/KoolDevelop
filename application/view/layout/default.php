<?php
/**
 * Main Application Layout
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/

/* @var $this \View */


$assets = $this->helper('Assets');

$baseurl = r()->getBase();

/* @var $assets \View\Helper\Assets */
$assets->css('http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,300,700');
$assets->css('less/metro.css');

$assets->script('js/jquery.js');
$assets->script('js/bootstrap-transition.js');
$assets->script('js/bootstrap-alert.js');
$assets->script('js/bootstrap-modal.js');
$assets->script('js/bootstrap-dropdown.js');
$assets->script('js/bootstrap-tab.js');
$assets->script('js/bootstrap-button.js');
$assets->script('js/bootstrap-collapse.js');

?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title><?php echo htmlspecialchars($page_title); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <base href="<?php echo $baseurl; ?>" />
        
        <?php $assets->outputStyle(); ?>
                
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->

		<!--[if lt IE 8]>
			<link href="less/font-awesome-ie7.css" rel="stylesheet">
        <![endif]-->

    </head>

    <body>

		<!-- Navigation -->
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="sidebar-container">
                    <div class="side">
                        <a class="brand" href="<?php echo $baseurl; ?>"><i class="icon icon-film"></i> <?php __w('KoolDevelop'); ?></a>
                    </div>
                    <div class="content">
                        <ul class="nav">
                            <li <?php if ($controller == 'start') : ?>class="active"<?php endif; ?>><a href="start/index"><i class="icon icon-home"></i> <?php __w('Home'); ?></a></li>
                            <li><a href="tips"><i class="icon icon-globe"></i> <?php __w('Tips'); ?></a></li>
                            <li><a href="api/tree.html" target="_blank"><i class="icon icon-download-alt"></i> <?php __w('API'); ?></a></li>
                            <li><a href="http://www.kooldevelopment.nl" target="_blank"><i class="icon icon-user"></i> <?php __w('Author'); ?></a></li>                            
                        </ul>
                        
                        <ul class="nav pull-right">
                            <li><a href="<?php r()->getUrl() ?>?lang=en"><img src="img/flags/gb.png" /> EN</a></li>
                            <li><a href="<?php r()->getUrl() ?>?lang=nl"><img src="img/flags/nl.png" /> NL</a></li>
                        </ul>
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="sidebar-container content-container">

            <div class="side">
                <div class="sidebar">
                    <div class="container-fluid">
                        <?php echo $this->placeholder('sidebar'); ?>
                    </div>
                </div>
            </div>


            <div class="content">
                <div class="container-fluid">

                    
                    <?php
                       echo $view_content;
                    ?>
                    
                </div>
            </div>

        </div>

        <?php $assets->outputScript(); ?>
        
        <!--//
            <?php print_r(\KoolDevelop\Database\Query::$ProfileLog); ?>
        //-->
    </body>
</html>

