<?php

use Carbon\Carbon;

// Helper, Setting, Log;

use App\User, App\PageCounter, App\Settings;

use App\SubscriptionPayment;

/**
 * @method tr()
 *
 * Description: used to convert the string to language based string
 *
 * @created Vidhya R
 *
 * @updated
 *
 * @param string $key
 *
 * @return string value
 */
function tr($key , $additional_key = "" , $lang_path = "messages.") {

    // if(Auth::guard('admin')->check()) {

    //     $locale = config('app.locale');

    // } else {

        if (!\Session::has('locale')) {

            $locale = \Session::put('locale', config('app.locale'));

        }else {

            $locale = \Session::get('locale');

        }
    // }
    return \Lang::choice('messages.'.$key, 0, Array('other_key' => $additional_key), $locale);

}

function api_success($key , $other_key = "" , $lang_path = "messages.") {

    if (!\Session::has('locale')) {

        $locale = \Session::put('locale', config('app.locale'));

    } else {

        $locale = \Session::get('locale');

    }
    return \Lang::choice('api-success.'.$key, 0, Array('other_key' => $other_key), $locale);

}

function api_error($key , $other_key = "" , $lang_path = "messages.") {

    if (!\Session::has('locale')) {

        $locale = \Session::put('locale', config('app.locale'));

    } else {

        $locale = \Session::get('locale');

    }
    return \Lang::choice('api-error.'.$key, 0, Array('other_key' => $other_key), $locale);

}

/**
 * @method envfile()
 *
 * Description: get the configuration value from .env file 
 *
 * @created Vidhya R
 *
 * @updated
 *
 * @param string $key
 *
 * @return string value
 */

function envfile($key) {

    $data = getEnvValues();

    if($data) {
        return $data[$key];
    }

    return "";

}

function getEnvValues() {

    $data =  [];

    $path = base_path('.env');

    if(file_exists($path)) {

        $values = file_get_contents($path);

        $values = explode("\n", $values);

        foreach ($values as $key => $value) {

            $var = explode('=',$value);

            if(count($var) == 2 ) {
                if($var[0] != "")
                    $data[$var[0]] = $var[1] ? $var[1] : null;
            } else if(count($var) > 2 ) {
                $keyvalue = "";
                foreach ($var as $i => $imp) {
                    if ($i != 0) {
                        $keyvalue = ($keyvalue) ? $keyvalue.'='.$imp : $imp;
                    }
                }
                $data[$var[0]] = $var[1] ? $keyvalue : null;
            }else {
                if($var[0] != "")
                    $data[$var[0]] = null;
            }
        }

        array_filter($data);
    
    }

    return $data;

}

/**
 * @method register_mobile()
 *
 * Description: Update the user register device details 
 *
 * @created Vidhya R
 *
 * @updated
 *
 * @param string $device_type
 *
 * @return - 
 */

function register_mobile($device_type) {

    // if($reg = MobileRegister::where('type' , $device_type)->first()) {

    //     $reg->count = $reg->count + 1;

    //     $reg->save();
    // }
    
}

/**
 * Function Name : subtract_count()
 *
 * Description: While Delete user, subtract the count from mobile register table based on the device type
 *
 * @created vithya R
 *
 * @updated vithya R
 *
 * @param string $device_ype : Device Type (Andriod,web or IOS)
 * 
 * @return boolean
 */

function subtract_count($device_type) {

    if($reg = MobileRegister::where('type' , $device_type)->first()) {

        $reg->count = $reg->count - 1;
        
        $reg->save();
    }

}

/**
 * @method get_register_count()
 *
 * Description: Get no of register counts based on the devices (web, android and iOS)
 *
 * @created Vidhya R
 *
 * @updated
 *
 * @param - 
 *
 * @return array value
 */

