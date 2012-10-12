<?php

defined('C5_EXECUTE') or die('Access Denied.');

class DashboardComposerListController extends DashboardBaseController {

    /**
     * Display all page types controlled by the composer
     * 
     * @return type
     */
    public function view() {
        $cap = Loader::helper('concrete/dashboard');
        if (!$cap->canAccessComposer()) return;
        
        $collectionTypes = CollectionType::getList();
        
        $composerCollectionTypes = array();
        foreach ($collectionTypes as $collectionType) {
            $collectionType->setComposerProperties();
            if ($collectionType->isCollectionTypeIncludedInComposer() == 1) {
                $composerCollectionTypes[] = $collectionType;
            }
        }
        $this->set('composerCollectionTypes', $composerCollectionTypes);
    }
    
    /**
     * Gets a list of all pages of the collection type
     * specified by $ctID
     * 
     * @param int $ctID ID of collection type
     */
    public function show($ctID) {
        
        $this->view();
        
        Loader::model('page_list');
        $pl = new PageList();
        $pl->filterByCollectionTypeID($ctID);
        
        $this->set('pages', $pl->getPage());
        $this->set('pagesPagination', $pl->displayPaging(false, true));
        
    }

}