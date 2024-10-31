<?php
class logModelFhf extends modelFhf {
    protected $_types = array(
        'email' => array('label' => 'Emails'),         //Let's it have an array as value to be able determine some params in future
		'order' => array('label' => 'Orders'),
    );
    public function post($d = array()) {
        if(frameFhf::_()->getModule('options')->get('enable_log_actions')) {
            switch($d['type']) {
                case 'email':
                    if(is_array($d['data'])) {
                        foreach($d['data'] as $k => $v) {
                            $d['data'][$k] = stripslashes($d['data'][$k]);
                        }
                    }
                    $d['data'] = utilsFhf::serialize($d['data']);
                    break;
				case 'order':
					$d['data'] = stripslashes($d['data']);
					break;
            }
            $d['date_created'] = time();
			if(!isset($d['uid'])) {
				$d['uid'] = frameFhf::_()->getModule('user')->getCurrentID();
			}
            return frameFhf::_()->getTable('log')->insert($d);
        }
        return false;
    }
    public function get($d = array()) {
		global $wpdb;
		$logAl = frameFhf::_()->getTable('log')->alias();
        frameFhf::_()->getTable('log')->orderBy('id DESC');
		frameFhf::_()->getTable('log')->arbitraryJoin('LEFT JOIN '. $wpdb->users. ' ON '. $wpdb->users. '.ID = '. $logAl. '.uid');
        $dataFromDb = frameFhf::_()->getTable('log')->get($logAl. '.*, '. $wpdb->users. '.user_nicename', $d);
        if(!empty($dataFromDb)) {
            foreach($dataFromDb as $i => $d) {
                $dataFromDb[ $i ] = $this->_adaptFromDb( $dataFromDb[ $i ] );
            }
        }
        return $dataFromDb;
    }
    public function getSorted($d = array()) {
        $dataFromDb = $this->get($d);
        $sortedData = array();
        if(!empty($dataFromDb)) {
            foreach($dataFromDb as $d) {
                $sortedData[$d['type']][$d['id']]= $d;
            }
        }
        return $sortedData;
    }
    protected function _adaptFromDb($d = array()) {
        switch($d['type']) {
            case 'email':
                $d['data'] = utilsFhf::unserialize($d['data']);
                break;
        }
        return $d;
    }
    public function getTypes() {
        return $this->_types;
    }
}