function get_register_count() {

    $ios_count = MobileRegister::where('type' , 'ios')->get()->count();

    $android_count = MobileRegister::where('type' , 'android')->get()->count();

    $web_count = MobileRegister::where('type' , 'web')->get()->count();

    $total = $ios_count + $android_count + $web_count;

    return array('total' => $total , 'ios' => $ios_count , 'android' => $android_count , 'web' => $web_count);

}

/**
 * @method: last_x_days_page_view()
 *
 * @uses: to get last x days page visitors analytics
 *
 * @created Anjana H
 *
 * @updated Anjana H
 *
 * @param - 
 *
 * @return array value
 */
function last_x_days_page_view($days){

    $views = PageCounter::orderBy('created_at','asc')->where('created_at', '>', Carbon::now()->subDays($days))->where('page','home');
 
    $arr = array();
 
    $arr['count'] = $views->count();

    $arr['get'] = $views->get();

      return $arr;
}

function counter($page){

    $count_home = PageCounter::wherePage($page)->where('created_at', '>=', new DateTime('today'));

        if($count_home->count() > 0) {
            $update_count = $count_home->first();
            $update_count->unique_id = uniqid();
            $update_count->count = $update_count->count + 1;
            $update_count->save();
        } else {
            $create_count = new PageCounter;
            $create_count->page = $page;
            $create_count->unique_id = uniqid();
            $create_count->count = 1;
            $create_count->save();
        }

}

//this function convert string to UTC time zone

function convertTimeToUTCzone($str, $userTimezone, $format = 'Y-m-d H:i:s') {

    $new_str = new DateTime($str, new DateTimeZone($userTimezone));

    $new_str->setTimeZone(new DateTimeZone('UTC'));

    return $new_str->format( $format);
}

//this function converts string from UTC time zone to current user timezone

function convertTimeToUSERzone($str, $userTimezone, $format = 'Y-m-d H:i:s') {

    if(empty($str)){
        return '';
    }
    
    try {
        
        $new_str = new DateTime($str, new DateTimeZone('UTC') );
        
        $new_str->setTimeZone(new DateTimeZone( $userTimezone ));
    }
    catch(\Exception $e) {
        // Do Nothing
    }
    
    return $new_str->format( $format);
}

function number_format_short( $n, $precision = 1 ) {

    if ($n < 900) {
        // 0 - 900
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        // 0.9k-850k
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        // 0.9m-850m
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        // 0.9b-850b
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        // 0.9t+
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }
  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
  // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if ( $precision > 0 ) {
        $dotzero = '.' . str_repeat( '0', $precision );
        $n_format = str_replace( $dotzero, '', $n_format );
    }
    return $n_format . $suffix;

}

function common_date($date , $timezone , $format = "d M Y h:i A") {
    
    if($timezone) {

        $date = convertTimeToUSERzone($date , $timezone , $format);

    }   
   
    return date($format , strtotime($date));
}


/**
 * function delete_value_prefix()
 * 
 * @uses used for concat string, while deleting the records from the table
 *
 * @created vidhya R
 *
 * @updated vidhya R
 *
 * @param $prefix - from settings table (Setting::get('prefix_user_delete'))
 *
 * @param $primary_id - Primary ID of the delete record
 *
 * @param $is_email 
 *
 * @return concat string based on the input values
 */

function delete_value_prefix($prefix , $primary_id , $is_email = 0) {

    if($is_email) {

        $site_name = str_replace(' ', '_', Setting::get('site_name'));

        return $prefix.$primary_id."@".$site_name.".com";
        
    } else {
        return $prefix.$primary_id;

    }

}

/**
 * function routefreestring()
 * 
 * @uses used for remove the route parameters from the string
 *
 * @created vidhya R
 *
 * @updated vidhya R
 *
 * @param string $string
 *
 * @return Route parameters free string
 */

function routefreestring($string) {

    $string = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $string));
    
    $search = [' ', '&', '%', "?",'=','{','}','$'];

    $replace = ['-', '-', '-' , '-', '-', '-' , '-','-'];

    $string = str_replace($search, $replace, $string);

    return $string;
    
}

