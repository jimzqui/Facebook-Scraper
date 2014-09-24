<?php

/**
 * Includes different library
 */
App::import('Vendor', 'facebook');
App::uses('Functions', 'Lib');

/**
 * Controlls the data retrieval
 *
 * @package AppController
 */
class DataController extends AppController {

	/**
     * Loads the models
     */
	public $uses = array('Post', 'Action', 'Scrape', 'User');

	/**
     * Load the components
     */
	public $components = array('DataTable');

	/**
	 * Display aggregated data
	 *
	 * @access public
	 * @return void
	 */
	public function index() {

		// Get page
		$page = $this->_getFbpage();

		// Get backdate
        $backdate = $this->_getBackDate($page);

		// Ser variables for view
		$this->set('page', $page);

		// Set variables for JS
		Configure::write('Export.page', $page);
		Configure::write('Export.backdate', $backdate);
	}
	
	/**
	 * Aggregate data from a facebook page
	 *
	 * @access public
	 * @return void
	 */
	public function scrape() {

		// Get page
		$page = $this->_getFbpage();

        // Get backdate
        $backdate = $this->_getBackDate($page);

        // Exit if backdate is already scraped
        if ($backdate && $this->Scrape->isExist($page, $backdate)) {
        	Configure::write('Export.exit', true);
        } else {
        	Configure::write('Export.exit', false);
        }

		// Set variables for JS
		Configure::write('Export.page', $page);
		Configure::write('Export.backdate', $backdate);

		// Ser variables for view
		$this->set('page', $page);
	}

	/**
	 * Retrieve all posts
	 *
	 * @access public
	 * @return json
	 */
	public function getPosts() {

		// Dont render view
		$this->autoRender = false;

		// Get data
		$page = $this->request->data['page'];
		$backdate = $this->request->data['backdate'];

		// Contruct fields
		$fields = 'id,message,picture,link,icon,type,status_type,object_id,created_time,updated_time';

		// Get param base in backdate
		if ($backdate) {
			$param = array(
				'access_token' => $this->access_token,
				'backdated_time' => strtotime($backdate),
				'fields' => $fields,
				'limit' => 1000
			);
		} else {
			$param = array(
				'access_token' => $this->access_token,
				'fields' => $fields,
				'limit' => 1000
			);
		}

		// Get result using the API
		try {
			$result = $this->facebook->api('/' . $page . '/posts', $param);
		} catch(Exception $e) {
			return json_encode(array('result' => 'error'));
		}

		// Return posts
		$posts = $result['data'];
		return json_encode($posts, true);
	}

	/**
	 * Retrieve likes and comments by batch
	 *
	 * @access public
	 * @param string $action
	 * @return json
	 */
	public function getActivity($action) {

		// Dont render view
		$this->autoRender = false;

		// Get data
		$chunk = $this->request->data['chunk'];

		// Create container
		$data = array();

		// Batch query
		$batch_query = array();

		// Object ids
		$post_ids = array();
		$obj_ids = array();
		$counter = 0;

		// Iterate chunk
		foreach($chunk as $post) {

			// Save post id
			$post_ids[$counter] = $post['id'];
			$obj_ids[$counter] = (isset($post['object_id'])) ? $post['object_id'] : '';

			// Get relative url
			if ($action == 'likes') {
				$relative_url = '/' . $post['id'] . '/likes?fields=id,name%26limit=1000';
			} else {
				$relative_url = '/' . $post['id'] . '/comments?fields=from%26limit=1000';
			}

		    array_push($batch_query, array('method' => 'GET', 'relative_url' => $relative_url));
		    $counter++;
		}

		// Do batch query
	    try {
			$batch_response = $this->facebook->api('?batch=' . json_encode($batch_query), 'POST');
		} catch(Exception $e) {
			return json_encode(array('result' => 'error'));
		}

	    // Iterate batch response
	    $counter = 0;

	    foreach ($batch_response as $response) {

	    	// Push array to container
	    	$arr = json_decode($response['body'], true);
	    	$data_arr = array();

	    	// Add post id to data
	    	foreach ($arr['data'] as $row) {

	    		if ($action == 'likes') {
	    			$info = $row;
	    		} else {
	    			$info = $row['from'];
	    		}

	    		array_push($data_arr, array(
	    			'id' => $info['id'],
	    			'name' => $info['name'],
	    			'post_id' => $post_ids[$counter],
	    			'objectid' => $obj_ids[$counter]
	    		));
	    	}

	    	$data = array_merge($data, $data_arr);
	    	$counter++;
	    }

		// Return data
	    return json_encode($data, true);
	}

