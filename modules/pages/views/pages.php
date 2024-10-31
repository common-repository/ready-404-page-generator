<?php
class pagesViewFhf extends viewFhf {
    public function displayDeactivatePage() {
        $this->assign('GET', reqFhf::get('get'));
        $this->assign('POST', reqFhf::get('post'));
        $this->assign('REQUEST_METHOD', strtoupper(reqFhf::getVar('REQUEST_METHOD', 'server')));
        $this->assign('REQUEST_URI', basename(reqFhf::getVar('REQUEST_URI', 'server')));
        parent::display('deactivatePage');
    }
}

