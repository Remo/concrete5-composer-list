<?php

defined('C5_EXECUTE') or die('Access Denied.');

class DashboardComposerListController extends DashboardBaseController {

    /**
     * Display all page types controlled by the composer
     * 
     * @return type
     */
    public function view($redirect = true) {
        $cap = Loader::helper('concrete/dashboard');
        if (!$cap->canAccessComposer())
            return;

        $composerCollectionTypes = CollectionType::getComposerPageTypes();
        if (count($composerCollectionTypes) == 1 && $redirect) {
            $ct = $composerCollectionTypes[0];
            $this->redirect('/dashboard/composer/list/show', $ct->getCollectionTypeID());
            exit;
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
	$hh = Loader::helper('html');
	$th = Loader::helper('text');
	    
        $emptyList = false;
        $this->view(false);

        Loader::model('page_list');
        $pl = new PageList();
        $pl->filterByCollectionTypeID($ctID);

        if (array_key_exists('cvName', $_REQUEST)) {
            $cvName = $th->sanitize($_REQUEST['cvName']);
	    $this->set('cvName', $cvName);
            $pl->filterByName($cvName);
            if (count($pl->getPage()) <= 0) {
                $pl = new PageList();
                $emptyList = true;
            }
        }

        if (!$emptyList) {
            $pl->sortByMultiple('p1.cParentID asc', 'p1.cDisplayOrder asc');
            $pages = $pl->getPage();
        } else {
            $pages = '';
            $this->set('emptyList', t('No entries found'));
        }
        $ct = CollectionType::getByID($ctID);

        // add all necessary header items like JavaScript and CSS files
        if (!array_key_exists('cvName', $_REQUEST) || $cvName == '') {            
            $this->addHeaderItem($hh->css('composer.sort.css', 'remo_composer_list'));
            $this->addHeaderItem($hh->javascript('composer.sort.js', 'remo_composer_list'));
        }
        
        $this->addHeaderItem($hh->javascript('composer.overview.js', 'remo_composer_list'));

        // add variables used by view
        $this->set('customColumns', $this->loadCustomColumns($ctID));
        $this->set('ctID', $ctID);
        $this->set('ctPublishMethod', $ct->getCollectionTypeComposerPublishMethod());
        $this->set('pages', $pages);
        $this->set('displaySearchBox', $this->displaySearchBox());
        $this->set('composerListTitel', $ct->getCollectionTypeName());
        $this->set('pagesPagination', $pl->displayPaging(false, true));
    }

    protected function displaySearchBox() {
        return !defined('SHOW_COMPOSER_LIST_SEARCH_BOX') || SHOW_COMPOSER_LIST_SEARCH_BOX;
    }
    
    /**
     * Puts the custom configuration for a page type into the config table.
     * Needs two request variables, $ctID to reference the correct page type
     * and $selectedAttributes containing all selected attributes
     */
    public function saveCustomColumns() {
        // get existing config
        $selectedColumns = $this->loadCustomColumns();

        // merge new selection for page type into existing variable
        $ctID = $this->post('ctID');        
        $selectedColumns[$ctID] = $_REQUEST['selectedAttributes'];
        
        // save new configuration
        $pkg = Package::getByHandle('remo_composer_list');        
        $pkg->saveConfig('SELECTED_COLUMNS', serialize($selectedColumns));
        
        die();
    }
    
    /**
     * Returns all selected columns for the page type specified by $ctID.
     * In case $ctID is omitted, this function will return all selected
     * columns for all page types.
     * 
     * @param int $ctID
     * @return array
     */
    public function loadCustomColumns($ctID = false) {
        // get existing config
        $pkg = Package::getByHandle('remo_composer_list');        
        $selectedColumns = unserialize($pkg->config('SELECTED_COLUMNS'));
        
        if ($ctID) {
            return $selectedColumns[$ctID];
        }
        return $selectedColumns;
    }

    public function delete($ctID, $cID) {
        $c = Page::getByID($cID);
        $p = new Permissions($c);
        if ($p->canDeletePage()) {
            $this->set('message', t("Page deleted."));
            $c->moveToTrash();
        } else {
            $this->set('message', t("You don't have the right to delete this page!"));
        }
        $this->show($ctID);
    }

}