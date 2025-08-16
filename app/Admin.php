<?php
/**
 * All admin facing functions
 */
namespace Codexpert\Warranty_Managment\App;
use Codexpert\Plugin\Base;
use Codexpert\Plugin\Metabox;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Admin
 * @author Codexpert <hi@codexpert.io>
 */
class Admin extends Base {

	public $plugin;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->server	= $this->plugin['server'];
		$this->version	= $this->plugin['Version'];
	}

	/**
	 * Internationalization
	 */
	public function i18n() {
		load_plugin_textdomain( 'warranty-managment', false, WARRANTY_MANAGMENT_DIR . '/languages/' );
	}

	/**
	 * Installer. Runs once when the plugin in activated.
	 *
	 * @since 1.0
	 */
	public function install() {

		if( ! get_option( 'warranty-managment_version' ) ){
			update_option( 'warranty-managment_version', $this->version );
		}
		
		if( ! get_option( 'warranty-managment_install_time' ) ){
			update_option( 'warranty-managment_install_time', time() );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . "wms_shop_applications";

		// Check if table exists
		if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
		    $charset_collate = $wpdb->get_charset_collate();
		    
		    $sql = "CREATE TABLE $table_name (
		        id BIGINT(20) NOT NULL AUTO_INCREMENT,
		        user_id BIGINT(20) NOT NULL,
		        shop_name VARCHAR(255) NOT NULL,
		        documents TEXT,
		        status VARCHAR(20) DEFAULT 'pending',
		        applied_date DATETIME,
		        approved_date DATETIME,
		        admin_note TEXT,
		        PRIMARY KEY  (id)
		    ) $charset_collate;";

		    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		    dbDelta( $sql );
		}

	}

	/**
	 * Enqueue JavaScripts and stylesheets
	 */
	public function enqueue_scripts() {
		$min = defined( 'WARRANTY_MANAGMENT_DEBUG' ) && WARRANTY_MANAGMENT_DEBUG ? '' : '.min';
		
		wp_enqueue_style( $this->slug, plugins_url( "/assets/css/admin{$min}.css", WARRANTY_MANAGMENT ), '', $this->version, 'all' );

		wp_enqueue_script( $this->slug, plugins_url( "/assets/js/admin{$min}.js", WARRANTY_MANAGMENT ), [ 'jquery' ], $this->version, true );
	}

	public function footer_text( $text ) {
		if( get_current_screen()->parent_base != $this->slug ) return $text;

		return sprintf( __( 'Built with %1$s by the folks at <a href="%2$s" target="_blank">Codexpert, Inc</a>.' ), '&hearts;', 'https://codexpert.io' );
	}

	public function modal() {
		echo '
		<div id="warranty-managment-modal" style="display: none">
			<img id="warranty-managment-modal-loader" src="' . esc_attr( WARRANTY_MANAGMENT_ASSET . '/img/loader.gif' ) . '" />
		</div>';
	}

}