jQuery(document).ready(function($){
	var loading = false;
    
    setTimeout(function() {
        $("a.product-color:first-child").trigger("click");
    },0.1);
    
    /*//initiate the plugin and pass the id of the div containing gallery images
	$("#product-image").elevateZoom({gallery:'product-images', cursor: 'pointer', galleryActiveClass: 'active', imageCrossfade: true, loadingIcon: 'http://www.elevateweb.co.uk/spinner.gif'}); */
	$('.product-imgs').click(function(){
		var image = $(this).data('image');
		$('#product-image').attr('src', image);
		return false;
	});

	$('.product-color').click(function(){
		$('.product-color').removeClass('active');
        var size = $(this).data('size');
		var color = $(this).data('colorname');
        $('#inputProductColor').val(color);
		if( size ){
			var productSizes = $(this).parents('.col-md-8').find('.size-items');
			productSizes.html('');
			$.each(size, function(index, value){
				productSizes.append('<a href="#" class="product-size" data-size="'+value.id+'" data-sizename="'+value.name+'" data-price="'+value.price+'" data-swatch="'+value.swatchColor+'" data-productid="'+value.productId+'" data-description="'+value.description+'">'+value.value+'</a>');
			});
            productSizes.find('a:first-child').trigger('click');
		}
		$(this).addClass('active');

		return false;
	});
	$(document).on('click', '.product-size', function(){
        var productId = $(this).data('productid');
		$('.product-size').removeClass('active');
        $('#inputProductSize').val($(this).data('sizename'));
        document.getElementById("product_id").value=productId;
        $(".product-description").html($(this).data('description'));
        $('.product-price').html('$'+$(this).data('price'));
        $('.swatch-items').html('<a class="swatch-itema" href="#" data-src="http://dev.cloudsales.xero-connect.com/UserData/'+$(this).data('swatch')+'"><span class="swatch-item" style="background: url(http://dev.cloudsales.xero-connect.com/UserData/'+$(this).data('swatch')+');background-size: cover;"></span></a>');
		$(this).addClass('active');
        $('.swatch-show').hide();

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

		return false;
	});
	$(document).on('click', '.swatch-itema', function(){
        var src = $(this).data('src');
        $('.swatch-show').html('<img src="'+src+'">').slideDown(500);
		return false;
	});
    
    $(document).on('change','#Industry',function () {
    	var val = $(this).val();

        var data = {
            'action': 'sa_load_city',
            'Industry': val
        };
        if( !loading ){
        	$('.loading').removeClass('hidden');
            loading = true;
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $.post( ajax.ajax_url, data, function(response) {
                $('.loading').addClass('hidden');
                loading = false;
                var dt = JSON.parse(response);
                $('#City').html(dt.data);
                $('#Store').html('<option>All</option>');
            });
        }

        $(this).parent().parent().find('li').removeClass('active');
        $(this).parent().addClass('active');
        return false;
    });

    $(document).on('change','#City',function () {
    	var val = $(this).val();

        var data = {
            'action': 'sa_load_store',
            'City': val
        };
        if( !loading ){
        	$('.loading').removeClass('hidden');
            loading = true;
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $.post(ajax.ajax_url, data, function(response) {
                $('.loading').addClass('hidden');
                loading = false;
                var dt = JSON.parse(response);
                $('#Store').html(dt.data);
            });
        }

        $(this).parent().parent().find('li').removeClass('active');
        $(this).parent().addClass('active');
        return false;
    });
    $(document).on('click', '#mn-header-1> ul> li> a', function () {
        $('#mn-header-1> ul> li> .sub-menu').parent().find('.sub-menu').not($(this).parent().find('.sub-menu').toggle()).hide();
        if($(this).parent().find('.sub-menu').length>0){
            return false;
        }
    });
    $(document).click(function(){
        $("#mn-header-1 ul li .sub-menu").hide();
    });
    $(document).on('click', '.store-api-sub-menu ul li a', function (event) {
        event.preventDefault();
        $(this).parent().parent().find('li').removeClass('active');
        $(this).parent().addClass('active');
        var GroupID = $(this).data('group-id');
        var LinkParent = $(this).data('link-id');
        var Type = $(this).data('type');
        var ListGroupIDs = $(this).data('parent-id');
        $(this).parent().parent().parent().nextAll().remove();
        var parent = $(this).parents('.store-api-sub-menu');

        var data = {
            'action': 'sa_load_menu',
            'GroupID': GroupID,
            'LinkParent': LinkParent,
            'Type': Type,
            'ListGroupIDs': ListGroupIDs
        };
        if( !loading ){
            $('.loading').removeClass('hidden');
            loading = true;
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $.post(ajax.ajax_url, data, function(response) {
                console.log(response);
                $('.loading').addClass('hidden');
                loading = false;
                var dt = JSON.parse(response);
                parent.append(dt.data);
                $('.store-api-sub-full-width').animate({scrollLeft: parent.parent().width()}, 800);
            });
        }
        return false;
    });

});
var addUrlParam = function(url, param, value,cpage) {
    if(cpage==1){
        url = removeParam('page', url);
    }
    if(value!=-1){
        param = encodeURIComponent(param);
        var r = "([&?]|&amp;)" + param + "\\b(?:=(?:[^&#]*))*";
        var a = document.createElement('a');
        var regex = new RegExp(r);
        var str = param + (value ? "=" + encodeURIComponent(value) : "");
        a.href = url;
        var q = a.search.replace(regex, "$1"+str);
        if (q === a.search) {
           a.search += (a.search ? "&" : "") + str;
        } else {
           a.search = q;
        }
        window.location.href = a.href;
    }
}
function removeParam(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}

