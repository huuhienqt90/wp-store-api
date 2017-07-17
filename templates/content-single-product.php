<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
global $wp_query, $product_api, $store_api;

$sellPriceLevel = get_option('wpsa_sell_price_level', 0);
$sellPriceIndex = get_option('wpsa_sell_price_index', 0);
$productID = $wp_query->query_vars['product'] ? $wp_query->query_vars['product'] : 0;
$productDetail = $product_api->post('GetProductDetail/JSON', ['ProductId'=>$productID, 'SellpriceIndex'=>$sellPriceIndex, 'SellpriceLevel'=>$sellPriceLevel]);
$title = $description = '';
$price = 0;
if( isset($productDetail->Content) && count($productDetail->Content) ){
    $price = !empty($productDetail->Content->SoldBys[0]->SellPrice) && !empty($productDetail->Content->SoldBys[0]->SellPrice) ? $productDetail->Content->SoldBys[0]->SellPrice->Price : 0;
    $title = $productDetail->Content->Name;
    $description = $productDetail->Content->SoldBys[0]->AdvancedSetup->Description;
    $images = $productDetail->Content->SoldBys[0]->AdvancedSetup->ProductImages;
    $image = isset($productDetail->Content->SoldBys[0]->AdvancedSetup->ProductImages) && !empty($productDetail->Content->SoldBys[0]->AdvancedSetup->ProductImages) ? 'http://dev.cloudsales.xero-connect.com/UserData/'.$productDetail->Content->SoldBys[0]->AdvancedSetup->ProductImages : $store_api->plugin_url().'/assets/images/no-image.png';

}else{
    exit; // Exit if accessed directly
}
?>
<div class="main-content" style="margin-top: 20px;">
    <?php do_action( 'store_api_message' ); ?>
    <form action="" method="post">
        <input id="product_id" type="hidden" name="product_id" value="<?php echo $productID; ?>" />
        <div class="container">
            <ol class="breadcrumb">
                <li><a href="#">Home</a></li>
                <li><a href="#">Product</a></li>
                <li class="active"><?php echo $title; ?></li>
            </ol>
            <div class="row">
                <div class="col-md-4">
                    <div class="main-product-img">
                        <?php if($images): ?>
                            <?php foreach ($images as $img): if(strlen($img->ImageUrl) <= 0) continue; ?>
                                <div class="product-image-slider-item"><span class="helper"></span><img src="http://dev.cloudsales.xero-connect.com/UserData/<?php echo $img->ImageUrl; ?>" alt="<?php echo $img->ImageName; ?>" class="img-responsive"></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="product-image-slider-item"><span class="helper"></span><img src="<?php echo $store_api->plugin_url().'/assets/images/no-image.png'; ?>" alt="<?php echo $img->ImageName; ?>" class="img-responsive"></div>
                        <?php endif; ?>
                    </div>
                    <div class="main-product-list">
                        <?php if($images): ?>
                            <?php foreach ($images as $img): if(strlen($img->ImageUrl) <= 0) continue; ?>
                                <div><img src="http://dev.cloudsales.xero-connect.com/UserData/<?php echo $img->ImageUrl; ?>" alt="<?php echo $img->ImageName; ?>" class="img-responsive"></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div><img src="<?php echo $store_api->plugin_url().'/assets/images/no-image.png'; ?>" alt="<?php echo $img->ImageName; ?>" class="img-responsive"></div>
                        <?php endif; ?>
                    </div>
                    <script type="text/javascript">
                        jQuery(document).ready(function($){
                            $('.main-product-img').slick({
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                arrows: true,
                                asNavFor: '.main-product-list'
                            });
                            $('.main-product-list').slick({
                                slidesToShow: 4,
                                slidesToScroll: 1,
                                asNavFor: '.main-product-img',
                                dots: false,
                                centerMode: false,
                                focusOnSelect: true
                            });
                            // Configure/customize these variables.
                            var showChar = 200;  // How many characters are shown by default
                            var ellipsestext = "...";
                            var moretext = "Show more";
                            var lesstext = "Show less";
                            

                            $('.product-description').each(function() {
                                var content = $(this).html();
                         
                                if(content.length > showChar) {
                         
                                    var c = content.substr(0, showChar);
                                    var h = content.substr(showChar, content.length - showChar);
                         
                                    var html = c + '<span class="moreellipses">' + ellipsestext+ '&nbsp;</span><span class="morecontent"><span>' + h + '</span>&nbsp;&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';
                         
                                    $(this).html(html);
                                }
                         
                            });
                         
                            $(".morelink").click(function(){
                                if($(this).hasClass("less")) {
                                    $(this).removeClass("less");
                                    $(this).html(moretext);
                                } else {
                                    $(this).addClass("less");
                                    $(this).html(lesstext);
                                }
                                $(this).parent().prev().toggle();
                                $(this).prev().toggle();
                                return false;
                            });
                        });
                    </script>
                </div>
                <div class="col-md-8">
                    <h1 class="product-detail-title" style="margin-top: 0;"><?php echo $title; ?></h1>
                    <h4 class="single-product-price">$<?php echo $price; ?></h4>
                    <div class="product-description">
                        <?php if( !empty($description) ): ?>
                            <?php echo $description; ?>
                        <?php else: ?>
                            Discover Tokyo in Sukajan style with this reversible bomber jacket. Crafted in a silky feel quality with Japanese themed embroidered artworks on the back and a quilted silk mix inner, the jacket belongs on the iconic streets of Yokosuka.
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <?php if($productDetail->Content->Children): ?>
                            <div class="product-colors col-md-12">
                                <h3 class="product-colors-label">Select Color:</h3>
                                <input type="hidden" name="data[Color]" id="inputProductColor" />
                                <div class="color-items">
                                    <?php foreach ($productDetail->Content->Children as $color): ?>
                                        <?php
                                            $size = [];
                                            if(isset($color->Children) && count($color->Children)){
                                                foreach ($color->Children as $sizeChild) {
                                                    if( isset($sizeChild->Size->Id) ){
                                                        $key = trim($sizeChild->ProductId.$sizeChild->Size->Id);
                                                        $sizePrice = !empty($sizeChild->SoldBys[0]->SellPrice) && !empty($sizeChild->SoldBys[0]->SellPrice) ? $sizeChild->SoldBys[0]->SellPrice->Price : 0;
                                                        $descriptioncolor=htmlentities($sizeChild->SoldBys[0]->AdvancedSetup->Description, ENT_QUOTES);
                                                        $size[$key] = [
                                                            'id' => $sizeChild->Size->Id,
                                                            'productId' => $sizeChild->ProductId,
                                                            'name' => $sizeChild->Size->Name,
                                                            'value' => $sizeChild->Size->Value,
                                                            'price' => $sizePrice,
                                                            'swatchColor' => $sizeChild->SoldBys[0]->AdvancedSetup->SwatchColor,
                                                            'description' => $descriptioncolor
                                                        ];
                                                    }
                                                }
                                            }
                                        ?>
                                        <a href="#" class="product-color" id="product-color-<?php echo $color->Color->Id; ?>" data-color="<?php echo $color->Color->Id; ?>" data-colorname="<?php echo $color->Color->Name; ?>" style="background: #<?php echo $color->Color->Value; ?>;" data-size='<?php echo json_encode($size); ?>'></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="product-swatch col-md-12">
                                <h3 class="product-colors-label">Swatch:</h3>
                                <input type="hidden" name="data[swatch]" id="inputProductSwatch" />
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="swatch-items">
                                            <a  class="swatch-itema" href="#" data-src="<?= $store_api->plugin_url().'/assets/images/swatch.png'; ?>"><span class="swatch-item" style="background: url('<?= $store_api->plugin_url().'/assets/images/swatch.png'; ?>');background-size: cover;"></span></a>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="position: relative;">
                                        <span class="swatch-show" style="position: absolute;"><img src="<?= $store_api->plugin_url().'/assets/images/swatch.png'; ?>" /></span>
                                    </div>
                                </div>
                            </div>
                            <div class="product-sizes col-md-8">
                                <h3 class="product-colors-label">Select Size:</h3>
                                <input type="hidden" name="data[Size]" id="inputProductSize" />
                                <div class="size-items">
                                
                                </div>
                                <div class="product-sizes-chart"><a href="#">Size Chart</a></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="add-to-cart-btn">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-inline">
                                    <div class="form-group">
                                        <label>Quantity</label>
                                        <input type="number" onkeypress='return event.charCode >= 48 && event.charCode <= 57' class="form-control form-control-small" name="quantity" value="1" placeholder="Quantity" />
                                        <input type="submit" class="btn btn-danger" name="add_to_cart" value="Add to cart"/>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="product-information">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="text-uppercase">MORE PRODUCTS FROM COUNTRY ROAD</h3>
                    </div>
                </div>
                <div class="list-products">
                    <div class="row">
                        <div class="col-md-3 col-xs-6 col-479">
                            <div class="home-product-img">
                                <a href=""><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/product-item-1.jpg" class="img-responsive" alt=""></a>
                                <span class="home-product-img-label text-uppercase">New</span>
                            </div>
                            <div class="media">
                                <div class="media-body">
                                    <h4 class="home-product-title"><a href="">BLUE ILLUSION</a></h4>
                                    <p>SIDE INSERT MIDI DRESS</p>
                                    <div class="product-price">
                                        <span class="product-price-old">$147.00</span>
                                        <span class="product-price-new">$139.99</span>
                                    </div>
                                </div>
                                <div class="media-right">
                                    <a href="#">
                                        <img class="media-object" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/add-to-cart-icon.png" alt="Add to cart">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6 col-479">
                            <div class="home-product-img">
                                <a href=""><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/product-item-2.jpg" class="img-responsive" alt=""></a>
                            </div>
                            <div class="media">
                                <div class="media-body">
                                    <h4 class="home-product-title"><a href="">WITCHERY</a></h4>
                                    <p>TIE FRONT JERSEY DRESS</p>
                                    <div class="product-price">
                                        <span class="product-price-normal">$119.95</span>
                                    </div>
                                </div>
                                <div class="media-right">
                                    <a href="#">
                                        <img class="media-object" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/add-to-cart-icon.png" alt="Add to cart">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6 col-479">
                            <div class="home-product-img">
                                <a href=""><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/product-item-3.jpg" class="img-responsive" alt=""></a>
                            </div>
                            <div class="media">
                                <div class="media-body">
                                    <h4 class="home-product-title"><a href="">COUNTRY ROAD</a></h4>
                                    <p>TIE WRAP DRESS</p>
                                    <div class="product-price">
                                        <span class="product-price-old">$147.00</span>
                                        <span class="product-price-new">$139.99</span>
                                    </div>
                                </div>
                                <div class="media-right">
                                    <a href="#">
                                        <img class="media-object" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/add-to-cart-icon.png" alt="Add to cart">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6 col-479">
                            <div class="home-product-img">
                                <a href=""><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/product-item-4.jpg" class="img-responsive" alt=""></a>
                            </div>
                            <div class="media">
                                <div class="media-body">
                                    <h4 class="home-product-title"><a href="">BLUE ILLUSION</a></h4>
                                    <p>SIDE INSERT MIDI DRESS</p>
                                    <div class="product-price">
                                        <span class="product-price-old">$147.00</span>
                                        <span class="product-price-new">$139.99</span>
                                    </div>
                                </div>
                                <div class="media-right">
                                    <a href="#">
                                        <img class="media-object" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/add-to-cart-icon.png" alt="Add to cart">
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 col-xs-6 col-479">
                            <div class="home-product-img">
                                <a href=""><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/product-item-1.jpg" class="img-responsive" alt=""></a>
                                <span class="home-product-img-label text-uppercase">New</span>
                            </div>
                            <div class="media">
                                <div class="media-body">
                                    <h4 class="home-product-title"><a href="">BLUE ILLUSION</a></h4>
                                    <p>SIDE INSERT MIDI DRESS</p>
                                    <div class="product-price">
                                        <span class="product-price-old">$147.00</span>
                                        <span class="product-price-new">$139.99</span>
                                    </div>
                                </div>
                                <div class="media-right">
                                    <a href="#">
                                        <img class="media-object" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/add-to-cart-icon.png" alt="Add to cart">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6 col-479">
                            <div class="home-product-img">
                                <a href=""><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/product-item-2.jpg" class="img-responsive" alt=""></a>
                            </div>
                            <div class="media">
                                <div class="media-body">
                                    <h4 class="home-product-title"><a href="">WITCHERY</a></h4>
                                    <p>TIE FRONT JERSEY DRESS</p>
                                    <div class="product-price">
                                        <span class="product-price-normal">$119.95</span>
                                    </div>
                                </div>
                                <div class="media-right">
                                    <a href="#">
                                        <img class="media-object" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/add-to-cart-icon.png" alt="Add to cart">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6 col-479">
                            <div class="home-product-img">
                                <a href=""><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/product-item-3.jpg" class="img-responsive" alt=""></a>
                            </div>
                            <div class="media">
                                <div class="media-body">
                                    <h4 class="home-product-title"><a href="">COUNTRY ROAD</a></h4>
                                    <p>TIE WRAP DRESS</p>
                                    <div class="product-price">
                                        <span class="product-price-old">$147.00</span>
                                        <span class="product-price-new">$139.99</span>
                                    </div>
                                </div>
                                <div class="media-right">
                                    <a href="#">
                                        <img class="media-object" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/add-to-cart-icon.png" alt="Add to cart">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-xs-6 col-479">
                            <div class="home-product-img">
                                <a href=""><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/product-item-4.jpg" class="img-responsive" alt=""></a>
                            </div>
                            <div class="media">
                                <div class="media-body">
                                    <h4 class="home-product-title"><a href="">BLUE ILLUSION</a></h4>
                                    <p>SIDE INSERT MIDI DRESS</p>
                                    <div class="product-price">
                                        <span class="product-price-old">$147.00</span>
                                        <span class="product-price-new">$139.99</span>
                                    </div>
                                </div>
                                <div class="media-right">
                                    <a href="#">
                                        <img class="media-object" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/add-to-cart-icon.png" alt="Add to cart">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <style type="text/css">
        /*set a border on the images to prevent shifting*/
        #product-images{
            position: relative;
            z-index: 999;
        }
         #product-images img{border:2px solid white;}
         
        /*Change the colour*/
        /* .active img{border:2px solid #333 !important;} */
    </style>
</div>