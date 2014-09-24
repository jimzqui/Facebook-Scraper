<?php

/**
 * Post
 *
 * @package AppModel
 */
class Post extends AppModel {

	public $name = 'Post';

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
	 * Add post
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function add($data) {
		if ($this->isExist($data['id'])) return false;
		$this->create();
		return $this->save($data);
	}

	/**
	 * Check if post exist
	 *
	 * @param string $id
	 * @return mixed
	 */
	public function isExist($id) {
		return $this->find('count', array(
			'conditions' => array(
				'Post.id' => $id
			)
		));
	}

	/**
	 * Retrieve link by id
	 *
	 * @param string $id
	 * @return mixed
	 */
	public function linkById($id) {
		return $this->find('first', array(
			'conditions' => array(
				'Post.id' => $id
			)
		));
	}

}