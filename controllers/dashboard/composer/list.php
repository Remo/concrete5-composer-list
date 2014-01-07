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
        $emptyList = false;
        $this->view(false);

        Loader::model('page_list');
        $pl = new PageList();
        $pl->filterByCollectionTypeID($ctID);

        if (array_key_exists('cvName', $_REQUEST)) {
            $cvName = Loader::helper('text')->sanitize($_REQUEST['cvName']);
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

        if (!array_key_exists('cvName', $_REQUEST) || $cvName == '') {
            $hh = Loader::helper('html');
            $this->addHeaderItem($hh->css('composer.sort.css', 'remo_composer_list'));
            $this->addHeaderItem($hh->javascript('composer.sort.js', 'remo_composer_list'));
        }

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