	/**
	 * Save all posts
	 *
	 * @access public
	 * @return int
	 */
	public function savePosts() {

		// Dont render view
		$this->autoRender = false;

		// Get data
		$chunk = $this->request->data['chunk'];
		$page = $this->request->data['page'];

		// Post counter
		$counter = 0;

		// Iterate chunk
		foreach($chunk as $post) {

			// Map other field names
			$post['fb_created_time'] = $post['created_time'];
			$post['fb_updated_time'] = $post['updated_time'];
			$post['objectid'] = (isset($post['object_id'])) ? $post['object_id'] : '';
			$post['page'] = $page;

			// Insert to DB
			try {
				$added_post = $this->Post->add($post);
			} catch(Exception $e) {
				$added_post = false;
			}

			if ($added_post) { $counter++; }
		}

		return $counter;
	}

	/**
	 * Save likes and comments
	 *
	 * @access public
	 * @return int
	 */
	public function saveActivity($action) {

		// Dont render view
		$this->autoRender = false;

		// Get data
		$chunk = $this->request->data['chunk'];
		$page = $this->request->data['page'];

		// Group comments by id
		$group = Functions::groupBy($chunk, 'id');

		// Like counter
		$counter = 0;

		// Iterate group
		foreach ($group as $row) {

			// Insert to DB
			try {

				// Check user
				$user = $this->User->findByFacebookid($row[0]['id'], $this->_getFbpage());

				// User exist, update
				if ($user) {
					if ($action == 'likes') {
						$total_likes = $user['User']['total_likes'] + count($row);
						$total_comments = $user['User']['total_comments'];
					} else {
						$total_likes = $user['User']['total_likes'];
						$total_comments = $user['User']['total_comments'] + count($row);
					}

					$added_user = $this->User->update(array(
						'page' => $page,
						'facebookid' => $row[0]['id'],
						'name' => $row[0]['name'],
						'total_likes' => $total_likes,
						'total_comments' => $total_comments
					), $user['User']['id']);

					$user_id = $user['User']['id'];
				} 

				// User doesnt exist, add instead
				else {
					if ($action == 'likes') {
						$total_likes = count($row);
						$total_comments = 0;
					} else {
						$total_likes = 0;
						$total_comments = count($row);
					}

					$added_user = $this->User->add(array(
						'page' => $page,
						'facebookid' => $row[0]['id'],
						'name' => $row[0]['name'],
						'total_likes' => $total_likes,
						'total_comments' => $total_comments
					));

					$user_id = $this->User->id;
				}
			} catch(Exception $e) {
				$added_action = false;
			}

			// Iterate row
			foreach ($row as $data) {

				// Insert to DB
				try {
					$added_action = $this->Action->add(array(
						'page' => $page,
						'user_id' => $user_id,
						'post_id' => $data['post_id'],
						'objectid' => $data['objectid'],
						'facebookid' => $data['id'],
						'type' => $action
					));
				} catch(Exception $e) {
					$added_action = false;
				}

				if ($added_action) { $counter++; }
			}
		}

		return $counter;
	}

	/**
	 * Save scrapes for future reference
	 *
	 * @access public
	 * @return boolean
	 */
	public function saveScrapes() {

		// Dont render view
		$this->autoRender = false;

		// Get data
		$saved_posts = $this->request->data['saved_posts'];
		$saved_likes = $this->request->data['saved_likes'];
		$saved_comments = $this->request->data['saved_comments'];
		$page = $this->request->data['page'];

		// Insert to DB
		try {
			$added_scrape = $this->Scrape->add(array(
				'page' => $page,
				'total_posts' => $saved_posts,
				'total_likes' => $saved_likes,
				'total_comments' => $saved_comments
			));

			return true;
		} catch(Exception $e) {
			$added_scrape = false;
			return false;
		}
	}

	/**
	 * Retrieves the active page
	 *
	 * @access private
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

	/**
	 * Retrieve all posts
	 *
	 * @access private
	 * @param string $page
	 * @return datetime
	 */
	public function _getBackDate($page) {

		// Get scrape by page
        $scrape = $this->Scrape->findByPage($page);

        // Set backdate
        if ($scrape) {
        	$backdate = date('Y-m-d H:i:s', strtotime('-1 days'));
        } else {
        	$backdate = false;
        }

        // Return backdate
        return $backdate;
	}

	/**
	 * Exports data into CSV file
	 *
	 * @access public
	 * @return void
	 */
	public function exportCsv() {

		// Disable view rendering
		$this->autoRender = false;

		// Retrieve all users
		$all_users = $this->User->all($this->_getFbpage());

		// Open file to write
		$fp = fopen('file.csv', 'w');
		fputcsv($fp, array(
			'Facebook ID',
			'Full Name',
			'Total Comments',
			'Total Likes',
			'ID'
		));

		foreach ($all_users as $user) {
		    fputcsv($fp, $user['User']);
		}

		// Close file
		fclose($fp);

		// Redirect to download page
		return $this->redirect(
            array('controller' => 'data', 'action' => 'download')
        );
	}

	/**
	 * Download CSV data
	 *
	 * @access public
	 * @return void
	 */
	public function download() {

		// Set view class
        $this->viewClass = 'Media';

        // Open file
        $this->set(array(
            'id'        => 'file.csv',
            'name'      => 'Users Data',
            'download'  => true,
            'extension' => 'csv'
        ));
    }
  
}