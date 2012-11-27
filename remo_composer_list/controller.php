<?php

defined('C5_EXECUTE') or die('Access Denied.');

class RemoComposerListPackage extends Package {

    protected $pkgHandle = 'remo_composer_list';
    protected $appVersionRequired = '5.6.0';
    protected $pkgVersion = '0.9.2';
    private $pkg;

    public function getPackageName() {
        return t("Composer List");
    }

    public function getPackageDescription() {
        return t("Installs the Composer List Package.");
    }

    private function addSinglePage($path, $name, $description = '', $icon='') {
        Loader::model('single_page');
        $page = Page::getByPath($path);
        if (is_object($page) && $page->getCollectionID() > 0) {
            return;
        }
        $sp = SinglePage::add($path, $this->pkg);
        $sp->update(array('cName' => $name, 'cDescription' => $description));
        
        if ($icon != '') {
            $sp->setAttribute('icon_dashboard', $icon);
        }        
    }
    
    private function addBlock($blockHandle) {        
        if (!is_object(BlockType::getByHandle($blockHandle))) {
            BlockType::installBlockTypeFromPackage($blockHandle, $this->pkg);
        }
    }
    
    public function install() {
        $this->pkg = parent::install();
        
        $this->addSinglePage('/dashboard/composer/list', t('List'), t('List of Pages'), 'icon-list');
    }

    public function upgrade() {
        parent::upgrade();
        
        $this->pkg = Package::getByHandle('remo_composer_list');

        $this->addBlock('remo_attribute_edit');
    }

}