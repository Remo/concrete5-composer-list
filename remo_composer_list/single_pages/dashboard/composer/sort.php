<?php
defined('C5_EXECUTE') or die('Access Denied.');

$nh = Loader::helper('navigation');
$ih = Loader::helper('concrete/interface');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
        t('Composer Page Order'), t('Shows a sortable list of all pages controlled by the composer.'), 'span10 offset1', false);
?>

<div class="ccm-pane-body">
    <?php
    if ($pages) {
        
        $previousParentID = -1;        
                
        foreach ($pages as $page) {
            
            if ($previousParentID != $page->getCollectionParentID()) {
                $previousParentID = $page->getCollectionParentID();
                $parentPage = Page::getByID($previousParentID);
                
                if ($previousParentID > -1) {
                    echo '</ul>';
                }
                
                echo "<h3>{$parentPage->getCollectionName()}</h3><ul class=\"composer-list-sortable\">";
            }
            
            echo "<li id=\"cID-{$page->getCollectionID()}\">{$pp}{$page->getCollectionName()} ({$page->getCollectionPath()})</li>";
        }
        if ($previousParentID > -1) {
            echo '</ul>';
        }

        echo $pagesPagination;
    } else {
        if (count($composerCollectionTypes) > 0) {
            echo '<h3>' . t('What type of page would you like to edit?') . '</h3>';
            echo '<ul class="item-select-list">';

            foreach ($composerCollectionTypes as $collectionType) {
                echo '<li class="item-select-page"><a href="' .
                $this->action('show', $collectionType->getCollectionTypeID()) . '">' . $collectionType->getCollectionTypeName() . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>' . t('You have not setup any page types for Composer.') . '</p>';
        }
    }
    ?>
</div>

<?php
echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);