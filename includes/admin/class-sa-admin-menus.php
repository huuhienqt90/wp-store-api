<?php
/**
 * Setup menus in WP admin.
 *
 * @author   Hien(Hamilton) H.HO
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WCPG_Admin_Menus' ) ) :

/**
 * WCPG_Admin_Menus Class.
 */
class WCPG_Admin_Menus {

    /**
     * Hook in tabs.
     */
    public function __construct() {
        // Add menus
        add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
        add_action( 'load-nav-menus.php', array( $this, 'nav_menus' ), 2 );
        add_filter( 'walker_nav_menu_start_el', array( $this, 'change_menu_structure' ), 1, 4 );

        // AJAX functions
        add_action( 'wp_ajax_sa_load_location', array( $this, 'ajax_load_location' ) );
        add_action( 'wp_ajax_nopriv_sa_load_location', array( $this, 'ajax_load_location' ) );

        add_action( 'wp_ajax_sa_load_city', array( $this, 'ajaxLoadCityByIndustry' ) );
        add_action( 'wp_ajax_nopriv_sa_load_city', array( $this, 'ajaxLoadCityByIndustry' ) );

        add_action( 'wp_ajax_sa_load_store', array( $this, 'ajaxLoadStoreByCountry' ) );
        add_action( 'wp_ajax_nopriv_sa_load_store', array( $this, 'ajaxLoadStoreByCountry' ) );

        add_action( 'wp_ajax_sa_post_api', array( $this, 'ajaxPostAPI' ) );
        add_action( 'wp_ajax_nopriv_sa_post_api', array( $this, 'ajaxPostAPI' ) );

        add_action( 'wp_ajax_sa_load_menu', array( $this, 'ajaxPostMenu' ) );
        add_action( 'wp_ajax_nopriv_sa_load_menu', array( $this, 'ajaxPostMenu' ) );
    }

    /**
     * Add menu to admin to can custom in admin panel
     */
    public function nav_menus(){
        add_meta_box(
            'menu-product-group-api',
            __( 'Product Group API', 'menu-icons' ),
            array( $this, 'meta_box' ),
            'nav-menus',
            'side',
            'low',
            array()
        );
    }

    /**
     * Show menu product group api in admin panel
     */
    public function meta_box(){
        ?>
        <div id="posttype-wl-login" class="posttypediv">
            <div id="tabs-panel-wishlist-login" class="tabs-panel tabs-panel-active">
                <ul id ="wishlist-login-checklist" class="categorychecklist form-no-clear">
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> Categories
                        </label>
                        <input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="store-api-group">
                        <input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="Categories">
                        <input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="group">
                        <input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="store-api-menu">
                    </li>
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> Product Categories
                        </label>
                        <input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="store-api-product-cat">
                        <input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="Product Categories">
                        <input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="city">
                        <input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="store-api-menu">
                    </li>
                </ul>
            </div>
            <p class="button-controls">
                <span class="list-controls">
                    <a href="<?php echo admin_url(); ?>?page-tab=all&amp;selectall=1#posttype-wl-login" class="select-all">Select All</a>
                </span>
                <span class="add-to-menu">
                    <input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-posttype-wl-login">
                    <span class="spinner"></span>
                </span>
            </p>
        </div>
        <?php
    }

    /**
     * Add menu items.
     */
    public function admin_menu() {
        add_menu_page( 'Locations', 'Locations', 'manage_options', 'sa-locations', array($this, 'menu_locations'), null, 30 );
        add_submenu_page( 'sa-locations', 'Setup', 'Setup', 'manage_options', 'sa-location-setup', array($this, 'menu_location_settings') );
    }

