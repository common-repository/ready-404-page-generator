<?php
class mailFhf extends moduleFhf {
	public function send($to, $subject, $message, $fromName = '', $fromEmail = '', $replyToName = '', $replyToEmail = '', $additionalHeaders = null, $additionalParameters = null) {
		$headersArr = array();
		$eol = "\r\n";
        if(!empty($fromName) && !empty($fromEmail)) {
            $headersArr[] = 'From: '. $fromName. ' <'. $fromEmail. '>';
        }
		if(!empty($replyToName) && !empty($replyToEmail)) {
            $headersArr[] = 'Reply-To: '. $replyToName. ' <'. $replyToEmail. '>';
        }
		if(!function_exists('wp_mail'))
			frameFhf::_()->loadPlugins();
		add_filter('wp_mail_content_type', array($this, 'mailContentType'));

        $result = wp_mail($to, $subject, $message, implode($eol, $headersArr));
		remove_filter('wp_mail_content_type', array($this, 'mailContentType'));
		
		frameFhf::_()->getModule('log')->getModel()->post(array(
            'type' => 'email',
            'data' => array(
                'to' => $to,
                'subject' => $subject,
                'headers' => htmlspecialchars(implode($eol, $headersArr)),
                'message' => $message,
                'result' => $result ? FHF_SUCCESS : FHF_FAILED,
            ),
        ));
		 
		return $result;
	}
	public function mailContentType($contentType) {
		$contentType = 'text/html';
        return $contentType;
	}
}