/**
 * @method selected()
 *
 * @uses set selected item 
 *
 * @created Anjana H
 *
 * @updated Anjana H
 *
 * @param $array, $id, $check_key_name
 *
 * @return response of array 
 */
function selected($array, $id, $check_key_name) {
    
    $is_key_array = is_array($id);
    
    foreach ($array as $key => $array_details) {

        $array_details->is_selected = ($array_details->$check_key_name == $id) ? YES : NO;
    }  

    return $array;
}


function nFormatter($num, $currency = "") {

    $currency = Setting::get('currency', "$");

    if($num>1000) {

        $x = round($num);

        $x_number_format = number_format($x);

        $x_array = explode(',', $x_number_format);

        $x_parts = ['k', 'm', 'b', 't'];

        $x_count_parts = count($x_array) - 1;

        $x_display = $x;

        $x_display = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');

        $x_display .= $x_parts[$x_count_parts - 1];

        return $currency." ".$x_display;

    }

    return $currency." ".$num;

}

/**
 * @method formatted_plan()
 *
 * @uses used to format the number
 *
 * @created Bhawya
 *
 * @updated Akshata
 *
 * @param integer $num
 * 
 * @param string $currency
 *
 * @return string $formatted_plan
 */

function formatted_plan($plan = 0, $type = "month") {

    switch ($type) {

        case 'weeks':

            $text = $plan <= 1 ? tr('week') : tr('weeks');

            break;

        case 'days':

            $text = $plan <= 1 ? tr('day') : tr('days');

            break;

        case 'years':

            $text = $plan <= 1 ? tr('year') : tr('years');

            break;
        
        default:
        
            $text = $plan <= 1 ? tr('month') : tr('months');
            
            break;
    }
    
    return $plan." ".$text;
}

/**
 * @method formatted_amount()
 *
 * @uses used to format the number
 *
 * @created vidhya R
 *
 * @updated vidhya R
 *
 * @param integer $num
 * 
 * @param string $currency
 *
 * @return string $formatted_amount
 */

function formatted_amount($amount = 0.00, $currency = "") {
   
    $currency = $currency ?: Setting::get('currency', '$');

    $amount = number_format((float)$amount, 2, '.', '');

    $formatted_amount = $currency."".$amount ?: "0.00";

    return $formatted_amount;
}

function readFileLength($file)  {

    $variableLength = 0;
    if (($handle = fopen($file, "r")) !== FALSE) {
         $row = 1;
         while (($data = fgetcsv($handle, 1000, "\n")) !== FALSE) {
            $num = count($data);
            $row++;
            for ($c=0; $c < $num; $c++) {
                $exp = explode("=>", $data[$c]);
                if (count($exp) == 2) {
                    $variableLength += 1; 
                }
            }
        }
        fclose($handle);
    }

    return $variableLength;
}

function no_of_class_formatted($no_of_class = 1) {

    $no_of_class = $no_of_class <= 1 ? $no_of_class.' '.tr('class') : $no_of_class.' '.tr('class').'s';

    return $no_of_class;

}

function no_of_users_each_class_formatted($no_of_users) {

    $no_of_users = $no_of_users <= 1 ? $no_of_users.' '.tr('no_of_user') : $no_of_users.' '.tr('no_of_user').'s';

    return $no_of_users."/per class";

}

function getExpiryDate($plan,$plan_type) {
   
    $expiry_date = date('Y-m-d h:i:s',strtotime("+{$plan}{$plan_type}"));

    return $expiry_date;
}

