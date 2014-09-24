<?php

/**
 * Controlls data retrieval for users
 *
 * @package AppController
 */
class UsersController extends AppController {

	/**
     * Loads the models
     */
	public $uses = array('User', 'Action', 'Post');

	/**
     * Load the components
     */
	public $components = array('DataTable');

	/**
	 * Get all users of page
	 *
	 * @access public
	 * @return void
	 */
	public function all() {

		// Dont render view
		$this->autoRender = false;

		// Get page
		$page = $this->_getFbpage();

		$this->paginate = array(
			'fields' => array('User.facebookid', 'User.name', 'User.total_comments', 'User.total_likes'),
			'conditions' => array(
				'User.page' => $page
			),
			'group' => 'User.facebookid',
			'order' => array('User.id DESC')
		);
		
		$this->DataTable->emptyElements = 0;
		echo json_encode($this->DataTable->getResponse(), true);
	}

	/**
	 * Get latest users of page
	 *
	 * @access public
	 * @return void
	 */
	public function latest() {

		// Dont render view
		$this->autoRender = false;

		// Get page
		$page = $this->_getFbpage();

		$this->paginate = array(
			'fields' => array('User.facebookid', 'User.name', 'User.total_comments', 'User.total_likes'),
			'conditions' => array(
				'User.page' => $page
			),
			'group' => 'User.facebookid',
			'order' => array('User.id DESC')
		);
		
		$this->DataTable->emptyElements = 0;
		echo json_encode($this->DataTable->getResponse(), true);
	}

	/**
	 * Retrieve action activity
	 *
	 * @access public
	 * @param string $facebookid
	 * @param string $type
	 * @return void
	 */
	public function activity($facebookid, $type) {

		// Dont render view
		$this->autoRender = false;

		// Get activity
		$actions = $this->Action->userActivity($facebookid, $type, $this->_getFbpage());

		// Create container
		$result = array();

		// Iterate activity
		foreach ($actions as $post) {
			$id = $post['Action']['post_id'];
			$data = $this->Post->linkById($id);

			$arr = array(
				'type' => $data['Post']['type'],
				'link' => $data['Post']['link']
			);
			array_push($result, $arr);
		}

		// Return result
		echo json_encode($result, true);
	}

	/**
	 * Retrieves the active page
	 *
	 * @access public
	 * @return string
	 */
	private function _getFbpage() {

		// If page is passed in the URL...
		if (isset($this->request->query['page'])) {

			// Set page from the URL
			$page = $this->request->query['page'];
		} else {

			// Set page from the default page
			$page = Configure::read('DefaultPage');
		}

		// Return page
		return $page;
	}
  
}