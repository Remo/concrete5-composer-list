<?php
defined('C5_EXECUTE') or die('Access Denied.');

$nh = Loader::helper('navigation');
$ih = Loader::helper('concrete/interface');
$fh = Loader::helper('form');
$uh = Loader::helper('concrete/urls');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
    t('%s List', $composerListTitel), t('Shows a list of all pages editable by the composer.'), 'span10 offset1', false);

if ($pages && $displaySearchBox || $emptyList) {
    ?>

    <div class="ccm-pane-options">
        <form class="form-horizontal" action="<?php echo $this->action('show/' . $ctID);?>" method="post">
            <div class="ccm-pane-options-permanent-search">
                <div class="span5">
                    <label for="cvName" class="control-label">Page Name</label>		
                    <div class="controls">
                        <input id="cvName" type="text" name="cvName" value="" style="width: 120px" class="ccm-input-text">		
                    </div>
                    <?php
                    echo $ih->submit(t('Search'), false, 'left', null, array('style' => 'margin-left: 10px;'));
                    ?>
                </div>
                <div class="span4" style="text-align: right;">
                    <a dialog-title="<?php echo t('Customize Columns')?>" 
                       dialog-modal="true"
                       dialog-width="600" 
                       dialog-height="450" 
                       class="composer-list-dialog"
                       href="<?php echo $uh->getToolsURL('customize_columns', 'remo_composer_list')?>?ctID=<?php echo $ctID?>">
                        <span class="ccm-menu-icon ccm-icon-properties"></span>
                        <?php echo t('Customize Columns')?>
                    </a>
                </div>
            </div>     
            <div class="clearfix ccm-pane-options-content"></div>
        </form>
    </div>

<?php }?>

<div class="ccm-pane-body">
    <?php
    if ($pages) {

        $previousParentID = -1;

        foreach ($pages as $page) {

            if ($previousParentID != $page->getCollectionParentID()) {
                $previousParentID = $page->getCollectionParentID();
                $parentPage = Page::getByID($previousParentID);

                if ($previousParentID > -1) {
                    ?>
                </tbody></table>
            <?php }
            ?>
            <table class="table composer-list-sortable">
                <?php if ($ctPublishMethod != 'PARENT') {?>
                    <thead><tr><th colspan="4"><?php echo $parentPage->getCollectionName()?></th></tr></thead>
                    <?php
                }
                ?>
                <tbody>
                    <?php
                }

                $button_edit = $ih->button(t('Edit'), View::url('/dashboard/composer/write/-/edit/', $page->getCollectionID()), '', 'right primary');
                $button_delete = $ih->button(t('Delete'), View::url('/dashboard/composer/list/delete/', $ctID, $page->getCollectionID()), '', 'right', array(
                    'style' => 'margin-left: 10px;',
                    'onclick' => 'return confirm(\'' . t('Are you sure you want to remove this page?') . '\')'
                ));
                ?>
                <tr id="cID-<?php echo $page->getCollectionID()?>">
                    <?php
                    if (is_array($customColumns) && !empty($customColumns)) {
                        foreach ($customColumns as $customColumn) {
                            ?>
                            <td><?php
                                switch ($customColumn) {
                                    case 'sp_pageType':
                                        echo $page->getCollectionTypeName();
                                        break;
                                    case 'sp_pageName':
                                        echo $page->getCollectionName();
                                        break;
                                    case 'sp_pagePath':
                                        echo $page->getCollectionPath();
                                        break;
                                    case 'sp_pageDateCreated':
                                        echo $page->getCollectionDateAdded();
                                        break;
                                    case 'sp_pageDateModified':
                                        echo $page->getCollectionDateLastModified();
                                        break;
                                    case 'sp_pageOwner':
                                        $uID = $page->getCollectionUserID();
                                        if ($uID) {
                                            $ui = UserInfo::getByID($uID);
                                            echo $ui->getUserName();
                                        }
                                        break;
                                    default:
                                        echo $page->getAttribute(substr($customColumn, 3), 'display');
                                        break;
                                }
                                ?>
                            </td><?php
                        }
                    } else {
                        ?>
                        <th style="width: 30%;"><?php echo $page->getCollectionName()?></th>
                        <td style="width: 50%;"><?php echo $page->getCollectionPath()?></td>
                    <?php }
                    ?>
                    <td style="width: 20%; text-align: right;\"><?php echo $button_edit?></td>
                        <td style="text-align: right;width:70px;\"><?php echo $button_delete?></td>
                </tr>
                <?php
            }

            if ($previousParentID > -1) {
                ?>
        </table>
        <?php
    }

    echo $pagesPagination;
} else if ($emptyList) {
    echo t('No pages found.');
} else {
    if (count($composerCollectionTypes) > 0) {
        ?>
        <h3><?php echo t('What type of page would you like to edit?')?></h3>
        <ul class="item-select-list">
            <?php foreach ($composerCollectionTypes as $collectionType) {?>
                <li class="item-select-page">
                    <a href="<?php echo $this->action('show', $collectionType->getCollectionTypeID())?>"><?php echo $collectionType->getCollectionTypeName()?></a></li>
                <?php
            }
            ?>
        </ul>
    <?php } else {
        ?>
        <p><?php echo t('You have not setup any page types for Composer.')?>
        </p>
        <?php
    }
}
?>
</div>

<?php
echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);