function last_6_months_data() {

    $months = 6;

    $data = new \stdClass;

    $data->currency = $currency = Setting::get('currency', '$');

    $last_x_days_revenues = [];

    $start  = new \DateTime('-6 month', new \DateTimeZone('UTC'));
    
    $period = new \DatePeriod($start, new \DateInterval('P1M'), $months);
    
    $dates = $last_x_days_revenues = [];

    foreach ($period as $date) {

        $current_month = $date->format('M');

        $last_x_days_data = new \stdClass;

        $last_x_days_data->month= $current_month;

        $month = $date->format('m');
      
        $last_x_days_total_earnings = SubscriptionPayment::whereMonth('paid_date', '=', $month)->where('status' , PAID)->sum('amount');
        
        $last_x_days_data->total_earnings = $last_x_days_total_earnings ?: 0.00;

        array_push($last_x_days_revenues, $last_x_days_data);

    }
    
    $data->last_x_days_revenues = $last_x_days_revenues;
    
    return $data;  
}

function static_page_footers($section_type = 0, $is_list = NO) {

    $lists = [
                STATIC_PAGE_SECTION_1 => tr('STATIC_PAGE_SECTION_1')."(".Setting::get('site_name').")",
                STATIC_PAGE_SECTION_2 => tr('STATIC_PAGE_SECTION_2')."(
                Discover)",
                STATIC_PAGE_SECTION_3 => tr('STATIC_PAGE_SECTION_3')."(Hosting)",
                STATIC_PAGE_SECTION_4 => tr('STATIC_PAGE_SECTION_4')."(Social)",
            ];

    if($is_list == YES) {
        return $lists;
    }

    return isset($lists[$section_type]) ? $lists[$section_type] : "Common";

}


function last_x_months_data($months) {

    $data = new \stdClass;

    $data->currency = $currency = Setting::get('currency', '$');

    $last_x_days_revenues = [];

    $start  = new \DateTime('-6 month', new \DateTimeZone('UTC'));
    
    $period = new \DatePeriod($start, new \DateInterval('P1M'), $months);
    
    $dates = $last_x_days_revenues = [];

    foreach ($period as $date) {

        $current_month = $date->format('M');

        $formatted_month = $date->format('Y-m');

        $last_x_days_data = new \stdClass;

        $last_x_days_data->month= $current_month;

        $last_x_days_data->formatted_month = $formatted_month;

        $month = $date->format('m');
      
        $last_x_days_subscription_earnings = \App\SubscriptionPayment::whereMonth('paid_date', '=', $month)->where('status' , PAID)->sum('amount');

        $last_x_days_order_earnings = \App\OrderPayment::whereMonth('paid_date', '=', $month)->where('status' , PAID)->sum('total');

        $last_x_days_post_earnings = \App\PostPayment::whereMonth('paid_date', '=', $month)->where('status' , PAID)->sum('paid_amount');
        
        $last_x_days_data->subscription_earnings = $last_x_days_subscription_earnings ?: 0.00;

        $last_x_days_data->order_earnings = $last_x_days_order_earnings ?: 0.00;

        $last_x_days_data->post_earnings = $last_x_days_post_earnings ?: 0.00;

        array_push($last_x_days_revenues, $last_x_days_data);

    }
    
    $data->last_x_days_revenues = $last_x_days_revenues;
    
    return $data;  
}

function last_x_days_revenue($days,$order_products_ids) {
            
    $data = new \stdClass;

    $data->currency = $currency = Setting::get('currency', '$');

    // Last 10 days revenues

    $last_x_days_revenues = [];

    $start  = new \DateTime('-7 day', new \DateTimeZone('UTC'));
    
    $period = new \DatePeriod($start, new \DateInterval('P1D'), $days);
   
    $dates = $last_x_days_revenues = [];

    foreach ($period as $date) {

        $current_date = $date->format('Y-m-d');

        $last_x_days_data = new \stdClass;

        $last_x_days_data->date = $current_date;
      
        $last_x_days_total_earnings = \App\OrderPayment::whereIn('order_id',[$order_products_ids])->whereDate('paid_date', '=', $current_date)->sum('total');
      
        $last_x_days_data->total_earnings = $last_x_days_total_earnings ?: 0.00;

        array_push($last_x_days_revenues, $last_x_days_data);

    }
    
    $data->last_x_days_revenues = $last_x_days_revenues;
    
    return $data;   

}