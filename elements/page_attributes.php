<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

$category = AttributeKeyCategory::getByHandle('collection');
$sets = $category->getAttributeSets();
$attribs = array();

$requiredKeys = array();
$usedKeys = array();
if ($page->getCollectionTypeID() > 0 && !$page->isMasterCollection()) {
    $cto = CollectionType::getByID($page->getCollectionTypeID());
    $aks = $cto->getAvailableAttributeKeys();
    foreach ($aks as $ak) {
        $requiredKeys[] = $ak->getAttributeKeyID();
    }
}
$setAttribs = $page->getSetCollectionAttributes();
foreach ($setAttribs as $ak) {
    $usedKeys[] = $ak->getAttributeKeyID();
}
$usedKeysCombined = array_merge($requiredKeys, $usedKeys);

foreach ($sets as $as) {
    $setattribs = $as->getAttributeKeys();
    foreach ($setattribs as $ak) {

        $attribs[] = $ak;
    }
}
$unsetattribs = $category->getUnassignedAttributeKeys();
foreach ($unsetattribs as $ak) {
    $attribs[] = $ak;
}

foreach ($attribs as $ak) {

    $caValue = $page->getAttributeValueObject($ak);


    if (!in_array($ak->getAttributeKeyID(), $usedKeysCombined)) {
        continue;
    }
    ?>

    <div class="remo-page-list-form-element">
        <div class="remo-page-list-well" id="ak<?php echo $ak->getAttributeKeyID() ?>">
            <input type="hidden" class="ccm-meta-field-selected" id="ccm-meta-field-selected<?php echo $ak->getAttributeKeyID() ?>" name="selectedAKIDs[]" value="<?php if (!in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?>0<?php } else { ?><?php echo $ak->getAttributeKeyID() ?><?php } ?>" />

            <a href="javascript:void(0)" class="ccm-meta-close" ccm-meta-name="<?php echo $ak->getAttributeKeyName() ?>" id="ccm-remove-field-ak<?php echo $ak->getAttributeKeyID() ?>" style="display:<?php echo (!in_array($ak->getAttributeKeyID(), $requiredKeys)) ? 'block' : 'none' ?>"><img src="<?php echo ASSETS_URL_IMAGES ?>/icons/remove_minus.png" width="16" height="16" alt="<?php echo t('remove') ?>" /></a>

            <label><?php echo $ak->getAttributeKeyName() ?></label>
            <?php echo $ak->render('form', $caValue); ?>
        </div>
    </div>

    <?php
}