    /**
     * Init the groups page.
     */
    public function menu_locations(){
        global $product_api;

        $industryID = get_option('wpsa_industry_id', 4);

        $industry = $product_api->post('GetLocationItem/JSON', ['LocationId'=>0]);
        ?>
        <div class="wrap woocommerce">
            <div class="icon32 icon32-group" id="icon-woocommerce"><br/></div>
            <h1><?php _e( 'Locations', __TEXTDOMAIN__ ); ?></h1>
            <br class="clear" />
            <?php global $store_api; ?>
            <div class="loading"><img src="<?php echo $store_api->plugin_url(); ?>/assets/images/loading.gif" alt=""/></div>
            <div id="col-container">
                <div class="locations-box">
                    <div class="location-items">
                        <div class="location-item" id="location-item-0">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'Industry', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0" data-target="1" class="location-item-click">All</a></li>
                                    <?php if( $industry->StatusCode == 200 && count($industry->Content) ): ?>
                                        <?php foreach ($industry->Content as $item): ?>
                                            <li><a href="" data-id="<?=$item->Id?>" data-target="1" class="location-item-click" data-device="<?=$item->Hierarchy?>"><?=$item->LocationItemName?></a></li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div><!-- END #location-item-0 -->

                        <div class="location-item" id="location-item-1">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'Chain', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-1 -->

                        <div class="location-item" id="location-item-2">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'Region', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-2 -->

                        <div class="location-item" id="location-item-3">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'Country', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-3 -->

                        <div class="location-item" id="location-item-4">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'State', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-4 -->

                        <div class="location-item" id="location-item-5">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'City', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-5 -->

                        <div class="location-item" id="location-item-6">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'Suburb', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-6 -->

                        <div class="location-item" id="location-item-7">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'Suburb Area', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-7 -->

                        <div class="location-item" id="location-item-8">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'Location', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-8 -->

                        <div class="location-item" id="location-item-9">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'Location Areas', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-9 -->

                        <div class="location-item" id="location-item-10">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'Devices Used', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-10 -->

                        <div class="location-item" id="location-item-11">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'Applications', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-11 -->

                        <div class="location-item" id="location-item-12">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'Menus', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-12 -->

                        <div class="location-item" id="location-item-13">
                            <div class="location-item-header"><h3 class="location-item-title"><?php _e( 'Special Menus', __TEXTDOMAIN__ ); ?></h3></div>
                            <div class="location-item-body">
                                <ul class="location-item-list">
                                    <li><a href="" data-id="0">All</a></li>
                                </ul>
                            </div>
                        </div><!-- END #location-item-13 -->
                    </div>
                </div>
                
            </div>
        </div>
        <style type="text/css">
            .locations-box{
                position: relative;
                width: 100%;
                overflow-x: scroll;
                overflow-y: hidden;
            }
            .location-items{
                position: relative;
                display: inline-flex;
                top: 0;
                left: 0;
            }
            .location-items .location-item:first-child{
                border-left: 1px solid #999;
            }
            .location-items .location-item{
                display: block;
                width: 200px;
                border-right: 1px solid #999;
                border-bottom: 1px solid #999;
                background: #FFF;
            }
            .location-item-header h3 {
                background: #999;
                color: #FFF;
                margin: 0;
                padding: 10px 5px;
            }
            .location-item-list{
                margin: 0;
            }
            .location-item-list li{
                margin: 0;
            }
            .location-item-list li a{
                display: block;
                width: 100%;
                box-sizing: border-box;
                padding: 5px;
                text-decoration: none;
                color: #000;
            }
            .location-item-list li.active a, .location-item-list li a:hover{
                background: #00A8EF;
                color: #FFF;
            }
            .loading{
                height: 20px;
            }
            .loading img{
                text-align: center;
                display: none;
            }
        </style>
        <script type="application/javascript">
            jQuery(document).ready(function ($) {
                var loading = false;
                var device = 0;
                $(document).on('click','.location-item-click',function () {
                    var id = $(this).data('id');
                    var target = $(this).data('target');
                    if( target == 11 ) device = $(this).data('device');
                    var nextTarget = target+1;

                    var data = {
                        'action': 'sa_load_location',
                        'id': id,
                        'target': target
                    };
                    if( device != 0 ){
                        data.device = device;
                    }
                    for(i = target; i<14; i++){
                        $('#location-item-'+i).find('.location-item-list').html('<li><a href="" data-id="0">All</a></li>');
                    }
                    if( !loading ){
                        //$('.loading').show().height($('body').height());
                        $('.loading img').show();
                        loading = true;
                        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                        $.post(ajaxurl, data, function(response) {
                            $('.loading img').hide();
                            loading = false;
                            var dt = JSON.parse(response);
                            $('#location-item-'+target).find('.location-item-list').html(dt.rs1);
                            $('#location-item-'+nextTarget).find('.location-item-list').html(dt.rs2);
                        });
                    }

                    $(this).parent().parent().find('li').removeClass('active');
                    $(this).parent().addClass('active');
                    return false;
                });
            });
        </script>
        <?php
    }

