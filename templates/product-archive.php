<?php get_header(); ?>
<?php global $wp_query, $product_api, $store_api; $groupID = $wp_query->query_vars['group'];?>
<?php

    $ListGroupIDs = [];
    if( isset( $_REQUEST['groupIds'] ) ){
        $ListGroupIDs = $_REQUEST['groupIds'];
    }
    $ListGroupIDs[] = $groupID;
    $ListGroupIDs = array_unique($ListGroupIDs);
    foreach ($ListGroupIDs as $key => $value) {
        $ListGroupIDs[$key] = (int)$value;
    }

    $take = 12;
    if( isset( $_REQUEST['take'] ) ){
        $take = $_REQUEST['take'];
    }
    
    $order = 1;
    if( isset( $_REQUEST['order'] ) ){
        $order = $_REQUEST['order'];
    }

    $page = $skip = 0;

    if( isset( $_REQUEST['page'] ) && $_REQUEST['page'] > 0 ){
        $page = $_REQUEST['page'];
        $skip = $take * ($page - 1);
    }
    
    $products = $product_api->post('GetProducts/JSON', ['Type'=>0, 'GroupIds'=> $ListGroupIDs, 'OrderBy' => (int)$order, 'Skip' => $skip, 'Take' => $take, 'ShowAll' => 1]);

    $sum		=	$products->Content->Total; 
    $pages		=	($sum-($sum%$take))/$take;
	if ($sum % $take <> 0){
		$pages = $pages + 1;
	}
    $page		=	($page==0)?1:(($page>$pages)?$pages:$page);
    $min 		= 	abs($page-1) * $take;
?>
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <div class="container">
            <div class="row" style="margin-top: 40px;">
                <div class="col-md-12">
                    <ol class="breadcrumb">
                        <li><a href="<?=home_url()?>">Home</a></li>
                        <li class="active">Products</li>
                    </ol>
                </div>
            </div>
            <div class="row" style="margin-top: 20px;">
                <?php get_sidebar(); ?>
                <div class="col-md-9" id="main-content">
                    <div class="product-sort">
                        <div class="row">
                            <div class="col-md-7 col-sm-8">
                                <form name="" action="" class="form-inline product-filter" method="get">
                                    <div class="form-group">
                                        <select onchange="addUrlParam('<?= curPageURL() ?>','take',this.value,this.options[this.selectedIndex].getAttribute('data-cpage'));" name="take" class="form-control">
                                            <option <?php selected($take, 12); ?> value="12" data-cpage="<?=12>=$sum?'1':'0'?>">12 items/page</option>
                                            <option <?php selected($take, 24); ?> value="24" data-cpage="<?=24>=$sum?'1':'0'?>">24 items/page</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select onchange="addUrlParam('<?=curPageURL()?>','order',this.value,0)" name="order" class="form-control">
                                            <option <?php selected($order, 1); ?> value="1">Name</option>
                                            <option <?php selected($order, 2); ?> value="2">Price</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Women's 2,630 items</label>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-5 col-sm-4">
                                <?php showPageNavigation($page, $pages, curPageURL());?>
                            </div>
                        </div>
                    </div><!-- END .product-sort -->
                    <div class="list-products">
                        <div class="row">
                            <?php if( $products->StatusCode == 200 && count($products->Content->Products)): ?>
                                <?php foreach ($products->Content->Products as $product): ?>
                                    <?php
                                        $price = !empty($product->SellPrice) && !empty($product->SellPrice) ? $product->SellPrice->Price : 0;
                                    ?>
                                    <div class="product-item">
                                        <div class="home-product-img">
                                            <?php if( isset($product->ProductImage) && !empty($product->ProductImage->ImageUrl) ): ?>
                                                <a href="<?php echo sa_get_permalink($product->Id, 'product'); ?>"><img src="http://dev.cloudsales.xero-connect.com/UserData/<?php echo $product->ProductImage->ImageUrl; ?>" class="img-responsive" alt="<?php echo $product->ProductImage->ImageName; ?>"/></a>
                                            <?php else: ?>
                                                <a href="<?php echo sa_get_permalink($product->Id, 'product'); ?>"><img src="<?php echo $store_api->plugin_url(); ?>/assets/images/no-image.png" class="img-responsive no-image" alt="<?php echo $product->Name; ?>"></a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="media">
                                            <div class="media-body">
                                                <h4 class="home-product-title"><a href="<?php echo sa_get_permalink($product->Id, 'product'); ?>"><?php echo $product->Name; ?></a></h4>
                                                <p>SIDE INSERT MIDI DRESS</p>
                                                <div class="product-price">
                                                    <span class="product-price-normal">$<?= $price ?>.00</span>
                                                </div>
                                            </div>
                                            <div class="media-right">
                                                <a href="#">
                                                    <img class="media-object" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/add-to-cart-icon.png" alt="Add to cart">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif;?>
                        </div><!-- END .row -->
                    </div><!-- END .list-products -->
                    <?php showPageNavigation($page, $pages, curPageURL());?>
                </div>
            </div>
        </div>
    </main><!-- .site-main -->
</div><!-- .content-area -->
<?php get_footer(); ?>