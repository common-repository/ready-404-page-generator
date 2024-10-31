<?php
class logViewFhf extends viewFhf {
    public function getList() {
        $this->assign('logs', frameFhf::_()->getModule('logFhf')->getModel()->getSorted());
        $this->assign('logTypes', frameFhf::_()->getModule('logFhf')->getModel()->getTypes());
        parent::display('logList');
    }
}