<?php
use Codexpert\Plugin\Table;

global $wpdb;

// ---------- Handle Approve / Reject for Shop Applications ----------
$app_table = $wpdb->prefix.'wms_shop_applications';
if ( isset($_GET['action'], $_GET['id']) ) {
    $id = intval($_GET['id']);
    if ($_GET['action'] === 'approve') {
        $wpdb->update($app_table, ['status'=>'approved','approved_date'=>current_time('mysql')], ['id'=>$id]);
        echo '<div class="updated"><p>Application approved!</p></div>';
    } elseif ($_GET['action'] === 'reject') {
        $wpdb->update($app_table, ['status'=>'rejected'], ['id'=>$id]);
        echo '<div class="error"><p>Application rejected!</p></div>';
    }
}

// ---------- Shop Owner Applications Table ----------
$apps = $wpdb->get_results("SELECT * FROM $app_table ORDER BY applied_date DESC");
echo '<h2>Shop Owner Applications</h2>';
echo '<table class="wp-list-table widefat fixed">';
echo '<tr><th>User</th><th>Shop Name</th><th>Status</th><th>Actions</th></tr>';
foreach ($apps as $app) {
    $user = get_userdata($app->user_id);
    echo '<tr>';
    echo '<td>'.$user->user_login.'</td>';
    echo '<td>'.$app->shop_name.'</td>';
    echo '<td>'.$app->status.'</td>';
    echo '<td>
        <a href="'.wp_nonce_url('?page=wms_applications&action=approve&id='.$app->id,'wms_app_action').'">Approve</a> |
        <a href="'.wp_nonce_url('?page=wms_applications&action=reject&id='.$app->id,'wms_app_action').'">Reject</a>
    </td>';
    echo '</tr>';
}
echo '</table><br>';

// ---------- Orders Table Using Codexpert Table ----------
$config = [
    'per_page'      => 5,
    'columns'       => [
        'id'            => __( 'Order #', 'warranty-managment' ),
        'products'      => __( 'Products', 'warranty-managment' ),
        'order_total'   => __( 'Order Total', 'warranty-managment' ),
        'commission'    => __( 'Commission', 'warranty-managment' ),
        'payment_status'=> __( 'Payment Status', 'warranty-managment' ),
        'time'          => __( 'Time', 'warranty-managment' ),
    ],
    'sortable'      => [ 'id', 'products', 'commission', 'payment_status', 'time' ],
    'orderby'       => 'time',
    'order'         => 'desc',
    'data'          => [
        [ 'id' => 345, 'products' => 'Abc', 'order_total' => '$678', 'commission' => '$98', 'payment_status' => 'Unpaid', 'time' => '2020-06-29' ],
        [ 'id' => 567, 'products' => 'Xyz', 'order_total' => '$178', 'commission' => '$18', 'payment_status' => 'Paid', 'time' => '2020-05-26' ],
        [ 'id' => 451, 'products' => 'Mno', 'order_total' => '$124', 'commission' => '$12', 'payment_status' => 'Paid', 'time' => '2020-07-01' ],
        [ 'id' => 588, 'products' => 'Uji', 'order_total' => '$523', 'commission' => '$22', 'payment_status' => 'Pending', 'time' => '2020-07-02' ],
        [ 'id' => 426, 'products' => 'Rim', 'order_total' => '$889', 'commission' => '$33', 'payment_status' => 'Paid', 'time' => '2020-08-01' ],
        [ 'id' => 109, 'products' => 'Rio', 'order_total' => '$211', 'commission' => '$11', 'payment_status' => 'Unpaid', 'time' => '2020-08-12' ],
    ],
    'bulk_actions'  => [
        'delete' => __( 'Delete', 'warranty-managment' ),
        'draft'  => __( 'Draft', 'warranty-managment' ),
    ],
];

$table = new Table( $config );
echo '<h2>Orders</h2>';
echo '<form method="post">';
$table->prepare_items();
$table->search_box( 'Search Orders', 'search' );
$table->display();
echo '</form>';
