<?php

/**
 * User
 *
 * @package AppModel
 */
class User extends AppModel {

	public $name = 'User';

	/**
	 * hasMany associations
	 *
	 * @var array
	 */
	public $hasMany = array(
		'Action' => array(
			'className' => 'Action',
			'foreignKey' => 'post_id',
			'dependent' => false
		)
	);

	/**
	 * Add user
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function add($data) {
		$this->create();
		return $this->save($data);
	}

	/**
	 * Update user
	 *
	 * @param array $data
	 * @param int $id
	 * @return mixed
	 */
	public function update($data, $id) {
		$this->id = $id;
		return $this->save($data);
	}

	/**
	 * Retrieve user by facebookid
	 *
	 * @param string $facebookid
	 * @param string $page
	 * @return mixed
	 */
	public function findByFacebookid($facebookid, $page) {
		return $this->find('first', array(
			'conditions' => array(
				'User.facebookid' => $facebookid,
				'User.page' => $page
			)
		));
	}

	/**
	 * Retrieve all user by page
	 *
	 * @param string $page
	 * @return mixed
	 */
	public function all($page) {
		return $this->find('all', array(
			'fields' => array('User.facebookid', 'User.name', 'User.total_comments', 'User.total_likes'),
			'conditions' => array(
				'User.page' => $page
			),
			'group' => 'User.facebookid',
			'order' => array('User.id DESC')
		));
	}

}