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
        </div>
    </div>
<?php $this->placeholder('sidebar')->end(); ?>

<div class="row-fluid">
    <div class="span12">
        <h1><?php __w('Tips'); ?></h1>
        <p></p>
        <table class="table">
            <thead>
                <tr>
                    <th width="10%"><?php __w('Id'); ?></th>
                    <th width="70%"><?php __w('Title'); ?></th>
                    <th width="20%"><?php __w('Action'); ?></th>
                </tr>
            </thead>
            
            <tbody>
                <?php foreach($paginate_items as $tip) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tip->getId()); ?></td>
                        <td><?php echo htmlspecialchars($tip->getTitle()); ?></td>
                        <td><?php __w('View'); ?></td>
                    </tr>
                
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
