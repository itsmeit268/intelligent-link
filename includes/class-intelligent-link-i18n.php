<?php

/**
 * @link       https://itsmeit.co/
 * @package    intelligent-link
 * @subpackage intelligent-link/includes
 * @author     itsmeit <buivanloi.2010@gmail.com>
 * Website     https://itsmeit.co/
 */

class Intelligent_Link_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain('intelligent-link',
            false,
            dirname( dirname( plugin_basename(__FILE__ ))) . '/languages/'
        );
	}
}
