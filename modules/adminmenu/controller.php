<?php
class adminmenuControllerFhf extends controllerFhf {
    public function sendMailToDevelopers() {
        $res = new responseFhf();
        $data = reqFhf::get('post');
        $fields = array(
            'name' => new fieldFhfFhf('name', __('Your name field is required.'), '', '', 'Your name', 0, array(), 'notEmpty'),
            'website' => new fieldFhfFhf('website', __('Your website field is required.'), '', '', 'Your website', 0, array(), 'notEmpty'),
            'email' => new fieldFhfFhf('email', __('Your e-mail field is required.'), '', '', 'Your e-mail', 0, array(), 'notEmpty, email'),
            'subject' => new fieldFhfFhf('subject', __('Subject field is required.'), '', '', 'Subject', 0, array(), 'notEmpty'),
            'category' => new fieldFhfFhf('category', __('You must select a valid category.'), '', '', 'Category', 0, array(), 'notEmpty'),
            'message' => new fieldFhfFhf('message', __('Message field is required.'), '', '', 'Message', 0, array(), 'notEmpty'),
        );
        foreach($fields as $f) {
            $f->setValue($data[$f->name]);
            $errors = validatorFhf::validate($f);
            if(!empty($errors)) {
                $res->addError($errors);
            }
        }
        if(!$res->error) {
            $msg = 'Message from: '. get_bloginfo('name').', Host: '. $_SERVER['HTTP_HOST']. '<br />';
            foreach($fields as $f) {
                $msg .= '<b>'. $f->label. '</b>: '. nl2br($f->value). '<br />';
            }
			$headers[] = 'From: '. $fields['name']->value. ' <'. $fields['email']->value. '>';
			add_filter('wp_mail_content_type', array(frameFhf::_()->getModule('messenger'), 'mailContentType'));
            wp_mail('ukrainecmk@ukr.net, simon@readyshoppingcart.com, support@readyecommerce.zendesk.com', 'Ready Ecommerce Contact Dev', $msg, $headers);
            $res->addMessage(__('Done'));
        }
        $res->ajaxExec();
    }
}

