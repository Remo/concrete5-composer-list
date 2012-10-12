<?php

defined('C5_EXECUTE') or die('Access Denied.');

echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
   t('Composer Page List'),
   t('Shows a list of all pages editable by the composer.'),
   'span10 offset1',
   false);
?>
<div class="ccm-pane-options">
<?php
$nh = Loader::helper('navigation');
$ih = Loader::helper('concrete/interface');

foreach ($composerCollectionTypes as $collectionType) { 
    echo $ih->button($collectionType->getCollectionTypeName(), $this->action('show', $collectionType->getCollectionTypeID()), 'left', '', array('style'=>'margin-right: 10px;'));
}

?>
</div>

<div class="ccm-pane-body">
<?php
if ($pages) {
    echo '<table class="table">';
    foreach ($pages as $page) {
        
        $button = $ih->button(t('Edit'), View::url('/dashboard/composer/write/-/edit/', $page->getCollectionID()));
                
        echo "
            <tr>
                <th>{$page->getCollectionName()}</th>
                <td>{$button}</td>
            </tr>";
    }
    echo '</table>';

    echo $pagesPagination;
}
?>
</div>

<?php



echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);