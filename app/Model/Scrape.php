<?php

/**
 * Scrape
 *
 * @package AppModel
 */
class Scrape extends AppModel {

	public $name = 'Scrape';

	/**
	 * Add scrape
	 *
	 * @param array $data
	 * @return mixed
	 */
	public function add($data) {
		$this->create();
		return $this->save($data);
	}

	/**
	 * Retrieve scrape by page
	 *
	 * @param string $page
	 * @return mixed
	 */
	public function findByPage($page) {
		return $this->find('all', array(
			'conditions' => array(
				'Scrape.page' => $page
			)
		));
	}

	/**
	 * Check if scrape exist by date
	 *
	 * @param string $page
	 * @param string $date
	 * @return mixed
	 */
	public function isExist($page, $date) {
		return $this->find('count', array(
			'conditions' => array(
				'Scrape.page >=' => $page,
				'Scrape.created >=' => $date
			)
		));
	}

}