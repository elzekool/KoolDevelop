<?php
/**
 * Tips\View View
 *
 * @author Elze Kool
 * @copyright Elze Kool, Kool Software en Webdevelopment
 *
 * @package SampleApplication
 **/

/* @var $this \View */

$pagination = $this->helper('Pagination');

?>


<?php $this->placeholder('sidebar')->start(); ?>
    <div class="row-fluid">
        <div class="span12">
            <form method="POST" action="tips" class="form-vertical">
                <input name="search" value="<?php echo htmlspecialchars($pagination->getParameter('search')); ?>" type="text" class="span12" placeholder="<?php __w('Search in tips…'); ?>" />
                <button type="submit" class="btn"><i class="icon icon-search"></i> <?php __w('Search'); ?></button>
            </form>            
        </div>
    </div>
<?php $this->placeholder('sidebar')->end(); ?>

<div class="row-fluid">
    <div class="span12">
        <h1><?php echo $page_title ?></h1>
        <p>
            <?php echo htmlspecialchars($tip->getText()); ?>
        </p>
    </div>
</div>
