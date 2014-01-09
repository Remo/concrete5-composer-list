<?php
defined('C5_EXECUTE') or die('Access Denied.');

$fh = Loader::helper('form');

$ctID = intval($_REQUEST['ctID']);

$standardProperties = array(
    'pageType' => t('Page Type'),
    'pageName' => t('Page Name'),
    'pagePath' => t('Path'),
    'pageDateCreated' => t('Date Created'),
    'pageDateModified' => t('Date Modified'),
    'pageOwner' => t('Page Owner'),
);


$pkg = Package::getByHandle('remo_composer_list');
$allSelectedColumns = unserialize($pkg->config('SELECTED_COLUMNS'));
$selectedColumns = $allSelectedColumns[$ctID];
if (!$selectedColumns) {
    $selectedColumns = array();
}

$attributeList = CollectionAttributeKey::getList();
?>
<div class="ccm-ui row-fluid">
    <form>
        <div class="span8" id="remo-composer-list-attributes">
            <h3><?php echo t('Choose Headers')?></h3>

            <div class="clearfix">
                <label><?php echo t('Standard Properties')?></label>
                <div class="input">
                    <ul class="inputs-list">
                        <?php foreach ($standardProperties as $standardPropertyHandle => $standardProperty) {?>
                            <li>
                                <label><?php echo $fh->checkbox('sp_' . $standardPropertyHandle, 1, in_array('sp_' . $standardPropertyHandle, $selectedColumns))?> 
                                    <span><?php echo $standardProperty?></span>
                                </label>
                            </li>
                        <?php }?>
                    </ul>
                </div>
            </div>

            <div class="clearfix">
                <label><?php echo t('Additional Attributes')?></label>
                <div class="input">
                    <ul class="inputs-list">
                        <?php foreach ($attributeList as $attribute) {?>
                            <li>
                                <label>
                                    <?php echo $fh->checkbox('ak_' . $attribute->getAttributeKeyHandle(), 1, in_array('ak_' . $attribute->getAttributeKeyHandle(), $selectedColumns))?>
                                    <span><?php echo $attribute->getAttributeKeyName()?></span>
                                </label>
                            </li>
                        <?php }?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="span4">
            <h3><?php echo t('Column Order')?></h3>
            <ul id="remo-composer-list-attributes-sortable" class="ccm-search-sortable-column-wrapper">
                <?php
                if (is_array($selectedColumns) && !empty($selectedColumns)) {
                    foreach ($selectedColumns as $selectedColumn) {
                        ?>
                        <li id="sort_<?php echo $selectedColumn?>">
                            <?php echo substr($selectedColumn, 0, 2) == 'ak' ? CollectionAttributeKey::getByHandle(substr($selectedColumn, 3))->getAttributeKeyName() : $standardProperties[substr($selectedColumn, 3)];?>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
    </form>
</div>

<div class="dialog-buttons">
    <input type="button" id="remo-composer-list-save" class="btn primary" value="<?php echo t('Save')?>" />
</div>

<script>
    var remo = (function ($) {
        'use strict';
        return {
            composerList: {
                init: function (ctID) {
                    $("#remo-composer-list-attributes input[type=checkbox]").change(this.checkboxStateChanged);
                    $("#remo-composer-list-save").click(this.saveColumns);
                    $("#remo-composer-list-attributes-sortable").sortable({
                        cursor: 'move',
                        opacity: 0.5
                    });
                    remo.composerList.ctID = ctID;
                },
                checkboxStateChanged: function (event) {
                    var attrHandle = $(this).attr("name");
                    var attrName = $(this).next().text();

                    if ($(this).prop("checked")) {
                        var $item = $("<li/>");
                        $item.text(attrName);
                        $item.attr("id", "sort_" + attrHandle);

                        $("#remo-composer-list-attributes-sortable").append($item);
                    }
                    else {
                        $("#remo-composer-list-attributes-sortable #sort_" + attrHandle).remove();
                    }
                },
                saveColumns: function () {
                    var selectedAttributes = [];
                    $("#remo-composer-list-attributes-sortable li").each(function (e, v) {
                        selectedAttributes.push($(this).attr("id").substr(5));

                    });
                    $.post('<?php echo View::url('/dashboard/composer/list/saveCustomColumns')?>',
                       {
                           "selectedAttributes": selectedAttributes,
                           "ctID": remo.composerList.ctID
                       }, function (data) {
                        $.fn.dialog.closeTop();
                    });
                }
            }
        }
    })($);

    remo.composerList.init(<?php echo intval($ctID)?>);
</script>