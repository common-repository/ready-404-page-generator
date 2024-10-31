<?php
class stplControllerFhf extends controllerFhf {
	public function load() {
		$res = new responseFhf();
		if($html = $this->getView()->load(reqFhf::get('post'))) {
			$res->setHtml($html);
			$id = (int)  reqFhf::getVar('id');
			if($id) {
				$res->addData('stpl', $this->getModel()->getById( $id ));
			}
		} else
			$res->pushError ($this->getView()->getErrors());
		return $res->ajaxExec();
	}
	public function preview() {
		$canPreview = false;
		if(frameFhf::_()->getModule('user')->isAdmin()) {
			$canPreview = true;
		} else {
			$email = reqFhf::getVar('email');
			$token = reqFhf::getVar('token');
			if(!empty($email) && !empty($token)) {
				$subscriber = frameFhf::_()->getModule('subscribe')->getModel()->getSuscriberByEmailToken($email, $token, true);
				if(!empty($subscriber)) {
					$canPreview = true;
				}
			}
		}
		if($canPreview) {
			$id = (int) reqFhf::getVar('stpl_id');
			if(!$id)
				$id = (int) reqFhf::getVar('id');
			if($id) {
				$options = reqFhf::get('get');
				if(!is_array($options))
					$options = array();
				$options['preview'] = true;
				echo $this->getView()->generateContent( $id, $options );
			}
		}
		exit();
	}
	public function save() {
		$res = new responseFhf();
		$stpl = reqFhf::getVar('stpl');
		if($id = $this->getModel()->save( $stpl )) {
			$stpl = $this->getModel()->getById($id);
			$res->addData('stpl', $stpl);
			$res->addMessage(__('Saved'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	// This is for developers only - it will generate queries for installation
	public function generateInstallQueries() {
		$reset = reqFhf::getVar('reset');
		function generateOut($tbl, $data) {
			$eol = "\n";
			$query = 'dbFhf::query("INSERT INTO `@__'. $tbl. '` (`'. implode('`, `', array_keys($data[0])). '`) VALUES ';
			$valuesArr = array();
			foreach($data as $d) {
				$values = array_map('mysql_real_escape_string', $d);
				
				$valuesArr[] = '(\''. implode('\', \'', $values). '\')';
			}
			$query .= $eol. implode(','. $eol, $valuesArr). ';");'. $eol;
			$query = str_replace('\0000a0', '0000a0', $query);
			echo $query;
		}
		if($reset) {
			$removeTill = (int) reqFhf::getVar('remove_till');
			$removeExact = (int) reqFhf::getVar('remove_exact');
			if($removeTill || $removeExact) {
				$removeIndex = $removeTill ? $removeTill : $removeExact;
				dbFhf::query('DELETE FROM @__stpl WHERE id '. ($removeTill ? '<' : '='). ' '. $removeIndex);
				$rows = dbFhf::get('SELECT * FROM @__stpl_rows');
				$rowsIdsToRemove = array();
				foreach($rows as $row) {
					if(($removeTill && (int)$row['stpl_id'] < $removeTill) 
						|| ($removeExact && (int)$row['stpl_id'] == $removeExact)
					) {
						$rowsIdsToRemove[] = $row['id'];
					}
				}
				if(!empty($rowsIdsToRemove)) {
					dbFhf::query('DELETE FROM @__stpl_rows WHERE id IN ('. implode(', ', $rowsIdsToRemove). ')');
					dbFhf::query('DELETE FROM @__stpl_cols WHERE stpl_row_id IN ('. implode(', ', $rowsIdsToRemove). ')');
				}
			}
			$stpls = dbFhf::get('SELECT * FROM @__stpl');
			$rows = dbFhf::get('SELECT * FROM @__stpl_rows');
			$cols = dbFhf::get('SELECT * FROM @__stpl_cols');
			$i = 1;
			foreach($stpls as $stpl) {
				dbFhf::query('UPDATE @__stpl SET id = '. $i. ', protected = 1 WHERE id = '. $stpl['id']);
				dbFhf::query('UPDATE @__stpl_rows SET stpl_id = '. $i. ' WHERE stpl_id = '. $stpl['id']);
				$j = 1;
				foreach($rows as $row) {
					dbFhf::query('UPDATE @__stpl_rows SET id = '. $j. ' WHERE id = '. $row['id']);
					dbFhf::query('UPDATE @__stpl_cols SET stpl_row_id = '. $j. ' WHERE stpl_row_id = '. $row['id']);
					$k = 1;
					foreach($cols as $col) {
						dbFhf::query('UPDATE @__stpl_cols SET id = '. $k. ' WHERE id = '. $col['id']);
						$k++;
					}
					$j++;
				}
				$i++;
			}
		}
		function softReplaceIds(&$array, $key, $fromTo = array()) {
			$newFromTo = array();
			foreach($array as $i => $val) {
				$newId = empty($fromTo) ? ($i + 1) : $fromTo[ $array[ $i ][ $key ] ];
				$newFromTo[ $array[ $i ][ $key ] ] = $newId;
				$array[ $i ][ $key ] = $newId;
			}
			return $newFromTo;
		}
		$stplIdsStr = '';
		$softReset = reqFhf::getVar('soft_reset');
		$stplIds = reqFhf::getVar('stpl_ids');
		if(!empty($stplIds) && !is_array($stplIds))
			$stplIds = explode(',', $stplIds);
		if(!empty($stplIds))
			$stplIdsStr = implode(',', $stplIds);
		$stplWhere = empty($stplIds) ? '' : ' WHERE id IN ('. $stplIdsStr. ') ORDER BY FIELD(id, '. $stplIdsStr. ')';
		$stpls = dbFhf::get('SELECT * FROM @__stpl'. $stplWhere);
		$rowsStplIdIn = array();
		foreach($stpls as $stpl) {
			$rowsStplIdIn[] = $stpl['id'];
		}
		$rows = dbFhf::get('SELECT * FROM @__stpl_rows WHERE stpl_id IN ('. implode(',', $rowsStplIdIn). ')');
		$colsRowsIds = array();
		foreach($rows as $row) {
			$colsRowsIds[] = $row['id'];
		}
		$cols = dbFhf::get('SELECT * FROM @__stpl_cols WHERE stpl_row_id IN ('. implode(',', $colsRowsIds). ')');
		if(!empty($cols)) {
			$invalidContents = array();
			$invalidOutCols = array(
				'newsletter_id' => 'Newsletter ID',
				'stpl_id' => 'Stpl ID',
				'row_id' => 'Row ID',
				'col_id' => 'Col ID',
				'error' => 'Error',
				'content' => 'Content',
			);
			foreach($cols as $col) {
				$error = '';
				if(strpos($col['content'], $_SERVER['HTTP_HOST']) !== false)
					$error = 'Host in some path detected';
				if((strpos($col['content'], 'href="http') !== false && !preg_match('/facebook|twitter/i', $col['content'])))
					$error = 'Direct link HREF with HTTP detected';
				if(strpos($col['content'], 'srs="http') !== false)
					$error = 'Direct SRC with HTTP detected';
				if(strpos($col['content'], 'wp-content/uploads') !== false)
					$error = 'Link to wp-content/uploads';
				if(!empty($error)) {
					$invalidInfo = array('content' => $col['content']);
					foreach($rows as $row) {
						if($row['id'] == $col['stpl_row_id']) {
							$invalidInfo['row_id'] = $row['id'];
							$invalidInfo['stpl_id'] = $row['stpl_id'];
							$invalidInfo['col_id'] = $col['id'];
							$invalidInfo['error'] = $error;
							$invalidInfo['newsletter_id'] = dbFhf::get('SELECT id FROM @__newsletters WHERE stpl_id = '. $row['stpl_id'], 'one');
							if(empty($invalidInfo['newsletter_id']))
								$invalidInfo['newsletter_id'] = 'No Newsletter';
							break;
						}
					}
					$invalidContents[] = $invalidInfo;
				}
			}
			if(!empty($invalidContents)) {
				$invalidOut = '<b style="color: red;">You have some errors in your templates, please see info bellow, fix this - and try again:</b><br />';
				$invalidOut .= '<table width="100%" border="1"><tr>';
				foreach($invalidOutCols as $colKey => $colLabel) {
					$invalidOut .= '<td>'. $colLabel. '</td>';
				}
				$invalidOut .= '</tr>';
				$invalidColIds = array();
				foreach($invalidContents as $invalidContent) {
					$invalidOut .= '<tr>';
					foreach($invalidOutCols as $colKey => $colLabel) {
						$invalidOut .= '<td>'. $invalidContent[$colKey]. '</td>';
					}
					$invalidOut .= '</tr>';
					$invalidColIds[] = $invalidContent['col_id'];
				}
				$invalidOut .= '</table><br />';
				$invalidOut .= 'Query for select invalid cols:<br />'. dbFhf::prepareQuery('SELECT * FROM @__stpl_cols WHERE id IN ('. implode(', ', $invalidColIds).')');
				echo $invalidOut;
				exit();
			}
		}
		if($softReset) {
			$stplFromTo = softReplaceIds($stpls, 'id');
			$rowsFromTo = softReplaceIds($rows, 'id');
			$colsFromTo = softReplaceIds($cols, 'id');
			
			softReplaceIds($rows, 'stpl_id', $stplFromTo);
			softReplaceIds($cols, 'stpl_row_id', $rowsFromTo);
			foreach($stpls as $i => $stpl) {
				$stpls[ $i ]['protected'] = 1;
				$stpls[ $i ]['parent_id'] = 0;
			}
		}
		$addAsPreselected = (int) reqFhf::getVar('add_as_preselected');
		if($addAsPreselected) {
			foreach($stpls as $stpl) {
				if($stpl['id'] == $addAsPreselected) {
					$newStpl = $stpl;
					$newStpl['protected'] = 0;
					$newStpl['parent_id'] = $stpl['id'];
					$newStpl['id'] = FHF_STPL_DEFINED_IDS_MAX;
					
					$addRows = array();
					$addCols = array();
					$maxRowId = 0;
					$maxColId = 0;
					foreach($rows as $row) {
						if($row['id'] > $maxRowId)
							$maxRowId = $row['id'];
					}
					foreach($cols as $col) {
						if($col['id'] > $maxColId)
							$maxColId = $col['id'];
					}
					foreach($rows as $row) {
						if($row['stpl_id'] == $stpl['id']) {
							$newRow = $row;
							$newRow['id'] = ++$maxRowId;
							$newRow['stpl_id'] = $newStpl['id'];
							foreach($cols as $col) {
								if($col['stpl_row_id'] == $row['id']) {
									$newCol = $col;
									$newCol['id'] = ++$maxColId;
									$newCol['stpl_row_id'] = $maxRowId;
									$addCols[] = $newCol;
								}
							}
							$addRows[] = $newRow;
						}
					}
					if(!empty($addRows))
						$rows = array_merge($rows, $addRows);
					if(!empty($addCols))
						$cols = array_merge($cols, $addCols);
					$stpls[] = $newStpl;
					break;
				}
			}
		}
		echo '<pre>';
		if(!empty($stpls)) {
			generateOut('stpl', $stpls);
		}
		if(!empty($rows)) {
			generateOut('stpl_rows', $rows);
		}
		if(!empty($rows)) {
			generateOut('stpl_cols', $cols);
		}
		exit();
	}
	public function getPostsListForSelect() {
		$res = new responseFhf();
		if(($posts = $this->getModel()->getPostsList())) {
			$res->addData('posts', $posts);
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getPagesListForSelect() {
		$res = new responseFhf();
		if(($posts = $this->getModel()->getPostsList(array('post_type' => 'page')))) {
			$res->addData('posts', $posts);
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getPostsCategoriesListForSelect() {
		$res = new responseFhf();
		if(($categories = $this->getModel()->getPostsCategoriesList())) {
			$res->addData('categories', $categories);
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getShortcodeHtml() {
		$res = new responseFhf();
		if(($html = $this->getModel()->getShortcodeHtml(reqFhf::get('post')))) {
			$res->setHtml( $html );
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			FHF_USERLEVELS => array(
				FHF_ADMIN => array('load', 'getTextEditor', 'save', 'generateInstallQueries', 'getPostsListForSelect', 'getPagesListForSelect', 'getShortcode')
			),
		);
	}
}

