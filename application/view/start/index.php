<?php
/**
 * Start\Index View
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/

/* @var $this \View */
?>


<?php $this->placeholder('sidebar')->start(); ?>
    <div class="row-fluid">
        <div class="span12">
            <form method="POST" action="" class="form-vertical">
                <input type="text" class="span12" placeholder="<?php __w('Search in tips…'); ?>" />
            </form>

            <div class="nav">
                <h2><i class="icon icon-globe"></i> <?php __w('Tips'); ?></h2>
                <ul>
                    <li><a href="/tips/category:database"><?php __w('Database'); ?></a></li>
                    <li><a href="/tips/category:security"><?php __w('Security'); ?></a></li>
                    <li><a href="/tips/category:mvc"><?php __w('Model-View-Controler'); ?></a></li>
                </ul>
            </div>
        </div>
    </div>
<?php $this->placeholder('sidebar')->end(); ?>

<div class="row-fluid">
    <div class="span12">
        <p>&nbsp;</p>
        <h1><?php __w('KoolDevelop demonstration website'); ?></h1>
        <p><?php __w('This simple application demonstrates the use of the KoolDevelop framework. Go trough the code to learn it quickly.'); ?></p>
        
        <a class="tile" href="tips">
            <i class="icon icon-cogs"></i> 
            <?php __w('Tips'); ?>
        </a>
        
        &nbsp;
        
        <a class="tile" href="api/tree.html" target="_blank">
            <i class="icon icon-download"></i> 
            <?php __w('API'); ?>
        </a>
        
        &nbsp;
        
        <a class="tile" href="http://www.kooldevelopment.nl">
            <i class="icon icon-user"></i> 
            <?php __w('Author'); ?>
        </a>
        
        
        <p>&nbsp;</p>
    </div>    
</div>


<div class="row-fluid">
    <div class="span4">
        <h2><i class="icon icon-align-right"></i> <?php __w('Namespace based'); ?></h2>
        <p><?php __w('The framework utilizes PHP namespaces fully, allowing easy inclusion of external libraries. It has an easy to configure AutoLoader for internal files and a PSR-0 compatible autoloader for external dependancies.'); ?></p>        
    </div>    

    <div class="span4">
        <h2><i class="icon icon-folder-open"></i> <?php __w('Model-View-Controller'); ?></h2>
        <p><?php __w('The framework has a clear usage of the Model-View-Controller pattern helping you to write clean, understandible code. All parts of this pattern are loosly coupled.'); ?></p>
        <pre><?php
            echo "\$this->View->setTitle('Demonstration');\n";
            echo "\$this->View->setView('demo/index');\n";
            echo "\$this->View->render();\n";
        ?></pre>        
    </div>    
    
    <div class="span4">
        <h2><i class="icon icon-eye-open"></i> <?php __w('No tricks'); ?></h2>
        <p><?php __w('The framework plays no tricks, it helps you where it can, but it doesn\'t do much without you telling it to. This prevents high loads, bloated applications and unexpected/untraceable problems.'); ?></p>                
    </div>
</div>

<div class="row-fluid">
    <div class="span4">
        <h2><i class="icon icon-fire"></i> <?php __w('Fluent database adaptor'); ?></h2>
        <p><?php __w('The framework includes a lightweight Fluent database adaptor for SQL databases utilizing prepared statements for speed and security.'); ?></p>        
        <pre><?php
            echo "\KoolDevelop\Database\Adaptor::getInstance()->newQuery()\n";
            echo "  ->select('*')\n";
            echo "  ->from('demo')\n";
            echo "  ->where('id = ?', \$id)\n";
            echo "  ->execute();"
        ?></pre>
    </div>    

    <div class="span4">
        <h2><i class="icon icon-cog"></i> <?php __w('Pure PHP views'); ?></h2>
        <p><?php __w('No templating engine that adds yet another layer of obfuscation but pure PHP view files with some powerfull utilties like Elements (reusable mini-views), Helpers (simple to load view libraries) and Placeholders'); ?></p>
        <pre><?php
            echo "// " . htmlspecialchars(__('In layout or in a view/element')) . "\n";
            echo htmlspecialchars("<?php echo \$this->placeholder('sidebar'); ?>\n");
            echo "\n";
            echo htmlspecialchars("<?php \$this->placeholder('sidebar')->start(); ?>\n");
            echo htmlspecialchars("  <div class=\"sidebar\">\n");
            echo "    &hellip;\n";
            echo htmlspecialchars("  </div>\n");
            echo htmlspecialchars("<?php \$this->placeholder('sidebar')->stop(); ?>\n");            
            
        ?></pre>
    </div>    
    
    <div class="span4">
        <h2><i class="icon icon-edit"></i> <?php __w('Easy configuration'); ?></h2>
        <p><?php __w('No large amount of configuration files to edit. Every major part of the framework has it\'s own .ini file. You only need to configure what you use.'); ?></p>        
        <pre><?php
            echo "[errors]\n";
            echo "display_errors=1\n";
            echo "error_reporting=E_ALL & ~(E_DEPRECATED)\n";
            echo "display_stacktrace=1\n";
            echo "display_details=1"
        ?></pre>
    </div>
</div>


