<?php

defined('C5_EXECUTE') or die("Access Denied.");

class RemoAttributeEditBlockController extends BlockController {

    protected $btInterfaceWidth = 600;
    protected $btInterfaceHeight = 500;
    protected $btTable = 'btRemoAttributeEdit';
    private $attributes = array();

    /**
     * Used for localization. If we want to localize the name/description we have to include this
     */
    public function getBlockTypeDescription() {
        return t("Adds a blocks which allows you to edit the page attributes.");
    }

    public function getBlockTypeName() {
        return t("Page Attributes");
    }

    public function view() {

    }

    public function save($data) {
        //parent::save($data);

        $db = Loader::db();

        $page = Page::getCurrentPage();        
        $cID = $page->getCollectionID();

        $page = Page::getByID($cID);
        $page->update(array('cName' => $_REQUEST['collectionName']));

        $collectionAttributes = CollectionAttributeKey::getList();

        foreach ($collectionAttributes as $collectionAttribute) {
            if (array_key_exists($collectionAttribute->akID, $_REQUEST['akID'])) {
                $collectionAttribute->setAttribute($page, false);
            }
        }
    }

}