    /**
     * Location setup page in wp-admin
     */
    public function menu_location_settings(){
        global $product_api;

        // Update options data
        if( isset( $_POST['apiServerURL'] ) && !empty($_POST['apiServerURL']) ){
            update_option('wpsa_api_server_url', $_POST['apiServerURL']);
        }
        if( isset( $_POST['deviceID'] ) && !empty($_POST['deviceID']) ){
            update_option('wpsa_device_id', $_POST['deviceID']);
        }
        if( isset( $_POST['menuID'] ) && !empty($_POST['menuID']) ){
            update_option('wpsa_menu_id', $_POST['menuID']);
        }
        if( isset( $_POST['sellPriceLevel'] ) && !empty($_POST['sellPriceLevel']) ){
            update_option('wpsa_sell_price_level', $_POST['sellPriceLevel']);
        }
        if( isset( $_POST['sellPriceIndex'] ) && !empty($_POST['sellPriceIndex']) ){
            update_option('wpsa_sell_price_index', $_POST['sellPriceIndex']);
        }
        $deviceID = get_option('wpsa_device_id', 1);
        $menuID = get_option('wpsa_menu_id', 1);
        $sellPriceLevel = get_option('wpsa_sell_price_level', 0);
        $sellPriceIndex = get_option('wpsa_sell_price_index', 0);
        $pointURL = get_option('wpsa_api_server_url', 'http://xero-connect.com:5233/');

        $devices = $product_api->post('GetDevices/JSON');
        $menus = $product_api->post('GetMenusByDevice/JSON', ['id'=>$deviceID]);
        ?>
        <div class="wrap woocommerce">
            <div class="icon32 icon32-group" id="icon-woocommerce"><br/></div>
            <h1><?php _e( 'Setup', __TEXTDOMAIN__ ); ?></h1>
            <br class="clear" />
            <div id="col-container">
                <section id="wpsa-settings" style="margin-bottom: 20px;">
                    <form action="" method="post">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row"><label for="industryID">API Server URL</label></th>
                                    <td>
                                        <input type="text" name="apiServerURL" class="regular-text" value="<?php echo $pointURL; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="deviceID">Device ID</label></th>
                                    <td>
                                        <select name="deviceID" class="regular-text" id="deviceID">
                                            <option value="0">All</option>
                                            <?php foreach ($devices->Content as $device): ?>
                                                <option value="<?php echo $device->Id; ?>" data-sellPriceLevel="<?php echo $device->PriceLevelId; ?>" data-sellPriceIndex="<?php echo $device->SellPriceNumber; ?>" <?php selected($device->Id, $deviceID); ?>><?php echo $device->Name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="menuID">Menu ID</label></th>
                                    <td>
                                        <select name="menuID" class="regular-text" id="menuID">
                                            <option value="0">All</option>
                                            <?php foreach ($menus->Content as $menu): ?>
                                                <option value="<?php echo $menu->Id; ?>" <?php selected($menu->Id, $menuID); ?>><?php echo $menu->MenuName; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="loading" style="display: none;">Loading &hellip;</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="sellPriceLevel">Sellprice Level</label></th>
                                    <td>
                                        <input readonly="readonly" type="text" name="sellPriceLevel" id="sellPriceLevel" class="regular-text" value="<?php echo $sellPriceLevel; ?>" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="sellPriceIndex">Sell Price Index</label></th>
                                    <td>
                                        <input readonly="readonly" type="text" name="sellPriceIndex" id="sellPriceIndex" class="regular-text" value="<?php echo $sellPriceIndex; ?>" />
                                    </td>
                                </tr>
                                
                            </tbody>
                        </table>
                        <input type="submit" value="Save change" class="button button-primary" />
                    </form>
                </section>
            </div>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                var loading = false;
                $(document).on('change', '#deviceID', function () {
                    var val = $(this).val();
                    var level = $('option:selected', this).attr('data-sellPriceLevel');
                    var index = $('option:selected', this).attr('data-sellPriceIndex');
                    $('#sellPriceLevel').val(level);
                    $('#sellPriceIndex').val(index);
                    var data = {
                        'action': 'sa_post_api',
                        'url': 'GetMenusByDevice/JSON',
                        'params': {
                            id: val
                        }
                    };
                    if( !loading ){
                        $('.loading').show();
                        loading = true;
                        $.post(ajaxurl, data, function(response) {
                            var result = JSON.parse(response);
                            if( result.status == 200 ){
                                var rl = result.data;
                                var menuID = $('#menuID');
                                menuID.html('<option value="0">All</option>');
                                rl.forEach(function (element) {
                                    menuID.append('<option value="'+element.Id+'">'+element.MenuName+'</option>');
                                });
                            }
                            loading = false;
                            $('.loading').hide();
                        });
                    }

                    return false;
                });
            });
        </script>
        <?php
    }

    /**
     * Change menu item
     *
     * @param $item_output
     * @param $item
     * @param $depth
     * @param $args
     * @return string
     */
    public function change_menu_structure($item_output, $item, $depth, $args){
        global $wp_query, $product_api;
        switch ($item->type){
            case "store-api-group":
            case "store-api-product-cat":
                $groupID = isset($wp_query->query_vars['group']) && !empty($wp_query->query_vars['group']) ? $wp_query->query_vars['group'] : null;
                $requestPost = ['GroupId' => $groupID, 'ProductGroupType' => 2];
                if( isset($_REQUEST['linkParent']) ){
                    $requestPost['LinkParent'] = $_REQUEST['linkParent'];
                }
                $groups = $product_api->post('GetProductGroup/json', $requestPost);
                
                $item_output = '<div class="store-api-sub-full-width"><ul class="store-api-sub-menu">';
                if( $groups->StatusCode == 200 && count($groups->Content)){
                    $matrixItem = [];
                    foreach ($groups->Content as $firstGroup){
                        $matrixItem[$firstGroup->LevelName]['name'] = $firstGroup->LevelName;
                        $matrixItem[$firstGroup->LevelName]['id'] = $firstGroup->Id;
                        $matrixItem[$firstGroup->LevelName]['data'][] = $firstGroup;
                    }
                    foreach ($matrixItem as $key => $value) {
                        $item_output .= '<li><h4>';
                        $item_output .= $value['name'];
                        if($item->type == 'store-api-product-cat'){
                            $item_output .= '<a href="'.sa_get_permalink($value['id']).'" class="pull-right">All</a>';
                        }
                        $item_output .= '</h4>';
                        if( count($value['data']) ){
                            $item_output .= '<ul class="store-api-sub-menu-1">';
                            foreach ($value['data'] as $childGroup) {
                                $linkID = $childGroup->LinkId;
                                $url = add_query_arg(['linkParent'=>$linkID], sa_get_permalink($childGroup->Id));
                                $item_output .= '<li>';
                                $item_output .= '<a data-parent-id="'.$value['id'].'" data-type="'.$item->type.'" data-group-id="'.$childGroup->Id.'" data-link-id="'.$childGroup->LinkId.'" href="'.$url.'" >'.$childGroup->Name.'</a>';
                                $item_output .= '</li>';
                            }
                            $item_output .= '</ul>';
                        }
                        $item_output .= '</li>';
                    }
                }
                $item_output .= '</ul></div>';
                return $item_output;
                break;
            default:
                return $item_output;
                break;
        }
    }


    /**
     * AJAX Load Location From API SERVER
     * @author Hien (Hamilton) H.HO
     * @date 09-10-2017
     */
    public function ajax_load_location(){
        global $product_api;
        $response = [];
        $rs1 = $rs2 = NULL;
        $target = isset( $_POST['target'] ) ? (int)$_POST['target']+1 : 1;
        $nextTarget = $target+1;
        $data = ['LocationId' => $_POST['id']];

        if(isset( $_POST['device'] ) && !empty($_POST['device']) && (int)$_POST['device'] > 0 && $target >= 11){
            $data['DeviceId'] = $_POST['device'];
        }

        $locations = $product_api->post('GetLocationItem/JSON', $data);

        if( isset($_POST['id']) && (int)$_POST['id'] == 0){
            ob_start();
            ?>
            <li><a href="" data-id="0" data-target="<?=$target?>" class="location-item-click" data-device="0"><?php _e("All", __TEXTDOMAIN__) ?></a></li>
            <?php
            $rs1 = ob_get_clean();
        }
        if( $locations->StatusCode == 200 && count( $locations->Content ) ){
            $rs1 = '<li><a href="" data-id="0" data-target="'. $target .'" class="location-item-click" data-device="0">'.__("All", __TEXTDOMAIN__).'</a></li>';
            $rs2 = '<li><a href="" data-id="0" data-target="'. $nextTarget .'" class="location-item-click" data-device="0">'.__("All", __TEXTDOMAIN__).'</a></li>';
            foreach ($locations->Content as $location){
                if($location->LocationLevelName == "Devices Used"){
                    $rs2 .= '<li><a href="" data-id="'.$location->Id.'" data-target="'.$nextTarget.'" class="location-item-click" data-device="'.$location->Id.'">'.$location->LocationItemName.'</a></li>';
                }else{
                    $rs1 .= '<li><a href="" data-id="'.$location->Id.'" data-target="'.$target.'" class="location-item-click" data-device="">'.$location->LocationItemName.'</a></li>';
                }
            }
            
        }else{
            ob_start();
            ?>
            <li><a href="" data-id="0" data-target="<?=$target?>" class="location-item-click" data-device="0"><?php _e("All", __TEXTDOMAIN__) ?></a></li>
            <?php
            $rs1 = ob_get_clean();
        }
        if( !empty(trim($rs1)) ){
            $response['rs1'] = $rs1;
        }
        if( !empty(trim($rs2)) ){
            $response['rs2'] = $rs2;
        }
        echo json_encode($response);
        die();
    }

    /**
     * AJAX load city by industry id
     * @author Hien (Hamilton) H.HO
     * @date 18-03-2017
     */
    public function ajaxLoadCityByIndustry(){
        global $product_api;
        $response = [
            'status' => 400,
            'msg' => "Data Not found!",
            'data' => '<option>All</option>'
        ];
        $industryID = get_option('wpsa_industry_id', 4);
        $industryID = isset($_POST['Industry']) && !empty($_POST['Industry']) ? $_POST['Industry'] : $industryID;
        $cities = $product_api->post('GetCities/JSON', ['id'=>$industryID]);
        if( $cities->StatusCode == 200 && count( $cities->Content ) ){
            $response = [
                'status' => 200,
                'msg' => "Load data successful!"
            ];
            $data = '<option>All</option>';
            foreach ($cities->Content as $city) {
                $data .= '<option value="'. $city->Id .'">'. $city->LocationItemName .'</option>';
            }
            $response['data'] = $data;
        }else{
            $response = [
                'status' => $cities->StatusCode,
                'msg' => $cities->Error
            ];
        }
        echo json_encode($response);
        die();
    }

    /**
     * AJAX load Store by country id
     * @author Hien (Hamilton) H.HO
     * @date 18-03-2017
     */
    public function ajaxLoadStoreByCountry(){
        global $product_api;
        $response = [
            'status' => 400,
            'msg' => "Data Not found!",
            'data' => '<option>All</option>'
        ];
        $countryID = get_option('wpsa_industry_id', 4);
        $countryID = isset($_POST['City']) && !empty($_POST['City']) ? $_POST['City'] : $countryID;
        $stores = $product_api->post('GetStoreLocation/JSON', ['CityId'=>$countryID, 'IsAll' => 0]);
        if( $stores->StatusCode == 200 && count( $stores->Content ) ){
            $response = [
                'status' => 200,
                'msg' => "Load data successful!"
            ];
            $data = '<option>All</option>';
            foreach ($stores->Content as $store) {
                $data .= '<option value="'. $store->Id .'">'. $store->LocationItemName .'</option>';
            }
            $response['data'] = $data;
        }else{
            $response = [
                'status' => $stores->StatusCode,
                'msg' => $stores->Error
            ];
        }
        echo json_encode($response);
        die();
    }

    /**
     *
     */
    public function ajaxPostAPI(){
        global $product_api;
        $response = [
            'status' => 400,
            'msg' => "Data Not found!",
            'data' => NULL
        ];
        $url = isset($_POST['url'] ) && !empty($_POST['url']) ? $_POST['url'] : NULL;
        if(!empty($url)){
            $params = $_POST['params'] ? $_POST['params'] : NULL;
            $results = $product_api->post($url, $params);
            if( $results->StatusCode == 200 && count( $results->Content ) ){
                $response = [
                    'status' => 200,
                    'msg' => "Load data successful!"
                ];
                $response['data'] = $results->Content;
            }else{
                $response = [
                    'status' => $results->StatusCode,
                    'msg' => $results->Error
                ];
            }
        }
        echo json_encode($response);
        die();
    }
    /**
     *
     */
    public function ajaxPostMenu(){
        global $product_api;
        $response = [
            'status' => 400,
            'msg' => "Data Not found!",
            'data' => NULL
        ];
        $GroupID = isset($_POST['GroupID'] ) && !empty($_POST['GroupID']) ? $_POST['GroupID'] : 0;
        $LinkParent = isset($_POST['LinkParent'] ) && !empty($_POST['LinkParent']) ? $_POST['LinkParent'] : 0;
        $ListGroupIDs = [];
        if( isset($_POST['ListGroupIDs'] ) && !empty($_POST['ListGroupIDs']) ){
            $ListGroupIDs = explode(',', $_POST['ListGroupIDs']);
        }
        if(!empty($GroupID)){
            $requestPost = ['GroupId' => $GroupID, 'ProductGroupType' => 2];
            if( isset($LinkParent) && $LinkParent != 0 ){
                $requestPost['LinkParent'] = $LinkParent;
            }
            $results = $product_api->post('GetProductGroup/json', $requestPost);
            $item_output = '';
            if( $results->StatusCode == 200 && count($results->Content)){
                $matrixItem = [];
                foreach ($results->Content as $firstGroup){
                    $matrixItem[$firstGroup->LevelName]['name'] = $firstGroup->LevelName;
                    $matrixItem[$firstGroup->LevelName]['id'] = $firstGroup->Id;
                    $matrixItem[$firstGroup->LevelName]['data'][] = $firstGroup;
                }
                foreach ($matrixItem as $key => $value) {
                    $item_output .= '<li><h4>';
                    $item_output .= $value['name'];
                    $ListGroupIDs[] = $value['id'];
                    $type = isset($_POST['Type']) && !empty($_POST['Type']) ? $_POST['Type'] : 'store-api-group';
                    if($type == 'store-api-product-cat'){
                        $item_output .= '<a href="'.add_query_arg(['groupIds' => $ListGroupIDs], sa_get_permalink($value['id']) ).'" class="pull-right">All</a>';
                    }
                    $item_output .= '</h4>';
                    if( count($value['data']) ){
                        $item_output .= '<ul class="store-api-sub-menu-1">';
                        $i = 1;
                        foreach ($value['data'] as $childGroup) {
                            if( $i > 7 ) break;
                            $linkID = $childGroup->LinkId;
                            $url = add_query_arg(['linkParent'=>$linkID], sa_get_permalink($childGroup->Id));
                            $item_output .= '<li>';
                            $item_output .= '<a data-parent-id="'.implode(',', $ListGroupIDs).'" data-type="'.$type.'" data-group-id="'.$childGroup->Id.'" data-link-id="'.$childGroup->LinkId.'" href="'.$url.'" >'.$childGroup->Name.'</a>';
                            $item_output .= '</li>';
                            $i++;
                        }
                        $item_output .= '</ul>';
                    }
                    $item_output .= '</li>';
                }
                $response = [
                    'status' => 200,
                    'msg' => "Load data successful!"
                ];
                $response['data'] = $item_output;
            }else{
                $response = [
                    'status' => $results->StatusCode,
                    'msg' => $results->Error
                ];
            }
        }
        echo json_encode($response);
        die();
    }
}

endif;

return new WCPG_Admin_Menus();