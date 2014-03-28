<div class="ccm-ui">
    <?php
    defined('C5_EXECUTE') or die(_("Access Denied."));

    $db = Loader::db();
    $page = Page::getCurrentPage();
    Loader::packageElement('page_attributes', 'remo_composer_list', array('page' => $page));
    ?>
</div>
