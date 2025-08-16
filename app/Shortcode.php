<?php
/**
 * All Shortcode related functions
 */
namespace Codexpert\Warranty_Managment\App;
use Codexpert\Plugin\Base;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @package Plugin
 * @subpackage Shortcode
 * @author Codexpert <hi@codexpert.io>
 */
class Shortcode extends Base {

    public $plugin;

    /**
     * Constructor function
     */
    public function __construct( $plugin ) {
        $this->plugin   = $plugin;
        $this->slug     = $this->plugin['TextDomain'];
        $this->name     = $this->plugin['Name'];
        $this->version  = $this->plugin['Version'];
    }

    public function wms_customer_dashboard() {
        ob_start();
        echo '<h2>Customer Dashboard</h2>';
        echo '<p>Apply to become a Shop Owner:</p>';
        echo '<form method="post" enctype="multipart/form-data">
                <input type="text" name="shop_name" placeholder="Shop Name" required><br>
                <input type="file" name="documents[]" multiple required><br>
                <input type="submit" name="wms_apply_shop" value="Apply">
              </form>';
        // Handle form submission
        if (isset($_POST['wms_apply_shop'])) {
            $user_id = get_current_user_id();
            $shop_name = sanitize_text_field($_POST['shop_name']);
            $docs = [];
            if (!empty($_FILES['documents'])) {
                foreach ($_FILES['documents']['name'] as $i => $name) {
                    $upload = wp_upload_bits($name, null, file_get_contents($_FILES['documents']['tmp_name'][$i]));
                    if (!$upload['error']) $docs[] = $upload['url'];
                }
            }
            global $wpdb;
            $wpdb->insert($wpdb->prefix.'wms_shop_applications', [
                'user_id' => $user_id,
                'shop_name' => $shop_name,
                'documents' => maybe_serialize($docs),
                'status' => 'pending',
                'applied_date' => current_time('mysql')
            ]);
            echo '<p>Application submitted. Waiting for admin approval.</p>';
        }
        return ob_get_clean();
    }
}