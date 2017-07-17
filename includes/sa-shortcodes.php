<?php
/**
 * Get groups by parent id
 */
add_shortcode( 'get_group', 'shortCodeGetGroup' );
function shortCodeGetGroup( $atts ) {
	global $product_api;
    $a = shortcode_atts( array(
        'parent_id' => 0
    ), $atts );
    if( $a['parent_id'] > 0 ){
    	$groups = $product_api->post('GetProductGroup/json', ['id'=>$a['parent_id']]);
    }else{
    	$groups = $product_api->post('GetProductGroup/json');
    }
    ob_start();
    if( $groups->StatusCode == 200 ){
    	foreach ($groups->Content as $group) {
    		echo "<h3>", $group->Name, "</h3>";
    	}
    }else{
    	echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get product by group id
 */
add_shortcode( 'get_product', 'shortCodeGetProduct' );
function shortCodeGetProduct( $atts ) {
    global $product_api;
    $a = shortcode_atts( array(
        'GroupId' => 0
    ), $atts );
    if( $a['groupid'] > 0 ){
        $groups = $product_api->post('GetProduct/JSON', ['GroupId'=>$a['GroupId']]);
    }else{
        $groups = $product_api->post('GetProduct/JSON', ['All'=>true]);
    }
    ob_start();
    if( $groups->StatusCode == 200 ){
        foreach ($groups->Content as $group) {
            echo "<h3>", $group->Name, "</h3>";
        }
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get sold by type
 */
add_shortcode( 'get_sold_by_type', 'shortCodeGetSoldByType' );
function shortCodeGetSoldByType( $atts ) {
    global $product_api;
    $groups = $product_api->post('GetSoldByType/JSON');
    ob_start();
    if( $groups->StatusCode == 200 ){
        foreach ($groups->Content as $group) {
            echo "<h3>", $group->Name, "</h3>";
        }
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get sold by product id or sold by type id
 */
add_shortcode( 'get_sold_by', 'shortCodeGetSoldBy' );
function shortCodeGetSoldBy( $atts ) {
    global $product_api;
    $a = shortcode_atts( array(
        'ProductId' => 3414,
        'SoldByTypeId' => 6
    ), $atts );
    $groups = $product_api->post('GetSoldBy/JSON', ['ProductId'=>$a['ProductId'], 'SoldByTypeId' => $a['SoldByTypeId']]);
    ob_start();
    if( $groups->StatusCode == 200 ){
        print_r( $groups );
        foreach ($groups->Content as $group) {
            echo "<h3>", $group->GenerateKeyName, "</h3>";
        }
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get advanced setup by product id or sold by id
 */
add_shortcode( 'get_advanced_setup', 'shortCodeGetAdvancedSetup' );
function shortCodeGetAdvancedSetup( $atts ) {
    global $product_api;
    $a = shortcode_atts( array(
        'ProductId' => 2,
        'SoldById' => 3250
    ), $atts );
    $groups = $product_api->post('GetAdvancedSetup/JSON', ['ProductId'=>$a['ProductId'], 'SoldById' => $a['SoldById']]);
    ob_start();
    if( $groups->StatusCode == 200 ){
        foreach ($groups->Content as $group) {
            echo "<h3>", $group->SoldById, "</h3>";
        }
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get sell price
 */
add_shortcode( 'get_sell_price', 'shortCodeGetSellPrice' );
function shortCodeGetSellPrice( $atts ) {
    global $product_api;
    $a = shortcode_atts( array(
        'ProductId' => 3394,
        'SoldById' => 3465,
        'PriceLevelId' => 1,
        'SellPriceIndex' => 1
    ), $atts );
    $groups = $product_api->post('GetSellPrice/JSON', ['ProductId'=>$a['ProductId'], 'SoldById' => $a['SoldById'], 'PriceLevelId' => $a['PriceLevelId'], 'SellPriceIndex' => $a['SellPriceIndex']]);
    ob_start();
    if( $groups->StatusCode == 200 ){
        print_r( $groups->Content );
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get product image
 */
add_shortcode( 'get_product_image', 'shortCodeGetProductImage' );
function shortCodeGetProductImage( $atts ) {
    global $product_api;
    $a = shortcode_atts( array(
        'id' => 147
    ), $atts );
    $groups = $product_api->post('GetProductImage/json', ['id'=>$a['id']]);
    ob_start();
    if( $groups->StatusCode == 200 ){
        print_r( $groups->Content );
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get course type
 */
add_shortcode( 'get_course_type', 'shortCodeGetCourseType' );
function shortCodeGetCourseType( $atts ) {
    global $product_api;
    $a = shortcode_atts( array(
        'id' => 347
    ), $atts );
    $groups = $product_api->post('GetCourseType/json', ['id'=>$a['id']]);
    ob_start();
    if( $groups->StatusCode == 200 ){
        print_r($groups->Content);
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get cooking style
 */
add_shortcode( 'get_cooking_style', 'shortCodeGetCookingStyle' );
function shortCodeGetCookingStyle( $atts ) {
    global $product_api;
    $a = shortcode_atts( array(
        'id' => 347
    ), $atts );
    $groups = $product_api->post('GetCookingStyle/json', ['id'=>$a['id']]);
    ob_start();
    if( $groups->StatusCode == 200 ){
        print_r($groups->Content);
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get card issuer type
 */
add_shortcode( 'get_card_issuer_type', 'shortCodeGetCardIssuerType' );
function shortCodeGetCardIssuerType( $atts ) {
    global $product_api;
    $groups = $product_api->post('GetCardIssuerType/json');
    ob_start();
    if( $groups->StatusCode == 200 ){
        foreach ($groups->Content as $group) {
            echo "<h3>", $group->Name, "</h3>";
        }
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get currency type
 */
add_shortcode( 'get_currency_type', 'shortCodeGetCurrencyType' );
function shortCodeGetCurrencyType( $atts ) {
    global $product_api;
    $groups = $product_api->post('GetCurrencyType/json');
    ob_start();
    if( $groups->StatusCode == 200 ){
        foreach ($groups->Content as $group) {
            echo "<h3>", $group->CurrencyName, "</h3>";
        }
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get discount key type
 */
add_shortcode( 'get_discount_key_type', 'shortCodeGetDiscountKeyType' );
function shortCodeGetDiscountKeyType( $atts ) {
    global $product_api;
    $groups = $product_api->post('GetDiscountKeyType/json');
    ob_start();
    if( $groups->StatusCode == 200 ){
        foreach ($groups->Content as $group) {
            echo "<h3>", $group->Name, "</h3>";
        }
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get order type
 */
add_shortcode( 'get_order_type', 'shortCodeGetOrderType' );
function shortCodeGetOrderType( $atts ) {
    global $product_api;
    $groups = $product_api->post('GetOrderType/json');
    ob_start();
    if( $groups->StatusCode == 200 ){
        foreach ($groups->Content as $group) {
            echo "<h3>", $group->Name, "</h3>";
        }
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get product type
 */
add_shortcode( 'get_product_type', 'shortCodeGetProductType' );
function shortCodeGetProductType( $atts ) {
    global $product_api;
    $groups = $product_api->post('GetProductType/json');
    ob_start();
    if( $groups->StatusCode == 200 ){
        foreach ($groups->Content as $group) {
            echo "<h3>", $group->Name, "</h3>";
        }
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get tender type
 */
add_shortcode( 'get_tender_type', 'shortCodeGetTenderType' );
function shortCodeGetTenderType( $atts ) {
    global $product_api;
    $a = shortcode_atts( array(
        'id' => 501
    ), $atts );
    if( $a['id'] ){
        $groups = $product_api->post('GetTenderType/json', ['id'=>$a['id']]);
    }else{
        $groups = $product_api->post('GetTenderType/json');
    }
    ob_start();
    if( $groups->StatusCode == 200 ){
        foreach ($groups->Content as $group) {
            echo "<h3>", $group->Name, "</h3>";
        }
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}

/**
 * Get product information
 */
add_shortcode( 'get_product_information', 'shortCodeGetProductInformation' );
function shortCodeGetProductInformation( $atts ) {
    global $product_api;
    $a = shortcode_atts( array(
        'id' => 56
    ), $atts );
    if( $a['id'] ){
        $groups = $product_api->post('GetProductInformation/JSON', ['id'=>$a['id']]);
    }else{
        $groups = $product_api->post('GetProductInformation/JSON');
    }
    ob_start();
    if( $groups->StatusCode == 200 ){
        foreach ($groups->Content as $group) {
            echo "<h3>", $group->Information, "</h3>";
        }
    }else{
        echo $groups->Error;
    }
    return ob_get_clean();
}