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

$pagination = $this->helper('Pagination');

?>


<?php $this->placeholder('sidebar')->start(); ?>
    <div class="row-fluid">
        <div class="span12">
            <form method="POST" action="tips" class="form-vertical">
                <input name="search" value="<?php echo htmlspecialchars($pagination->getParameter('search')); ?>" type="text" class="span12" placeholder="<?php __w('Search in tips…'); ?>" />
                <button type="submit" class="btn"><i class="icon icon-search"></i> <?php __w('Search'); ?></button>
                <a href="<?php echo $pagination->getLink(array('search' => null)); ?>" class="btn"><i class="icon icon-refresh"></i> <?php __w('Reset'); ?></a>
            </form>            
        </div>
    </div>
<?php $this->placeholder('sidebar')->end(); ?>

<div class="row-fluid">
    <div class="span12">
        <h1><?php __w('Tips'); ?></h1>
        
        <ul class="pagination">
            <li <?php if ($paginate_page == 0) : ?>class="disabled"<?php endif; ?>>
                <a href="<?php echo $pagination->getLink(array('page' => 0)); ?>">«</a>
            </li>
            <?php foreach($pagination->getPageNumbers() as $page) : ?>
                <li <?php if ($page == $paginate_page) : ?>class="active"<?php endif; ?>>
                    <a href="<?php echo $pagination->getLink(array('page' => $page)); ?>"><?php echo $page+1; ?></a>
                </li>
            <?php endforeach; ?>    
            <li <?php if ($paginate_page == ($paginate_pages-1)) : ?>class="disabled"<?php endif; ?>>
                <a href="<?php echo $pagination->getLink(array('page' => $paginate_pages - 1)); ?>">»</a>
            </li>
        </ul>

        <p></p>
        
        <table class="table">
            <thead>
                <tr>
                    <th width="10%"><a href="<?php echo $pagination->getSortLink('tips.id', 'ASC'); ?>"><?php __w('#'); ?></a></th>
                    <th width="40%"><a href="<?php echo $pagination->getSortLink('tips.title', 'ASC'); ?>"><?php __w('Title'); ?></a></th>
                    <th width="40%"><a href="<?php echo $pagination->getSortLink('categories.title', 'ASC'); ?>"><?php __w('Category'); ?></a></th>
                    <th width="10%"><?php __w('Action'); ?></th>
                </tr>
            </thead>
            
            <tbody>
                <?php foreach($paginate_items as $tip) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tip->getId()); ?></td>
                        <td><?php echo htmlspecialchars($tip->getTitle()); ?></td>
                        <td><?php echo htmlspecialchars($tip->getCategory()->getTitle()); ?></td>
                        <td>
                            <a href="tip/<?php echo $tip->getId(); ?>">
                                <?php __w('View'); ?>
                            </a>
                        </td>
                    </tr>
                
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
