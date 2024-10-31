<?php
class stpl_additionsModelFhf extends modelFhf {
	public function getMenusList() {
		return get_terms('nav_menu');
	}
}
