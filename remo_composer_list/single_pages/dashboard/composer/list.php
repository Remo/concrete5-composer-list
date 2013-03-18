<?php
defined('C5_EXECUTE') or die('Access Denied.');

$nh = Loader::helper('navigation');
$ih = Loader::helper('concrete/interface');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
        t('Composer Page List'), t('Shows a list of all pages editable by the composer.'), 'span10 offset1', false);

if ($pages && $displaySearchBox) {
    ?>

    <div class="ccm-pane-options">
        <form class="form-horizontal" method="post">
            <div class="ccm-pane-options-permanent-search">
                <div class="span5">
                    <label for="cvName" class="control-label">Page Name</label>		
                    <div class="controls">
                        <input id="cvName" type="text" name="cvName" value="" style="width: 120px" class="ccm-input-text">		
                    </div>
                    <?php
                    echo $ih->submit(t('Search'), false, 'left', null, array('style'=>'margin-left: 10px;'));
                    ?>
                </div>
            </div>     
            <div class="clearfix ccm-pane-options-content"></div>
        </form>
    </div>

<?php } ?>

<div class="ccm-pane-body">
    <?php
    if ($pages) {
        
        $previousParentID = -1;
        
        foreach ($pages as $page) {

            if ($previousParentID != $page->getCollectionParentID()) {
                $previousParentID = $page->getCollectionParentID();
                $parentPage = Page::getByID($previousParentID);
                
                if ($previousParentID > -1) {
                    echo '</tbody></table>';
                }
                
                echo "<table class=\"table composer-list-sortable\">";
                if ($ctPublishMethod != 'PARENT') {
                    echo "<thead><tr><th colspan=\"2\">{$parentPage->getCollectionName()}</th></tr></thead>";
                }
                echo "<tbody>";
            }
            
            
            $button = $ih->button(t('Edit'), View::url('/dashboard/composer/write/-/edit/', $page->getCollectionID()), '', 'right primary');
            $button .= $ih->button(t('Delete'), View::url('/dashboard/composer/list/delete/', $ctID, $page->getCollectionID()), '', 'right', array(
                'style' => 'margin-left: 10px;',
                'onclick' => 'return confirm(\'' . t('Are you sure you want to remove this page?') . '\')'
            ));

            echo "
            <tr id=\"cID-{$page->getCollectionID()}\">
                <th style=\"width: 30%;\">{$page->getCollectionName()}</th>
                <td style=\"width: 50%;\">{$page->getCollectionPath()}</td>
                <td style=\"width: 20%; text-align: right;\">{$button}</td>
            </tr>";
        }
        
        if ($previousParentID > -1) {
            echo '</table>';
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