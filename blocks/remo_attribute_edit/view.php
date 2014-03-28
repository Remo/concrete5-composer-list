<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>
<div class="remo-composer-list-attributes">
    <?php
    $page = Page::getCurrentPage();

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

        echo '<div class="remo-composer-list-attribute">';
        echo $page->getAttribute($ak->getAttributeKeyHandle());
        echo '</div>';
    }
    ?>
</div>
