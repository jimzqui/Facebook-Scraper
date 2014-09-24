<?php

/**
 * Action
 *
 * @package AppModel
 */
class Action extends AppModel {

	public $name = 'Action';

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
	public $belongsTo = array(
		'Post' => array(
			'className' => 'Post',
			'foreignKey' => 'post_id'
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'post_id'
		)
	);

	/**
	 * Add action
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function add($data) {
		if ($this->isExist($data['post_id'], $data['facebookid'], $data['type'])) return false;
		$this->create();
		return $this->save($data);
	}

	/**
	 * Check if action exist
	 *
	 * @param string $post_id
	 * @param string $facebookid
	 * @param string $type
	 * @return mixed
	 */
	public function isExist($post_id, $facebookid, $type) {
		return $this->find('count', array(
			'conditions' => array(
				'Action.post_id' => $post_id,
				'Action.facebookid' => $facebookid,
				'Action.type' => $type
			)
		));
	}

	/**
	 * Retrieve action by page with type
	 *
	 * @param string $facebookid
	 * @param string $page
	 * @param string $type
	 * @return mixed
	 */
	public function userActivity($facebookid, $type, $page) {
		return $this->find('all', array(
			'conditions' => array(
				'Action.facebookid' => $facebookid,
				'Action.page' => $page,
				'Action.type' => $type
			)
		));
	}

}