<?php

/**
 * Get template part (for templates like the shop-loop).
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 */
function sa_get_template_part($slug, $name = '')
{
    $template = '';

    // Look in yourtheme/slug-name.php and yourtheme/store-api/slug-name.php
    if ($name) {
        $template = locate_template(array("{$slug}-{$name}.php", wpsa()->template_path() . "{$slug}-{$name}.php"));
    }

    // Get default slug-name.php
    if (!$template && $name && file_exists(wpsa()->plugin_path() . "/templates/{$slug}-{$name}.php")) {
        $template = wpsa()->plugin_path() . "/templates/{$slug}-{$name}.php";
    }

    // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/store-api/slug.php
    if (!$template) {
        $template = locate_template(array("{$slug}.php", wpsa()->template_path() . "{$slug}.php"));
    }

    // Allow 3rd party plugins to filter template file from their plugin.
    $template = apply_filters('sa_get_template_part', $template, $slug, $name);

    if ($template) {
        load_template($template, false);
    }
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function sa_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
{
    if (!empty($args) && is_array($args)) {
        extract($args);
    }

    $located = sa_locate_template($template_name, $template_path, $default_path);

    if (!file_exists($located)) {
        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $located), '2.1');
        return;
    }

    // Allow 3rd party plugin filter template file from their plugin.
    $located = apply_filters('sa_get_template', $located, $template_name, $args, $template_path, $default_path);

    do_action('store_api_before_template_part', $template_name, $template_path, $located, $args);

    include($located);

    do_action('store_api_after_template_part', $template_name, $template_path, $located, $args);
}

/**
 * Like sa_get_template, but returns the HTML instead of outputting.
 * @see sa_get_template
 * @since 2.5.0
 * @param string $template_name
 */
