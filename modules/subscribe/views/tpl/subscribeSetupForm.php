<p>
	<label for="<?php echo $this->widget->get_field_id('subscr_form_title')?>"><?php lang::_e('Subscribe form title')?>:</label><br />
    <?php 
        echo html::text($this->widget->get_field_name('subscr_form_title'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('subscr_form_title'). '"', 
            'value' => $this->data['subscr_form_title']));
    ?><br />
    <label for="<?php echo $this->widget->get_field_id('subscr_enter_email_msg')?>"><?php lang::_e('"Enter Email" message for your subscribe form')?>:</label><br />
    <?php 
        echo html::text($this->widget->get_field_name('subscr_enter_email_msg'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('subscr_enter_email_msg'). '"', 
            'value' => $this->data['subscr_enter_email_msg']));
    ?><br />
	<label for="<?php echo $this->widget->get_field_id('subscr_success_msg')?>"><?php lang::_e('Message that user will see after subscribe')?>:</label><br />
    <?php 
        echo html::text($this->widget->get_field_name('subscr_success_msg'), array(
            'attrs' => 'id="'. $this->widget->get_field_id('subscr_success_msg'). '"', 
            'value' => $this->data['subscr_success_msg']));
    ?><br />
	<label for="<?php echo $this->widget->get_field_id('list')?>"><?php lang::_e('Lists where subscribers should be added')?>:</label><br />
    <?php 
		if(!empty($this->allLists)) {
			if(!isset($this->data['list']) || !$this->data['list'])
				$this->data['list'] = array();
			foreach($this->allLists as $list) {
				echo '<label>';
				echo html::checkbox($this->widget->get_field_name('list'). '[]', array(
					'value' => $list['id'],
					'checked' => in_array($list['id'], $this->data['list'])
				));
				echo '&nbsp;';
				echo $list['label'];
				echo '</label><br />';
			}
		}
    ?><br />
	
</p>