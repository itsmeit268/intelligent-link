<?php

/**
 * @link       https://itsmeit.co/
 * @package    intelligent-link
 * @subpackage intelligent-link/admin
 * @author     itsmeit <buivanloi.2010@gmail.com>
 * Website     https://itsmeit.co/
 */

class Preplink_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain('prep-link',
            false,
            dirname( dirname( plugin_basename(__FILE__ ))) . '/languages/'
        );
	}
}
