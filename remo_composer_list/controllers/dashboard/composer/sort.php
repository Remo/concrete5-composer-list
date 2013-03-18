<?php

defined('C5_EXECUTE') or die('Access Denied.');

class DashboardComposerSortController extends DashboardBaseController {

    /**
     * Display all page types controlled by the composer
     * 
     * @return type
     */
    public function view($redirect=true) {
        $cap = Loader::helper('concrete/dashboard');
        if (!$cap->canAccessComposer()) return;
        
        $composerCollectionTypes = CollectionType::getComposerPageTypes();
        if (count($composerCollectionTypes) == 1 && $redirect) {
            $ct = $composerCollectionTypes[0];
            $this->redirect('/dashboard/composer/sort/show', $ct->getCollectionTypeID());
            exit;
        }
            
        $this->set('composerCollectionTypes', $composerCollectionTypes);
    }
    
    /**
     * Gets a list of all pages of the collection type
     * specified by $ctID which are grouped by their parent to offer the
     * possibility to sort the pages.
     * 
     * @param int $ctID ID of collection type
     */
    public function show($ctID) {
        
        $this->view(false);
        
        Loader::model('page_list');
        $pl = new PageList();
        $pl->filterByCollectionTypeID($ctID);
                
        $pl->sortByMultiple('p1.cParentID asc', 'p1.cDisplayOrder asc');        
        
        $this->set('ctID', $ctID);
        $this->set('pages', $pl->getPage());
        $this->set('pagesPagination', $pl->displayPaging(false, true));
        
        $hh = Loader::helper('html');
        
        $this->addHeaderItem($hh->css('composer.sort.css', 'remo_composer_list'));
        $this->addHeaderItem($hh->javascript('composer.sort.js', 'remo_composer_list'));
        
    }
    

}