function sa_get_template_html($template_name, $args = array(), $template_path = '', $default_path = '')
{
    ob_start();
    sa_get_template($template_name, $args, $template_path, $default_path);
    return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *        yourtheme        /    $template_path    /    $template_name
 *        yourtheme        /    $template_name
 *        $default_path    /    $template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function sa_locate_template($template_name, $template_path = '', $default_path = '')
{
    if (!$template_path) {
        $template_path = wpsa()->template_path();
    }

    if (!$default_path) {
        $default_path = wpsa()->plugin_path() . '/templates/';
    }

    // Look within passed path within the theme - this is priority.
    $template = locate_template(
        array(
            trailingslashit($template_path) . $template_name,
            $template_name
        )
    );

    // Get default template/
    if (!$template) {
        $template = $default_path . $template_name;
    }

    // Return what we found.
    return apply_filters('wpsa_locate_template', $template, $template_name, $template_path);
}

/**
 * Get permalink by ID
 *
 * @param int $id
 * @param string $slug
 * @return string
 */
function sa_get_permalink($id = 0, $slug = 'group')
{
    $permalink = get_option('permalink_structure');
    if (isset($permalink) && !empty($permalink)) {
        return home_url('/' . $slug . '/' . $id);
    } else {
        return add_query_arg([$slug => $id], home_url());
    }
}

/**
 * Get current URL
 *
 * @return string
 */
function curPageURL()
{
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

/**
 * Show page navigation
 *
 * @param $currentPage
 * @param $maxPage
 * @param string $path
 */
function showPageNavigation($currentPage, $maxPage, $path = '')
{

    if ($maxPage <= 1) {
        return;
    }
    $nav = array(
        'left' => 1,
        'right' => 1,
    );
    if ($maxPage < $currentPage) {
        $currentPage = $maxPage;
    }
    $max = $nav['left'] + $nav['right'];
    if ($max >= $maxPage) {
        $start = 1;
        $end = $maxPage;
    } elseif ($currentPage - $nav['left'] <= 0) {
        $start = 1;
        $end = $max + 1;
    } elseif (($right = $maxPage - ($currentPage + $nav['right'])) <= 0) {
        $start = $maxPage - $max;
        $end = $maxPage;
    } else {
        $start = $currentPage - $nav['left'];
        if ($start == 2) {
            $start = 1;
        }

        $end = $start + $max;
        if ($end == $maxPage - 1) {
            ++$end;
        }
    }
    $navig = '<ul class="pagination pull-right" style="margin-top: 0; margin-bottom: 0;">';
    if ($currentPage >= 2) {
        if (($currentPage - 1) != 1) {
            $navig .= '<li><a onclick="addUrlParam(' . "'" . $path . "'" . ',' . "'" . page . "'" . ',' . ($currentPage - 1) . ')" href="javascript:;return false;">PREV</a></li>';
        } else {
            $navig .= '<li><a onclick="addUrlParam(' . "'" . $path . "'" . ',' . "'" . page . "'" . ',1)" href="javascript:;return false;">PREV</a></li>';
        }
        if ($currentPage >= $nav['left']) {
            if ($currentPage - $nav['left'] > 2 && $max < $maxPage) {
                $navig .= '<li><a onclick="addUrlParam(' . "'" . $path . "'" . ',' . "'" . page . "'" . ',1)" href="javascript:;return false;">1</a></li>';
                $navig .= '<li><a href="javascript:;return false;">...</a></li>';
            }
        }
    } else {
        $navig .= '<li class="active"><a href="javascript:;return false;">PREV</a></li>';
    }

    for ($i = $start; $i <= $end; $i++) {
        if ($i == $currentPage) {
            $navig .= '<li class="active"><a href="javascript:;return false;">' . $i . '</a></li>';
        } else {
            $navig .= '<li><a onclick="addUrlParam(' . "'" . $path . "'" . ',' . "'" . page . "'" . ',' . $i . ')" href="javascript:;return false;">' . $i . '</a></li>';
        }
    }
    if ($currentPage <= $maxPage - 1) {
        if ($currentPage + $nav['right'] < $maxPage - 1 && $max + 1 < $maxPage) {
            $navig .= '<li><a href="javascript:;return false;">...</a></li>';
            $navig .= '<li><a href="javascript:;return false;" onclick="addUrlParam(' . "'" . $path . "'" . ',' . "'" . page . "'" . ',' . $maxPage . ')">' . $maxPage . '</a></li>';
        }
        $navig .= '<li><a onclick="addUrlParam(' . "'" . $path . "'" . ',' . "'" . page . "'" . ',' . ($currentPage + 1) . ')" href="javascript:;return false;">NEXT</a></li>';
    } else {
        $navig .= '<li class="active"><a href="javascript:;return false;">NEXT</a></li>';
    }
    $navig .= '</ul>';
    echo $navig;
}

/**
 * Add event meta box
 */
function event_field_box()
{
    add_meta_box('event-field', 'Event information', 'event_field_output', 'event', 'advanced', 'high');
}

add_action('add_meta_boxes', 'event_field_box');
function event_field_output($post)
{
    global $store_api;
    wp_enqueue_script('moment', $store_api->plugin_url().'/bower_components/moment/min/moment.min.js');
    wp_enqueue_script('bootstrap', $store_api->plugin_url().'/bower_components/bootstrap/dist/js/bootstrap.min.js');
    wp_enqueue_script('bootstrap-datetimepicker', $store_api->plugin_url().'/bower_components/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');
    wp_enqueue_style('bootstrap', $store_api->plugin_url().'/bower_components/bootstrap/dist/css/bootstrap.min.css');
    wp_enqueue_style('bootstrap-datetimepicker', $store_api->plugin_url().'/bower_components/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css');
    $event_start = get_post_meta($post->ID, '_event_start', true);
    $event_end = get_post_meta($post->ID, '_event_end', true);
    $event_map = get_post_meta($post->ID, '_event_map', true);
    $event_map_note = get_post_meta($post->ID, '_event_map_note', true);
    $day_table1 = json_decode( get_post_meta($post->ID, '_day_table', true) );
    wp_nonce_field('save_field', 'field_nonce');
    ?>
    <div class="form-group">
        <label for="_event_map">Map: (ex: 39.916775, -101.126286)</label>
        <input type="text" id="_event_map" name="_event_map" class="form-control" value="<?= esc_attr($event_map) ?>"/>
    </div>
    <div class="form-group">
        <label for="_event_map">Note (Map)</label>
        <input type="text" id="_event_map_note" name="_event_map_note" class="form-control" value="<?= esc_attr($event_map_note) ?>"/>
    </div>
    <div class="form-group">
        <label for="_event_start">Date Start: </label>
        <input type="text" id="_event_start" name="_event_start" class="form-control" value="<?= esc_attr($event_start) ?>"/>
    </div>
    <div class="form-group">
        <label for="_event_end">Date End: </label>
        <input type="text" id="_event_end" name="_event_end" class="form-control" value="<?= esc_attr($event_end) ?>"/>
    </div>
    <div class="list-event-dates">
    <?php if($day_table1!=''){
         foreach($day_table1 as $k => $timeSheet){?>
        <p>
           <input readonly="" name="_timeSheet[<?=$k?>][day]" type="text" value="<?=$timeSheet->day?>" />
           <input class="time_date" name="_timeSheet[<?=$k?>][start]" type="text" value="<?=$timeSheet->start?>" /> - 
           <input class="time_date" name="_timeSheet[<?=$k?>][end]" type="text" value="<?=$timeSheet->end?>" /><br />
        </p>
        <?php }
    }?>
    </div>
    <button class="custom-event-date btn btn-danger"><?=__('Custom Event Date')?></button>
    <style>
        #titlediv #title-prompt-text {font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif!important;line-height: 1.4em!important;font-weight: normal;padding: 3px 10px;}
        body {font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif!important;}
        .time_date{width: 80px;text-align: center;}
        input[readonly] {margin-right: 10px;color: #000;}
    </style>
    <script>
        jQuery(document).ready(function($){
            $('#_event_start').datetimepicker({
                format: 'YYYY-MM-DD h:m A'
            });
            $('.time_date').live('click', function(){
                $(this).datetimepicker({
                    format: 'LT'
                }).focus();
            });
            $('#_event_end').datetimepicker({
                format: 'YYYY-MM-DD h:m A',
                useCurrent: false //Important! See issue #1075
                
            });
            $("#_event_start").on("dp.change", function (e) {
                $('#_event_end').data("DateTimePicker").minDate(e.date);
                
            });
            $("#_event_end").on("dp.change", function (e) {
                $('#_event_start').data("DateTimePicker").maxDate(e.date);
            });
            $('.custom-event-date').click(function(){
                if($('#_event_start').val() != '' && $('#_event_end').val() != ''){
                    var startDate = $('#_event_start').val();
                    var endDate = $('#_event_end').val();
                    var time1=startDate.substr(11);
                    var time2=endDate.substr(11);
                    // Returns an array of dates between the two dates
                    var getDates = function(startDate, endDate) {
                        var dates = [],
                            currentDate = startDate,
                            addDays = function(days) {
                                var date = new Date(this.valueOf());
                                date.setDate(date.getDate() + days);
                                return date;
                            };
                        while (currentDate <= endDate) {
                            dates.push(currentDate);
                            currentDate = addDays.call(currentDate, 1);
                        }
                        return dates;
                    };
                    var dateFormat = function () {
                        var    token = /d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,
                            timezone = /\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,
                            timezoneClip = /[^-+\dA-Z]/g,
                            pad = function (val, len) {
                                val = String(val);
                                len = len || 2;
                                while (val.length < len) val = "0" + val;
                                return val;
                            };
                    
                        // Regexes and supporting functions are cached through closure
                        return function (date, mask, utc) {
                            var dF = dateFormat;
                    
                            // You can't provide utc if you skip other args (use the "UTC:" mask prefix)
                            if (arguments.length == 1 && Object.prototype.toString.call(date) == "[object String]" && !/\d/.test(date)) {
                                mask = date;
                                date = undefined;
                            }
                    
                            // Passing date through Date applies Date.parse, if necessary
                            date = date ? new Date(date) : new Date;
                            if (isNaN(date)) throw SyntaxError("invalid date");
                    
                            mask = String(dF.masks[mask] || mask || dF.masks["default"]);
                    
                            // Allow setting the utc argument via the mask
                            if (mask.slice(0, 4) == "UTC:") {
                                mask = mask.slice(4);
                                utc = true;
                            }
                    
                            var    _ = utc ? "getUTC" : "get",
                                d = date[_ + "Date"](),
                                D = date[_ + "Day"](),
                                m = date[_ + "Month"](),
                                y = date[_ + "FullYear"](),
                                H = date[_ + "Hours"](),
                                M = date[_ + "Minutes"](),
                                s = date[_ + "Seconds"](),
                                L = date[_ + "Milliseconds"](),
                                o = utc ? 0 : date.getTimezoneOffset(),
                                flags = {
                                    d:    d,
                                    dd:   pad(d),
                                    ddd:  dF.i18n.dayNames[D],
                                    dddd: dF.i18n.dayNames[D + 7],
                                    m:    m + 1,
                                    mm:   pad(m + 1),
                                    mmm:  dF.i18n.monthNames[m],
                                    mmmm: dF.i18n.monthNames[m + 12],
                                    yy:   String(y).slice(2),
                                    yyyy: y,
                                    h:    H % 12 || 12,
                                    hh:   pad(H % 12 || 12),
                                    H:    H,
                                    HH:   pad(H),
                                    M:    M,
                                    MM:   pad(M),
                                    s:    s,
                                    ss:   pad(s),
                                    l:    pad(L, 3),
                                    L:    pad(L > 99 ? Math.round(L / 10) : L),
                                    t:    H < 12 ? "a"  : "p",
                                    tt:   H < 12 ? "am" : "pm",
                                    T:    H < 12 ? "A"  : "P",
                                    TT:   H < 12 ? "AM" : "PM",
                                    Z:    utc ? "UTC" : (String(date).match(timezone) || [""]).pop().replace(timezoneClip, ""),
                                    o:    (o > 0 ? "-" : "+") + pad(Math.floor(Math.abs(o) / 60) * 100 + Math.abs(o) % 60, 4),
                                    S:    ["th", "st", "nd", "rd"][d % 10 > 3 ? 0 : (d % 100 - d % 10 != 10) * d % 10]
                                };
                    
                            return mask.replace(token, function ($0) {
                                return $0 in flags ? flags[$0] : $0.slice(1, $0.length - 1);
                            });
                        };
                    }();
                    
                    // Some common format strings
                    dateFormat.masks = {
                        "default":      "ddd mmm dd yyyy HH:MM:ss",
                        shortDate:      "m/d/yy",
                        mediumDate:     "mmm d, yyyy",
                        longDate:       "mmmm d, yyyy",
                        fullDate:       "dddd, mmmm d, yyyy",
                        shortTime:      "h:MM TT",
                        mediumTime:     "h:MM:ss TT",
                        longTime:       "h:MM:ss TT Z",
                        isoDate:        "yyyy-mm-dd",
                        isoTime:        "HH:MM:ss",
                        isoDateTime:    "yyyy-mm-dd'T'HH:MM:ss",
                        isoUtcDateTime: "UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"
                    };
                    
                    // Internationalization strings
                    dateFormat.i18n = {
                        dayNames: [
                            "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat",
                            "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"
                        ],
                        monthNames: [
                            "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
                            "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
                        ]
                    };
                    
                    // For convenience...
                    Date.prototype.format = function (mask, utc) {
                        return dateFormat(this, mask, utc);
                    };

                    var dates = getDates(new Date(startDate), new Date(endDate));
                    $('.list-event-dates').html('');
                    dates.forEach(function(date) {
                        var html = '<p>';
                        html+='<input readonly="" name="_timeSheet['+dateFormat(date, "yyyy")+'-'+ dateFormat(date, "mm-d")+'][day]" type="text" value="'+dateFormat(date, "dddd")+','+ dateFormat(date, "mmmm dd")+'" />';
                        html+='<input class="time_date" name="_timeSheet['+dateFormat(date, "yyyy")+'-'+ dateFormat(date, "mm-d")+'][start]" type="text" value="'+time1+'" />'+ ' - ';
                        html+='<input class="time_date" name="_timeSheet['+dateFormat(date, "yyyy")+'-'+ dateFormat(date, "mm-d")+'][end]" type="text" value="'+time2+'" /></br>';
                        html += '</p>';
                        $('.list-event-dates').append(html);
                    });
                }else{
                    alert('Please select start and end date')
                }
                return false;
            });
        });
    </script>
    <?php
}

function event_field_save($post_id)
{

    $field_nonce = $_POST['field_nonce'];
    // Kiểm tra nếu nonce chưa được gán giá trị
    if (!isset($field_nonce)) {
        return;
    }
    // Kiểm tra nếu giá trị nonce không trùng khớp
    if (!wp_verify_nonce($field_nonce, 'save_field')) {
        return;
    }
    
    $day_table=json_encode($_POST['_timeSheet']);   
    
    $quote_post_meta['_event_start'] = $_POST['_event_start'];
    $quote_post_meta['_event_end'] = $_POST['_event_end'];
    $quote_post_meta['_event_map'] = $_POST['_event_map'];
    $quote_post_meta['_event_map_note'] = $_POST['_event_map_note'];
    $quote_post_meta['_day_table'] = $day_table;
    // Lưu dữ liệu nếu thoả điều kiện
    foreach ($quote_post_meta as $key => $value) {
        $value = implode(',', (array)$value);
        if (get_post_meta($post_id, $key, FALSE)) {
            update_post_meta($post_id, $key, $value);
        } else {
            add_post_meta($post_id, $key, $value);
        }
        if (!$value) {
            delete_post_meta($post_id, $key);
        }
    }
}

add_action('save_post', 'event_field_save');

function load_custom_wp_admin_style()
{
    global $store_api;
    wp_register_style('custom_wp_admin_css', $store_api->plugin_url() . '/assets/css/admin-style.css', '1.0.0', true);
    wp_enqueue_style('custom_wp_admin_css');
}

add_action('admin_enqueue_scripts', 'load_custom_wp_admin_style');