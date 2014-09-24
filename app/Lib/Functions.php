<?php

/**
 * Holds miscellaneous functions
 *
 * @package AppController
 */
class Functions {

	/**
	 * Computes the percentile rankings
	 *
	 * @access public
	 * @param array $array
	 * @param string $key
	 * @return array
	 */
	public static function groupBy($array, $key) {
	    $return = array();

	    foreach($array as $val) {
	        $return[$val[$key]][] = $val;
	    }

	    return $return;
	}

}

?>