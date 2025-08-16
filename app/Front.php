<?php
/**
 * All public facing functions
 */
namespace Codexpert\Warranty_Managment\App;
use Codexpert\Plugin\Base;
use Codexpert\Warranty_Managment\Helper;
/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Front
 * @author Codexpert <hi@codexpert.io>
 */
class Front extends Base {

	public $plugin;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->version	= $this->plugin['Version'];
	}

	public function head() {}
	
	/**
	 * Enqueue JavaScripts and stylesheets
	 */
	public function enqueue_scripts() {
		$min = defined( 'WARRANTY_MANAGMENT_DEBUG' ) && WARRANTY_MANAGMENT_DEBUG ? '' : '.min';

		wp_enqueue_style( $this->slug, plugins_url( "/assets/css/front{$min}.css", WARRANTY_MANAGMENT ), '', $this->version, 'all' );

		wp_enqueue_script( $this->slug, plugins_url( "/assets/js/front{$min}.js", WARRANTY_MANAGMENT ), [ 'jquery' ], $this->version, true );
		
		$localized = [
			'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
			'_wpnonce'	=> wp_create_nonce(),
		];
		wp_localize_script( $this->slug, 'WARRANTY_MANAGMENT', apply_filters( "{$this->slug}-localized", $localized ) );
	}

	public function modal() {
		echo '
		<div id="warranty-managment-modal" style="display: none">
			<img id="warranty-managment-modal-loader" src="' . esc_attr( WARRANTY_MANAGMENT_ASSET . '/img/loader.gif' ) . '" />
		</div>';
	}
}