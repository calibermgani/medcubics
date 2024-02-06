<?php

namespace App\Http\Helpers;

use App;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Medcubics\ApiConfig as ApiConfig;
use App\Models\Medcubics\Users as Users;
use App\Models\Profile\ProfileEvents as ProfileEvents;
use App\Models\Profile\PrivateMessageDetails;
use App\Models\Profile\Blog as Blog;
use App\Models\Patients\Patient;
use App\Models\Profile\UserLoginHistory as UserLoginHistory;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Http\Helpers\EncryptIdAlgorithm as EncryptIdAlgorithm;
use App\Models\Medcubics\Setpracticeforusers as Setpracticeforusers;
use App\Models\Medcubics\Practice as Practice;
use App\Models\Medcubics\Icd as Icd;
use App\Models\Employer as Employer;
use App\Models\Medcubics\Cpt as Cpt;
use App\Models\Medcubics\Modifier as Modifier;
use App\Models\Document;
use App\Models\Insurance;
use App\Models\Insurancetype;
use App\Models\Provider;
use App\Models\Template;
use App\Models\Patients\PatientInsurance;
use App\Models\Patients\PatientBudget;
use App\Http\Controllers\Medcubics\Api\UpdatesApiController;
// New payment flow changes
use App\Models\Payments\ClaimInfoV1;
use App\Models\Payments\PMTClaimCPTFINV1;
use App\Models\Payments\PMTClaimFINV1;
use App\Models\Payments\PMTInfoV1;
use App\Models\Payments\PMTClaimTXV1;
use App\Models\Cpt as Cpts;
use App\Models\Holdoption as Holdoption;

use App\Models\Facility;
use App\Models\Patients\PatientNote as PatientNote;
use App\Models\SearchFields as SearchFields;
use App\Models\SearchUserData as SearchUserData;
use App\Models\ReportExport as ReportExport;
use App\Models\Medcubics\Customer as Customer;
use App\Models\ProcedureCategory as ProcedureCategory;
use App\Models\Wishlist as Wishlist;
use App\User as User;

use Route;
use Config;
use Response;
use Image;
use Session;
use Redirect;
use Request;
use DB;
use View;
use Validator;
use DateTime;
use DateTimeZone;
use Input;
use Hash;
use Cache;
use Log;
use Carbon\Carbon;

use Google\Cloud\Storage\StorageClient;
use League\Flysystem\Filesystem;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;
use App\Http\Controllers\Claims\ClaimControllerV1;
use Jenssegers\Agent\Agent;


if (!defined("DS"))
    DEFINE('DS', DIRECTORY_SEPARATOR);

class Helpers {

    public static function dateFormat($date, $type = 'date') {
        try{
            $date = trim($date); // date string space trimmed.
            if(is_object($date)){
                //Log::info("Date passed as object handle this "); Log::info($date);
                //return '';
            }

            if ($date != '' && $date != '-0001-11-30 00:00:00' && $date != '0000-00-00' && $date != '1901-01-01' && $date != '01/01/1970') {//dd('if');
                if ($type == 'date'){
                    return date('m/d/y', strtotime($date));
                } elseif ($type == 'dob') {
                    $date = DateTime::createFromFormat('Y-m-d', $date);
                    if ($date == false)
                        return date('m/d/Y', strtotime($date));
                    return $date->format('m/d/Y');
                } elseif ($type == 'claimdate') {
                    return date('m/d/Y', strtotime($date));
                } elseif ($type == 'time')
                    return date('m/d/y h:i A', strtotime($date));
                elseif ($type == 'timestamp')
                    return date('h:i A', strtotime($date));
                elseif ($type == 'datetime')
                    return date('m/d/y H:i A', strtotime($date));
                elseif ($type == 'datedb')
                    return date('Y-m-d', strtotime($date));
            }else {
                if($type == 'date' || $type == 'claimdate')
                    return '';
                return '-Nil-';
            }
        } catch(Exception $e){
            Log::info("Error occured on dateFormat ".$e->getMessage());
            return '';
        }
    }

    public static function checkAndGetAvatar($img_details) {
        $module_name = $img_details['module_name'];
        $file_name = $img_details['file_name'];
        $practice_name = $img_details['practice_name'];
        $patient_id = (isset($img_details['patient_id'])) ? $img_details['patient_id'] : '';
        unset($img_details['module_name']);
        unset($img_details['file_name']);
        unset($img_details['practice_name']);
        $main_dir_name = $practice_name;
        if ($main_dir_name == null && Session::get('practice_dbid') != '') {
            $main_dir_name = md5('P' . Session::get('practice_dbid'));
        }
        $chk_env_site = getenv('APP_ENV');
        $default_view = Config::get('siteconfigs.production.defult_production');
        if ($chk_env_site == $default_view) {
            $storage_disk = "s3_production";
            $bucket_name = "medcubicsproduction";
        } else {
            $storage_disk = "s3";
            $bucket_name = "medcubicslocal";
        }/*
          if(!$sock = @fsockopen('www.aws.amazon.com', 80))
          {
          echo 'Not Connected';
          }
          else
          {
          echo 'Connected';
          }
          die(); */
        $avatar_url = 'img/noimage.png';
         $connected = fsockopen("www.aws.amazon.com", 80); //For handle aws issue on network connection interupted.
        //dd($connected);
        if (is_resource($connected) && $file_name != '' && @Storage::disk($storage_disk)->exists(@$main_dir_name . "/image/" . $module_name . "/" . $file_name) && $file_name != '.') {
            $avatar_url = @Storage::disk($storage_disk)->getDriver()->getAdapter()->getClient()->getObjectUrl($bucket_name, $main_dir_name . "/image/" . $module_name . "/" . $file_name);
        } else {//dd('sdd123');
            if ($module_name == 'patient')
                $avatar_url = 'img/patient_noimage.png';
            elseif ($module_name == 'practice')
                $avatar_url = 'img/practice-avatar.jpg';
            elseif ($module_name == 'facility')
                $avatar_url = 'img/facility-avator.jpg';
            elseif ($module_name == 'insurance')
                $avatar_url = 'img/insurance-avator.jpg';
            else
                $avatar_url = 'img/noimage.png';
        }

        if (isset($img_details['need_url']))
            return $avatar_url;
        /*         * * Getting Image tag and image path encryption starts here  ** */

        $ext = pathinfo(@$avatar_url, PATHINFO_EXTENSION);
        //echo $avatar_url; exit;
        $contextOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false
            )
        );
        set_time_limit(0);
        $image = file_get_contents(@$avatar_url, false, stream_context_create($contextOptions));
        $enc_path = 'data:image/' . $ext . ';base64,' . base64_encode($image); //type need [base64], no other encrypt method
        $img_details['src'] = $enc_path;
        $get_all_attr = '';
        foreach ($img_details as $attr_key => $attr_val) {
            $get_all_attr = $get_all_attr . " " . $attr_key . '="' . $attr_val . '"';
        }
        //if($id =="style") return '<img src="'.$enc_path.'" alt="'.$alt_text.'" style="height:20px;width:20px;" />';
        return'<img ' . $get_all_attr . '/>';
        //*** Getting Image tag and image path encryption ends here  ***/
    }

    public static function checkAndGetAvatarold($img_details) {
        $module_name = $img_details['module_name'];
        $file_name = $img_details['file_name'];
        $practice_name = $img_details['practice_name'];
        $patient_id = (isset($img_details['patient_id'])) ? $img_details['patient_id'] : '';
        unset($img_details['module_name']);
        unset($img_details['file_name']);
        unset($img_details['practice_name']);
        $main_dir_name = $practice_name;
        if ($main_dir_name == null && Session::get('practice_dbid') != '') {
            $main_dir_name = md5('P' . Session::get('practice_dbid'));
        }
        $chk_env_site = getenv('APP_ENV');
        $default_view = Config::get('siteconfigs.production.defult_production');
        if ($chk_env_site == $default_view) {
            $storage_disk = "s3_production";
            $bucket_name = "medcubicsproduction";
        } else {
            $storage_disk = "gcs";
            $bucket_name = "medcubicslocal";
        }

        $avatar_url = 'img/noimage.png';
        //$connected = fsockopen("www.aws.amazon.com", 80); //For handle aws issue on network connection interupted.
        $disk = Storage::disk($storage_disk);

        // check if a file exists
        if ($disk->exists($main_dir_name . "/image/" . $module_name . "/" . $file_name)) {
            // get url to file
            $avatar_url = $disk->url($main_dir_name . "/image/" . $module_name . "/" . $file_name);
        } else {

            if ($module_name == 'patient')
                $avatar_url = 'img/patient_noimage.png';
            elseif ($module_name == 'practice')
                $avatar_url = 'img/practice-avatar.jpg';
            elseif ($module_name == 'facility')
                $avatar_url = 'img/facility-avator.jpg';
            elseif ($module_name == 'insurance')
                $avatar_url = 'img/insurance-avator.jpg';
            else
                $avatar_url = 'img/noimage.png';
        }

        if (isset($img_details['need_url']))
            return $avatar_url;
        /*         * * Getting Image tag and image path encryption starts here  ** */

        $ext = pathinfo(@$avatar_url, PATHINFO_EXTENSION);
        $contextOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false
            )
        );
        set_time_limit(0);
        $image = file_get_contents(@$avatar_url, false, stream_context_create($contextOptions));
        $enc_path = 'data:image/' . $ext . ';base64,' . base64_encode($image); //type need [base64], no other encrypt method
        $img_details['src'] = $enc_path;
        $get_all_attr = '';
        foreach ($img_details as $attr_key => $attr_val) {
            $get_all_attr = $get_all_attr . " " . $attr_key . '="' . $attr_val . '"';
        }
        return'<img ' . $get_all_attr . '/>';
        //*** Getting Image tag and image path encryption ends here  ***/
    }

    public static function getPracticeBlogImgUrl($module = null, $main_dir_name = null) {
        if ($main_dir_name == '')
            $main_dir_name = md5('P' . Session::get('practice_dbid'));

        $chk_env_site = getenv('APP_ENV');
        $default_view = Config::get('siteconfigs.production.defult_production');
        if ($chk_env_site == $default_view) {
            $storage_disk = "s3_production";
            $bucket_name = "medcubicsproduction";
        } else {
            $storage_disk = "s3";
            $bucket_name = "medcubicslocal";
        }
        $response = Storage::disk($storage_disk)->getDriver()->getAdapter()->getClient()->getObjectUrl($bucket_name, $main_dir_name . "/image/" . $module);
        return $response;
    }

    public static function sliderTimeDisplay($time_value, $noon) {
        $time_value_arr = explode(';', $time_value);
        $from = floor($time_value_arr[0] / 60) . ":" . $time_value_arr[0] % 60;
        $to = floor($time_value_arr[1] / 60) . ":" . $time_value_arr[1] % 60;
        if (strlen($from) < 5) {
            $split_from = explode(':', $from);
            if (strlen($split_from[0]) < 2)
                $split_from1 = '0' . $split_from[0];
            else
                $split_from1 = $split_from[0];

            if (strlen($split_from[1]) < 2)
                $split_from2 = '0' . $split_from[1];
            else
                $split_from2 = $split_from[1];
            $from = $split_from1 . ':' . $split_from2;
        }

        if (strlen($to) < 5) {
            $split_to = explode(':', $to);
            if (strlen($split_to[0]) < 2)
                $split_to1 = '0' . $split_to[0];
            else
                $split_to1 = $split_to[0];

            if (strlen($split_to[1]) < 2)
                $split_to2 = '0' . $split_to[1];
            else
                $split_to2 = $split_to[1];
            $to = $split_to1 . ':' . $split_to2;
        }

        if ($noon == 'forenoon') {
            if ($from < 12)
                $from = $from . ' AM';
            else
                $from = $from . ' PM';

            if ($to < 12)
                $to = $to . ' AM';
            else
                $to = $to . ' PM';
        } else {
            $from = $from . ' PM';
            $split_hour = explode(':', $from);

            if ($split_hour[0] >= 13 && $split_hour[0] <= 24) {
                $split_hour_new = $split_hour[0] - 12;
                $from = $split_hour_new . ':' . $split_hour[1];
            }
            $to = $to . ' PM';
            $split_sehour = explode(':', $to);

            if ($split_sehour[0] >= 13 && $split_sehour[0] <= 24) {
                $split_sehour_new = $split_sehour[0] - 12;
                if (strlen($split_sehour_new) <= 1)
                    $split_sehour_new = '0' . $split_sehour_new;
                $to = $split_sehour_new . ':' . $split_sehour[1];
            }
        }
        /*
         * from time & to time length equalt 7 or less than 7 before add zero in hours
         */
        if (strlen($from) <= 7)
            $from = '0' . $from;
        elseif (strlen($to) <= 7)
            $to = '0' . $to;
        return $from . ' - ' . $to;
    }

    public static function addOrdinalNumberSuffix($num) {
        if (!in_array(($num % 100), array(11, 12, 13))) {
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1: return $num . 'st';
                case 2: return $num . 'nd';
                case 3: return $num . 'rd';
            }
        }
        return $num . 'th';
    }

    public static function getDateswithSuffix() {
        for ($i = 1; $i <= 31; $i++) {
            $date_list[$i] = Helpers::addOrdinalNumberSuffix($i);
        }
        return $date_list;
    }

    public static function getDropDownlistTimings($time_slot, $noon = '') {
        $time_value_arr = explode(';', $time_slot);
        $from = floor($time_value_arr[0] / 60) . ":" . $time_value_arr[0] % 60;
        $to = floor($time_value_arr[1] / 60) . ":" . $time_value_arr[1] % 60;
        $start = strtotime($from);
        $end = strtotime($to);

        $options = [];
        for ($i = $start; $i <= $end; $i += 900) {
            // $options .= "<option value='".date('h:i a', $i)."'>".date('h:i a', $i)."</option>";
            $options[date('h:i a', $i)] = date('h:i a', $i);
        }
        if ($to == '23:59' && $time_value_arr[1] == '1439')
            $options["11:59 pm"] = "11:59 pm";
        //dd($time_slot);
        return $options;
    }

    public static function getAvailableTimings($forenoon, $afternoon) {
        if ($forenoon != '0;0')
            $forenoon_avilable = Helpers::sliderTimeDisplay($forenoon, 'forenoon');
        else
            $forenoon_avilable = '';

        if ($afternoon != '720;720')
            $afternoon_avilable = Helpers::sliderTimeDisplay($afternoon, 'afternoon');
        else
            $afternoon_avilable = '';

        if ($forenoon_avilable != '' && $afternoon_avilable != '')
            $available_time = '(' . $forenoon_avilable . ' / ' . $afternoon_avilable . ')';
        elseif ($forenoon_avilable != '')
            $available_time = '(' . $forenoon_avilable . ')';
        elseif ($afternoon_avilable != '')
            $available_time = '(' . $afternoon_avilable . ')';
        else
            $available_time = 'Not available';

        $available_time = str_replace('00:00 AM', '12:00 AM', $available_time);
        return $available_time;
    }

    public static function getFacilityWorkingTimingsList($facility) {
        $monday = [];
        if ($facility->monday_forenoon != '0;0')
            $monday = Helpers::getDropDownlistTimings($facility->monday_forenoon);
        if ($facility->monday_afternoon != '720;720')
            $monday = $monday + Helpers::getDropDownlistTimings($facility->monday_afternoon);

        $tuesday = [];
        if ($facility->tuesday_forenoon != '0;0')
            $tuesday = Helpers::getDropDownlistTimings($facility->tuesday_forenoon);
        if ($facility->tuesday_afternoon != '720;720')
            $tuesday = $tuesday + Helpers::getDropDownlistTimings($facility->tuesday_afternoon);

        $wednesday = [];
        if ($facility->wednesday_forenoon != '0;0')
            $wednesday = Helpers::getDropDownlistTimings($facility->wednesday_forenoon);
        if ($facility->wednesday_afternoon != '720;720')
            $wednesday = $wednesday + Helpers::getDropDownlistTimings($facility->wednesday_afternoon);

        $thursday = [];
        if ($facility->thursday_forenoon != '0;0')
            $thursday = Helpers::getDropDownlistTimings($facility->thursday_forenoon);
        if ($facility->thursday_afternoon != '720;720')
            $thursday = $thursday + Helpers::getDropDownlistTimings($facility->thursday_afternoon);

        $friday = [];
        if ($facility->friday_forenoon != '0;0')
            $friday = Helpers::getDropDownlistTimings($facility->friday_forenoon);
        if ($facility->friday_afternoon != '720;720')
            $friday = $friday + Helpers::getDropDownlistTimings($facility->friday_afternoon);

        $saturday = [];
        if ($facility->saturday_forenoon != '0;0')
            $saturday = Helpers::getDropDownlistTimings($facility->saturday_forenoon);
        if ($facility->saturday_afternoon != '720;720')
            $saturday = $saturday + Helpers::getDropDownlistTimings($facility->saturday_afternoon);

        $sunday = [];
        if ($facility->sunday_forenoon != '0;0')
            $sunday = Helpers::getDropDownlistTimings($facility->sunday_forenoon);
        if ($facility->sunday_afternoon != '720;720')
            $sunday = $sunday + Helpers::getDropDownlistTimings($facility->sunday_afternoon);

        /// Timings array by days
        $facility_timings_details['monday'] = $monday;
        $facility_timings_details['tuesday'] = $tuesday;
        $facility_timings_details['wednesday'] = $wednesday;
        $facility_timings_details['thursday'] = $thursday;
        $facility_timings_details['friday'] = $friday;
        $facility_timings_details['saturday'] = $saturday;
        $facility_timings_details['sunday'] = $sunday;
        return $facility_timings_details;
    }

    public static function getFacilityWorkingTimingsDropDownListOption($days_array) {
        foreach ($days_array['monday'] as $val) {
            $days['monday'][] = '<option value="' . $val . '">' . $val . '</option>';
        }

        foreach ($days_array['tuesday'] as $val) {
            $days['tuesday'][] = '<option value="' . $val . '">' . $val . '</option>';
        }

        foreach ($days_array['wednesday'] as $val) {
            $days['wednesday'][] = '<option value="' . $val . '">' . $val . '</option>';
        }

        foreach ($days_array['thursday'] as $val) {
            $days['thursday'][] = '<option value="' . $val . '">' . $val . '</option>';
        }

        foreach ($days_array['friday'] as $val) {
            $days['friday'][] = '<option value="' . $val . '">' . $val . '</option>';
        }

        foreach ($days_array['saturday'] as $val) {
            $days['saturday'][] = '<option value="' . $val . '">' . $val . '</option>';
        }

        foreach ($days_array['sunday'] as $val) {
            $days['sunday'][] = '<option value="' . $val . '">' . $val . '</option>';
        }
        return $days;
    }

    public static function splitAndGetTimingsByDay($array_list, $from, $to, $facility_availability_timings = '') {
        $start = strtotime($from);
        $end = strtotime($to);
        for ($i = $start; $i <= $end; $i += 900) {
            $time = date('h:i a', $i);
            if ($facility_availability_timings != '') {
                if (in_array($time, $facility_availability_timings))
                    $array_list[$time] = $time;
            } else
                $array_list[$time] = $time;
        }
        return $array_list;
    }

    public static function GetNoOfDatesBetween2Dates($from, $to) {
        $from_date = strtotime($from);
        $to_date = strtotime($to);
        $current = $from_date;
        $dates_array['days'] = $dates_array['dates'] = [];
        while ($current <= $to_date) {
            $date = date("Y-m-d", $current);
            $dates_array['days'][$date] = strtolower(date('l', $current));
            $dates_array['dates'][$date] = $date;
            $current = $current + 86400;
        }
        return $dates_array;
    }

    public static function checkScheduledTimeAvailablity($day_array, $from, $to, $available_timings, $facility_available_timings, $error_msg = '') {
        $day_array['available_count'] = 0;
        $day_array['error_count'] = 0;
        for ($i = 1; $i <= 3; $i++) {
            $day_array['dates'][$i] = [];
            $day_array['errors'][$i] = [];
            $is_available = '';
            $is_error = '';
            if ($from[$i] != '' && $to[$i] != '') {
                $day_array['dates'][$i] = Helpers::splitAndGetTimingsByDay($day_array['dates'][$i], $from[$i], $to[$i], $facility_available_timings);
                $array_count = count($day_array['dates'][$i]);
                $s = 1;
                foreach ($day_array['dates'][$i] as $value) {
                    if (in_array($value, $available_timings)) {
                        $is_available = 'yes';
                        if ($s != 1 || $s != $array_count)
                            unset($available_timings[$value]);
                    } else {
                        $is_error = 'yes';
                        $day_array['errors'][$i][] = $value;
                    }
                    $s++;
                }
                if ($is_available == 'yes')
                    $day_array['available_count'] ++;
                if ($is_error == 'yes')
                    $day_array['error_count'] ++;
            }

            if ($is_error == 'yes') {
                if ($error_msg != '')
                    $error_msg .= ', ';
                $error_msg .= $from[$i] . '-' . $to[$i];
            }
        }
        $day_array['error_msg'] = $error_msg;
        return $day_array;
    }

    public static function checkAvailableDaySelectedOrNot($facility_day_available_count, $day_time_selected_count) {
        if ($facility_day_available_count > 0) {
            if ($day_time_selected_count == 1)
                return 'no_error';
            else
                return 'error';
        }
        else {
            return 'not_available';
        }
    }

    public static function GetNoOfDatesBetween2DatesAndCheckWithFacilityWorkingDays($from, $to, $exclude_days) {
        $from_date = strtotime($from);
        $to_date = strtotime($to);
        $current = $from_date;
        $dates_array['dates'] = '';
        //dd($exclude_days);
        while ($current <= $to_date) {
            $date = date("Y-m-d", $current);
            $day = strtolower(date('l', $current));
            //echo "<pre>"; print_r($day);
            if (in_array($day, $exclude_days)) {
                $dates_array['dates'][] = $date;
                $current = $current + 86400;
            } else {
                $to = date("Y-m-d", strtotime($to . "+ 1 days"));
                $to_date = strtotime($to);
                $current = $current + 86400;
            }
        }
        // echo "<pre>"; print_r($dates_array); exit;
        //dd($dates_array1);
        return $dates_array;
    }

    public static function getDayNameByDate($date, $type = 'date') {
        if ($type == 'date')
            return strtolower(date('l', strtotime($date)));
        else
            return strtolower(date('l', $date));
    }

    public static function arrangeDaysByAscending($days_name_array) {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $values) {
            if (in_array($values, $days_name_array))
                $days_array_ascending[] = ucfirst($values);
        }
        return $days_array_ascending;
    }

    public static function findListOfWeeksAndStartAndEndDates($month, $monthly_visit_type_week) {
        $monthly_visit_type_week = $monthly_visit_type_week - 1;
        $textdt = $month;
        $dt = strtotime($textdt);
        $currdt = $dt;
        $nextmonth = strtotime($textdt . "+1 month");
        $i = 0;
        do {
            $weekday = date("w", $currdt);
            $nextday = 7 - $weekday;
            $endday = abs($weekday - 6);
            $startarr[$i] = $currdt;
            $endarr[$i] = strtotime(date("Y-m-d", $currdt) . "+$endday day");
            $currdt = strtotime(date("Y-m-d", $endarr[$i]) . "+1 day");
            if ($i == $monthly_visit_type_week) {
                $start_date = date("Y-m-d", $startarr[$i]);
                $end_date = date("Y-m-d", $endarr[$i]);
                $dates_array_all = Helpers::GetNoOfDatesBetween2Dates($start_date, $end_date);
                break;
            }
            $i++;
        } while ($endarr[$i - 1] < $nextmonth);
        return $dates_array_all;
    }

    public static function checkAndsetVisitTimingsByDay($timings) {
        $split_time = explode(',', $timings);
        $time_display = '';
        foreach ($split_time as $time) {
            if ($time != '') {
                if ($time_display != '')
                    $time_display .= ', ';
                $time_display .= str_replace('-', ' - ', $time);
            }
        }
        if ($time_display == '')
            $time_display = 'Not Available';
        return $time_display;
    }

    public static function getDaysTimeListByDay($selected_timings, $days_time) {
        $days_time = array_values($days_time);
        $i = 1;
        $split_time = explode(',', $selected_timings);
        $day[$i]['from']['options'] = $days_time;
        $day_details['from_option' . $i] = array_combine($days_time, $days_time);
        foreach ($split_time as $val) {
            if ($val != '') {
                $split_timings = explode('-', $val);
                $from = $split_timings[0];
                $to = $split_timings[1];

                if ($i != 1) {
                    $to_value = $day[$i - 1]['selected_to_time'];
                    $to_array = $day[$i - 1]['to']['options'];
                    $from_array_key = array_search($to_value, $to_array);
                    $day[$i]['from']['options'] = array_slice($to_array, $from_array_key);
                }

                // To option listing
                $from_option = $from;
                $from_array_key = array_search($from_option, $day[$i]['from']['options']);
                $day[$i]['to']['options'] = array_slice($day[$i]['from']['options'], $from_array_key + 1);
                $to_array_key = array_search($to, $day[$i]['to']['options']);

                $day_details['from_option' . $i] = array_combine($day[$i]['from']['options'], $day[$i]['from']['options']);
                $day_details['to_option' . $i] = array_combine($day[$i]['to']['options'], $day[$i]['to']['options']);
                $day[$i]['selected_from_time'] = $day_details['selected_from_time' . $i] = $from;
                $day[$i]['selected_to_time'] = $day_details['selected_to_time' . $i] = $to;
            } else {
                $day[$i] = '';

                if ($i != 1)
                    $day_details['from_option' . $i] = [];

                $day_details['to_option' . $i] = [];
                $day_details['selected_from_time' . $i] = '';
                $day_details['selected_to_time' . $i] = '';
            }
            $i++;
        }
        //dd($day_details);
        return $day_details;
    }

    /*
      public static function splitAndGetTimingsByDay($day)
      {
      $time = '';
      $day_time = explode(',',$day);
      for($s=0;$s<=2;$s++)
      {
      if($day_time[$s] != '')
      {
      $split_time = explode('-',$day_time[$s]);
      if(count($split_time)>=1)
      {
      $start = strtotime($split_time[0]);
      $end   = strtotime($split_time[1]);

      for($i = $start;$i<=$end;$i+=900)
      {
      $time[date('h:i a', $i)] = date('h:i a', $i);
      }
      }
      }
      }
      return $time;
      }
     */

    /*     * *************** Start Manual Error log Method *********************************************** */

    public static function manual_errorlog($e, $htt_exp) {
        $ret_err_code = '';
        try {
            $err_file = $e->getFile();
            $err_line = $e->getLine();

            $auth_id = (isset(Auth::user()->id)) ? Auth::user()->id : "x_" . mt_rand(5, 15);
            $auth_name = (isset(Auth::user()->name)) ? Auth::user()->name : 'Guest';
            if (!Storage::disk('local_manual_log')->exists('manual_logs')) {
                Storage::disk('local_manual_log')->makeDirectory('manual_logs');
            }

            if (!Storage::disk('local_manual_log')->exists('manual_logs/' . date('d-m-Y'))) {
                Storage::disk('local_manual_log')->makeDirectory('manual_logs/' . date('d-m-Y'));
            }

            $fileContent = "";
            if (!Storage::disk('local_manual_log')->exists('manual_logs/' . date('d-m-Y') . '/' . $auth_id . '.txt')) {
                Storage::disk('local_manual_log')->put('manual_logs/' . date('d-m-Y') . '/' . $auth_id . '.txt', '');
            } else {
                $fileContent = Storage::disk('local_manual_log')->get('manual_logs/' . date('d-m-Y') . '/' . $auth_id . '.txt');
            }

            if ($htt_exp) {
                $err_code = $e->getStatusCode();
                if ($err_code == 302 || $err_code == 404 || $err_code == 500) {
                    $err_msg = $ret_err_code = $err_code;
                } else {
                    $err_msg = $e->getMessage();
                    $ret_err_code = 0;
                }
            } else {
                $err_msg = $e->getMessage();
                $ret_err_code = 0;
            }

            $string = " ==================================================== \n";
            $string .= " Time : " . date('H:i:s') . "\n";
            $string .= " User Id and Name : " . $auth_id . " " . $auth_name . "\n";
            $string .= " URL Path : " . $_SERVER['REQUEST_URI'] . "\n";
            $string .= " Error Message : $err_msg \n";
            $string .= " File path : $err_file \n";
            $string .= " Line Number : $err_line \n";

            $to = "anitha@clouddesigners.com";
            $subject = "Medcubics Issue - " . date('d-m-Y H:i:s');
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: Admin' . "\r\n";
            $headers .= 'Cc: developers@clouddesigners.com,akash@clouddesigners.com' . "\r\n";

            $to = "admin@medcubics.com";
            $cc_emails = '';
            //$cc_emails = 'ravikumar@clouddesigners.com,akash@clouddesigners.com,selvakumar@clouddesigners.com';

            $data = array('email' => $to, 'name'=> 'Developers', 'cc_email' => $cc_emails, 'subject' => $subject, 'msg' => $string);
            SELF::sendMail($data);
            //   mail($to, $subject, $string, $headers);

            Storage::disk('local_manual_log')->put('manual_logs/' . date('d-m-Y') . '/' . $auth_id . '.txt', $string . "\n" . $fileContent);
        } catch (Exception $e) {
            die("Errors " . $e->getMessage());
        }
        return $ret_err_code;
    }

    /*     * *************** End Manual Error log Method *********************************************** */

     public static function manual_errorlog_charges($e, $htt_exp) {
        $ret_err_code = '';
        try {
            $err_file = $e->getFile();
            $err_line = $e->getLine();

            $auth_id = (Auth::user()->id) ? Auth::user()->id : "x_" . mt_rand(5, 15);
            $auth_name = (Auth::user()->name) ? Auth::user()->name : 'Guest';
            if (!Storage::disk('local_manual_log_charges')->exists('manual_logs')) {
                Storage::disk('local_manual_log_charges')->makeDirectory('manual_logs');
            }

            if (!Storage::disk('local_manual_log_charges')->exists('manual_logs/' . date('d-m-Y'))) {
                Storage::disk('local_manual_log_charges')->makeDirectory('manual_logs/' . date('d-m-Y'));
            }

            $fileContent = "";
            if (!Storage::disk('local_manual_log_charges')->exists('manual_logs/' . date('d-m-Y') . '/' . $auth_id . '.txt')) {
                Storage::disk('local_manual_log_charges')->put('manual_logs/' . date('d-m-Y') . '/' . $auth_id . '.txt', '');
            } else {
                $fileContent = Storage::disk('local_manual_log_charges')->get('manual_logs/' . date('d-m-Y') . '/' . $auth_id . '.txt');
            }

            if ($htt_exp) {
                $err_code = $e->getStatusCode();
                if ($err_code == 302 || $err_code == 404 || $err_code == 500) {
                    $err_msg = $ret_err_code = $err_code;
                } else {
                    $err_msg = $e->getMessage();
                    $ret_err_code = 0;
                }
            } else {
                $err_msg = $e->getMessage();
                $ret_err_code = 0;
            }

            $string = " ==================================================== \n";
            $string .= " URL Path : " . $_SERVER['REQUEST_URI'] . "\n";
            $string .= " Error Message : $err_msg \n";
            $string .= " File path : $err_file \n";
            $string .= " Line Number : $err_line \n";
            Storage::disk('local_manual_log')->put('manual_logs/' . date('d-m-Y') . '/' . $auth_id . '.txt', $string . "\n" . $fileContent);
        } catch (Exception $e) {
            die("Errors " . $e->getMessage());
        }
        return $ret_err_code;
    }


    /*     * *************** Start amazon s3 login user folder creation Method *********************************************** */


	public static function amazon_server_folder_check($module, $file, $file_store_name, $src = null, $practicename = null, $resize = null, $file_old_name = null, $main_module = "document") {
        $main_dir_name = ''; //Main dir name base practice id
        if ($practicename == '' && Session::get('practice_dbid') != '') {
            $main_dir_name = md5('P' . Session::get('practice_dbid'));
        } elseif ($practicename != '') {
            $main_dir_name = $practicename;
        }

        if ($main_dir_name != '') {
            $chk_env_site = getenv('APP_ENV'); //Check files from production or local
            $default_view = Config::get('siteconfigs.production.defult_production');
            if ($chk_env_site == $default_view) {
                $storage_disk = "s3_production";
                $bucket_name = "medcubicsproduction";
            } else {
                $storage_disk = "s3";
                $bucket_name = "medcubicslocal";
            }
            //Get s3 server storage disk url
            $store_domain = Storage::disk($storage_disk)->getDriver()->getAdapter()->getClient()->getObjectUrl('teststring', $bucket_name);
            $store_domain = str_replace("teststring.", "", $store_domain);
            $main_dir_arr = Storage::disk($storage_disk)->directories(); //we get list of directories from s3 server

            if (!in_array($main_dir_name, $main_dir_arr)) {
                Storage::disk($storage_disk)->makeDirectory($main_dir_name); //Check and create main directory
            }
            $main_dir_arrimg = Storage::disk($storage_disk)->directories($main_dir_name); //Get list of sub directories from s3 server

            if (!in_array($main_dir_name . "/" . $main_module, $main_dir_arrimg)) {
                Storage::disk($storage_disk)->makeDirectory($main_dir_name . "/" . $main_module); //Check and create sub directory
            }
            $main_dir_arr_module = Storage::disk($storage_disk)->directories($main_dir_name . '/' . $main_module); //Get list of child directories

            if (!in_array($main_dir_name . "/" . $main_module . "/" . $module, $main_dir_arr_module)) {
                Storage::disk($storage_disk)->makeDirectory($main_dir_name . "/" . $main_module . "/" . $module); //Check and create child directory
            }

            //Check file avail from upload or webcam image
            if (!empty($src)) {
                $store_file_val = file_get_contents($src);
            } else {
                //Image resize option in profile image
                if (is_array($resize)) {
                    $image = Image::make($file)->resize($resize[0], $resize[1])->stream();
                    $store_file_val = $image->__toString();
                } elseif ($main_module == 'patienteligibility') {
                    $store_file_val = $file;
                } else {
                    $store_file_val = File::get($file);
                }
            }

            //Delete profile old image from S3 server
            if ($main_module == 'image') {
                if ($file_old_name != '' && Storage::disk($storage_disk)->exists($main_dir_name . "/" . $main_module . "/" . $module . "/" . $file_old_name)) {
                    Storage::disk($storage_disk)->delete($main_dir_name . "/" . $main_module . "/" . $module . "/" . $file_old_name);
                }
            }
            Storage::disk($storage_disk)->put($main_dir_name . "/" . $main_module . "/" . $module . "/" . $file_store_name, $store_file_val, 'public'); //Store to s3 server

            if ($main_module == 'document') {
                $store_path = $main_dir_name . "/" . $main_module . "/" . $module . "/";
                $res = array($store_path, $store_domain); //return storage url and storage domain
                return $res;
            } elseif ($main_module == 'patienteligibility') {
                $pdf_url = Storage::disk($storage_disk)->getDriver()->getAdapter()->getClient()->getObjectUrl($bucket_name, $main_dir_name . "/" . $main_module . "/" . $module . "/" . $file_store_name);
                return $pdf_url;
            } else {
                return true;
            }
        }
    }

    public static function amazon_server_get_file($document_path, $document_file_name) {
        $default_view = Config::get('siteconfigs.production.defult_production');
        $chk_env_site = getenv('APP_ENV');
        if ($chk_env_site == "local")
            $storage_disk = "s3";
        elseif ($chk_env_site == $default_view)
            $storage_disk = "s3_production";
        else
            $storage_disk = "s3";
        ob_clean();
        $file = Storage::disk($storage_disk)->get($document_path . $document_file_name);

        /* -- set header donwload based on file type.

        .htm, .html         Response.ContentType = "text/HTML";
        .txt                Response.ContentType = "text/plain";
        .doc, .rtf, .docx   Response.ContentType = "Application/msword";
        .xls, .xlsx         Response.ContentType = "Application/x-msexcel";
        .jpg, .jpeg         Response.ContentType = "image/jpeg";
        .gif                Response.ContentType =  "image/GIF";
        .pdf                Response.ContentType = "application/pdf";
        Content-Type: application/octet-stream
        Content-Disposition: attachment; filename="picture.png"

        $filename = $document_path . $document_file_name;
        $fDet = pathinfo($document_path . $document_file_name);
        switch ($fDet['extension']) {
            case 'pdf':
                $type = "application/pdf";
                break;

            case 'xls':
            case 'xlsx':
                $type = "Application/x-msexcel";
                break;

            case 'jpg':
            case 'jpeg':
                $type = "image/jpeg";
                break;

            case 'png':
                $type = "image/jpeg";
                break;

            default:
                $type = "application/octet-stream";
                break;
        }

        function forceDownload($filename, $type = "application/octet-stream") {
            header('Content-Type: '.$type.'; charset=utf-8');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
        }

        $file = $disk->get($filename);
        $fSize = $disk->size($filename);
        header ( 'Content-Length: ' . $fSize );
        header('Content-Type: '.$type.'; charset=utf-8'); // header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        echo $file;
        exit;
        */

        return $file;
    }


    public static function amazon_server_folder_check_old($module, $file, $file_store_name, $src = null, $practicename = null, $resize = null, $file_old_name = null, $main_module = "document") { // google bucket implemented
       try{
            $main_dir_name = ''; //Main dir name base practice id
            if ($practicename == '' && Session::get('practice_dbid') != '') {
                $main_dir_name = md5('P' . Session::get('practice_dbid'));
            } elseif ($practicename != '') {
                $main_dir_name = $practicename;
            }

            if ($main_dir_name != '') {
                $chk_env_site = getenv('APP_ENV'); //Check files from production or local
                $default_view = Config::get('siteconfigs.production.defult_production');
                /*
                if ($chk_env_site == $default_view) {
                    $storage_disk = "s3_production";
                    $bucket_name = "medcubicsproduction";
                } else {
                    $storage_disk = "s3";
                    $bucket_name = "medcubicslocal";
                }
                */
                $storage_disk = "gcs";
                $bucket_name = "medcubics";

                //Get s3 server storage disk url
                $store_domain = Storage::disk($storage_disk)->url('/');
                $main_dir_arr = Storage::disk($storage_disk)->directories(); //we get list of directories from s3 server

                if (!in_array($main_dir_name, $main_dir_arr)) {
                    Storage::disk($storage_disk)->makeDirectory($main_dir_name); //Check and create main directory
                }
                $main_dir_arrimg = Storage::disk($storage_disk)->directories($main_dir_name); //Get list of sub directories from s3 server

                if (!in_array($main_dir_name . "/" . $main_module, $main_dir_arrimg)) {
                    Storage::disk($storage_disk)->makeDirectory($main_dir_name . "/" . $main_module); //Check and create sub directory
                }
                $main_dir_arr_module = Storage::disk($storage_disk)->directories($main_dir_name . '/' . $main_module); //Get list of child directories

                if (!in_array($main_dir_name . "/" . $main_module . "/" . $module, $main_dir_arr_module)) {
                    Storage::disk($storage_disk)->makeDirectory($main_dir_name . "/" . $main_module . "/" . $module); //Check and create child directory
                }

                //Check file avail from upload or webcam image
                if (!empty($src)) {
                    $store_file_val = file_get_contents($src);
                } else {
                    //Image resize option in profile image
                    if (is_array($resize)) {
                        $image = Image::make($file)->resize($resize[0], $resize[1])->stream();
                        $store_file_val = $image->__toString();
                    } elseif ($main_module == 'patienteligibility') {
                        $store_file_val = $file;
                    } else {
                        $store_file_val = File::get($file);
                    }
                }

                //Delete profile old image from S3 server
                if ($main_module == 'image') {
                    if ($file_old_name != '' && Storage::disk($storage_disk)->exists($main_dir_name . "/" . $main_module . "/" . $module . "/" . $file_old_name)) {
                        Storage::disk($storage_disk)->delete($main_dir_name . "/" . $main_module . "/" . $module . "/" . $file_old_name);
                    }
                }
                $res = Storage::disk($storage_disk)->put($main_dir_name . "/" . $main_module . "/" . $module . "/" . $file_store_name, $store_file_val, 'public'); //Store to s3 server
                //\Log::info($res);
                if ($main_module == 'document') {
                    $store_path = $main_dir_name . "/" . $main_module . "/" . $module . "/";
                    $res = array($store_path, $store_domain); //return storage url and storage domain
                    return $res;
                } elseif ($main_module == 'patienteligibility') {
                    $pdf_url = Storage::disk($storage_disk)->url('/').$main_dir_name . "/" . $main_module . "/" . $module . "/" . $file_store_name;
                    return $pdf_url;
                } else {
                    return true;
                }
            }
        } catch(Exception $e){
            \Log::info("Error: ".$e->getMessage());
        }
    }

    public static function amazon_server_get_file_old($document_path, $document_file_name) {  // google bucket implemented
        $default_view = Config::get('siteconfigs.production.defult_production');
        $chk_env_site = getenv('APP_ENV');
        /*
        if ($chk_env_site == "local")
            $storage_disk = "s3";
        elseif ($chk_env_site == $default_view)
            $storage_disk = "s3_production";
        else
            $storage_disk = "s3";
        */
        $storage_disk = "gcs";
        $file = Storage::disk($storage_disk)->get($document_path . $document_file_name);
        return $file;
    }

    /*     * *************** end amazon s3 login user folder creation Method *********************************************** */
    /*     * *************** Commom function for blade file to display right and wrong icon on usps and NpI api start *********************************************** */

    public static function commonNPIcheck_view($is_vali_npi, $type = null) {
        $npi_check = ApiConfig::where('api_for', 'npi')->where('api_status', 'Active')->first();
        $get_practiceAPI = DBConnectionController::getUserAPIIds('npi');
        $varvalid_class = 'hide';
        $invarvalid_class = 'hide';
        if ($is_vali_npi == 'Yes') {
            $varvalid_class = '';
        } elseif ($is_vali_npi == 'No') {
            $invarvalid_class = '';
        }

        if ($get_practiceAPI == 1) {
            if ($npi_check && !$type) {
                return '<span class="js-npi-group-success ' . $varvalid_class . '"><a data-toggle="modal" href="" data-target="#form-npi-modal"><i class="fa fa-check icon-green"></i></a></span>
                     <span class="js-npi-group-error ' . $invarvalid_class . '"><a data-toggle="modal" href=""  data-target="#form-npi-modal"><i class="fa fa-close icon-red"></i></a></span>';
            } elseif ($npi_check && $type == 'induvidual') {
                return '<span class="js-npi-individual-success ' . $varvalid_class . '"><a data-toggle="modal" href=""  data-target="#form-npi-modal"><i class="fa fa-check icon-green-form"></i></a></span>
                     <span class="js-npi-individual-error ' . $invarvalid_class . '"><a data-toggle="modal" href=""  data-backdrop="false" data-target="#form-npi-modal"><i class="fa fa-close icon-red-form"></i></a></span>';
            } elseif ($npi_check && $type == 'provider') {
                return '<span class="js-npi-individual-success ' . $varvalid_class . '"><a data-toggle="modal" href=""  data-target="#form-npi-modal"><i class="fa fa-check icon-green-form" style="margin-top:-3px; margin-left:10px;"></i></a></span>
                     <span class="js-npi-individual-error ' . $invarvalid_class . '"><a data-toggle="modal" href=""  data-backdrop="false" data-target="#form-npi-modal"><i class="fa fa-close icon-red-form" style="margin-top:-3px; margin-left:10px;"></i></a></span>';
            } else {
                return "";
            }
        } else {
            return "";
        }
    }

    public static function commonUSPScheck_view($is_valid_address, $type = null) {
        $usps_api = ApiConfig::where('api_for', 'address')->where('api_status', 'Active')->count();
        $get_practiceAPI = DBConnectionController::getUserAPIIds('address');

        $varvalid_class = 'hide';
        $invarvalid_class = 'hide';
        if ($is_valid_address == 'Yes') {
            $varvalid_class = '';
        } elseif ($is_valid_address == 'No') {
            $invarvalid_class = '';
        }

        $css_class_add = '';
        if ($type == 'css_class_add') {
            $type = '';
            $css_class_add = 'margin-t-22';
        }

        if ($get_practiceAPI == 1) {
            if ($usps_api && $type == 'popup') {
                return '<span class="js-address-success ' . $varvalid_class . '"><i class="fa fa-check icon-green-form ' . $css_class_add . '"></i></span>
                   <span class="js-address-error ' . $invarvalid_class . '"><i class="fa fa-close icon-red-form ' . $css_class_add . '"></i></span>';
            } elseif ($usps_api && !$type) {
                return '<span class="js-address-success ' . $varvalid_class . '"><a data-toggle="modal" href=""  data-target="#form-address-modal"><i class="fa fa-check icon-green-form ' . $css_class_add . '"></i></a></span>
                   <span class="js-address-error ' . $invarvalid_class . '"><a data-toggle="modal" href=""  data-target="#form-address-modal"><i class="fa fa-close icon-red-form ' . $css_class_add . '"></i></a></span>';
            } elseif ($usps_api && $type == 'show') {
                return '<span class="js-address-success ' . $varvalid_class . '"><a data-toggle="modal" href=""  data-target="#form-address-modal"><i class="fa fa-check icon-green ' . $css_class_add . '"></i></a></span>
               <span class="js-address-error ' . $invarvalid_class . '"><a data-toggle="modal"  href=""  data-target="#form-address-modal"><i class="fa fa-close icon-red ' . $css_class_add . '"></i></a></span>';
            } else {
                return "";
            }
        } else {
            return "";
        }
    }

    /*     * ************ Commom function for blade file to display right and wrong icon on usps and NpI api end    ************ */

    public static function mediauploadpath($practicename, $module, $file, $resize, $file_store_name, $file_old_name = null, $patient_id = null, $src = null) {
        if ($module == 'patienteligibility') {
            $main_module = $module;
            $module = $patient_id;
        } else {
            $main_module = "image";
        }
        return self::amazon_server_folder_check($module, $file, $file_store_name, $src, $practicename, $resize, $file_old_name, $main_module);
    }

    public static function removeimage($practicename, $module, $file_name) {
        $main_dir_name = $practicename;
        if ($practicename == '' && Session::get('practice_dbid') != '') {
            $main_dir_name = md5('P' . Session::get('practice_dbid'));
        }
        $default_view = Config::get('siteconfigs.production.defult_production');
        if ($main_dir_name != '') {
            $chk_env_site = getenv('APP_ENV');
            if ($chk_env_site == $default_view) {
                $storage_disk = "s3_production";
                $bucket_name = "medcubicsproduction";
            } else {
                $storage_disk = "s3";
                $bucket_name = "medcubicslocal";
            }

            if ($file_name != '' && Storage::disk($storage_disk)->exists($main_dir_name . "/image/" . $module . "/" . $file_name)) {
                Storage::disk($storage_disk)->delete($main_dir_name . "/image/" . $module . "/" . $file_name);
            }
        }
    }

    public static function getColor($num = 6) {
        $rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
        return $color = '#' . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)] . $rand[rand(0, 15)];
        /* $hash = md5('color'.$num); // modify 'color' to get a different palette
          return array(
          hexdec(substr($hash, 0, 2)),
          hexdec(substr($hash, 2, 2)),
          hexdec(substr($hash, 4, 2))
          ); */
    }

    /*     * *To enable and disable the document upload for scanner and webcam starts** */

    public static function getDocumentUpload($type) {
        if ($type == 'webcam') {
            return config('siteconfigs.document_upload.webcam');
        } elseif ($type == 'scanner') {
            return config('siteconfigs.document_upload.scanner');
        }
    }

    /*     * *To enable and disable the document upload for scanner and webcam ends** */

    public static function getTimeSlotByGivenTime($starttime, $endtime, $duration = 10, $get_upcoming_slot = 'no') {
        if ($get_upcoming_slot == 'yes')
            $get_current_time = date("h:i a");
        $array_of_time = array();
        $start_time = strtotime($starttime);
        $end_time = strtotime($endtime);

        //dd($starttime);
        $add_mins = $duration * 60;
        $i = 0;
        $time_slot = [];
        while ($start_time <= $end_time) {
            $array_of_time[$i] = date("h:i a", $start_time);
            if ($i > 0) {
                if (($get_upcoming_slot == 'yes' && strtotime($get_current_time) <= $start_time) || $get_upcoming_slot == 'no') {
                    $time_slot[$array_of_time[$i - 1] . '-' . date("h:i a", $start_time)] = $array_of_time[$i - 1] . '-' . date("h:i a", $start_time);
                }
            }
            $i++;
            $start_time += $add_mins;
        }
        //dd($time_slot);
        // if($duplicate_array != '')
        //$time_slot = array_diff ($time_slot, $duplicate_array);
        // $time_slot = array_values($time_slot);
        if ($endtime == "11:59 pm") {
            if ($duration == 5)
                $time_slot["11:55 pm-11:59 pm"] = "11:55 pm-11:59 pm";
            if ($duration == 10 && ((strpos($starttime, '45') == false) || (strpos($starttime, '5') == false)))
                $time_slot["11:50 pm-11:59 pm"] = "11:50 pm-11:59 pm";
            if ($duration == 15)
                $time_slot["11:45 pm-11:59 pm"] = "11:45 pm-11:59 pm";
            if ($duration == 30 && ((strpos($starttime, '00') == false) || (strpos($starttime, '30') == false)))
                $time_slot["11:30 pm-11:59 pm"] = "11:30 pm-11:59 pm";
            if ($duration == 60 && (strpos($starttime, '00') == false))
                $time_slot["11:00 pm-11:59 pm"] = "11:00 pm-11:59 pm";
        }
        //dd($time_slot);
        return $time_slot;
    }

    // To generate randon alphanumeric characters for charge Entry Process
    public static function getRandonCharacter($type = 'charge') {
        return "CHR" . date('m') . date('d') . rand(10, 1000);
    }

    /// Encode and decode id ///
    public static function getEncodeAndDecodeOfId($id, $type = 'encode') {
        $encode_decode_alg = config('siteconfigs.encode_decode_alg');
        return EncryptIdAlgorithm::$encode_decode_alg($id, $type);
    }

    public static function checkAndDisplayDateInInput($date, $condition = '', $replace = '', $format='m/d/Y') {
        if ($date != '' && $date != '-' && $date != '-0001-11-30 00:00:00' && $date != '0000-00-00 00:00:00' && $date != '0000-00-00'
                && $date != '1901-01-01' && $date != '' && $date != $condition)
            return date($format, strtotime($date));
        else
            return $replace;
    }

    /*     * * Home controller & Auth controller function Start ** */

    public static function login_history($user_id) {
        //Get IP Address and Latidude and longitude
        $ip_lat_longitude = Helpers::GetIpAndLatAndLongitude();

        // Get Browser name
        $browser = Helpers::browserName();

        //$UserLogin =UserLoginHistory::where('user_id',$user_id)->first();
        $result = [];
        $result['user_id'] = $user_id;
        $result['browser_name'] = $browser;
        $result['ip_address'] = $ip_lat_longitude["ipaddress"];
        $result['latitude'] = $ip_lat_longitude["latitude"];
        $result['logitude'] = $ip_lat_longitude["longitude"];
        //$result['address'] = $ip_lat_longitude["address"];
        return $result;
    }

    /*     * *Home controller & Auth controller function end** */

    public static function browserName() {
        $ExactBrowserNameUA = $_SERVER['HTTP_USER_AGENT'];
        if (strpos(strtolower($ExactBrowserNameUA), "safari/") and strpos(strtolower($ExactBrowserNameUA), "opr/"))
            $ExactBrowserNameBR = "Opera";
        elseif (strpos(strtolower($ExactBrowserNameUA), "safari/") and strpos(strtolower($ExactBrowserNameUA), "chrome/"))
            $ExactBrowserNameBR = "Chrome";
        elseif (strpos(strtolower($ExactBrowserNameUA), "msie"))
            $ExactBrowserNameBR = "Internet Explorer";
        elseif (strpos(strtolower($ExactBrowserNameUA), "firefox/"))
            $ExactBrowserNameBR = "Firefox";
        elseif (strpos(strtolower($ExactBrowserNameUA), "safari/") and strpos(strtolower($ExactBrowserNameUA), "opr/") == false and strpos(strtolower($ExactBrowserNameUA), "chrome/") == false)
            $ExactBrowserNameBR = "Safari";
        else
            $ExactBrowserNameBR = "OUT OF DATA";

        return $ExactBrowserNameBR;
    }

    /* Get Browser and Device Name - Anjukaselvan*/
    public static function getBrowserAndDeviceName() {

        $result['browser_name'] = '';
        $result['device_name'] = '';
        $result['platform'] = '';
        return $result;
    }

    public static function GetIpAndLatAndLongitude_old() {
        $address_ip = $address = $address_lat = $address_lon = '';
        try {
            $url = "https://ipfind.co/me?auth=78af4dfc-7fd6-4064-ae75-981e4f3e7aad"; //300 times
            $ip_find_api = Helpers::GetIpAddress($url);
            if (!empty($ip_find_api->error)) {

            } elseif ($ip_find_api && $ip_find_api->ip_address <> "") {
                $address_ip = $ip_find_api->ip_address; //returns IP
                $address_lat = $ip_find_api->latitude; //13.08 ~
                $address_lon = $ip_find_api->longitude; //80.28 ~
            } else {
                $ip_find_api = json_decode(file_get_contents("http://ip-api.com/json"));
                if ($ip_find_api && $ip_find_api->status == "success") {
                    $address_ip = $ip_find_api->query; //returns IP
                    $address_lat = $ip_find_api->lat; // 13.0833
                    $address_lon = $ip_find_api->lon; // 80.2833
                } else {
                    $ip_find_api = json_decode(file_get_contents("https://ipinfo.io/"));
                    if ($ip_find_api && $ip_find_api->ip <> "") {
                        $address_ip = $ip_find_api->ip; //returns IP
                        $location = explode(",", $ip_find_api->loc);
                        $address_lat = $location[0]; //20 ~
                        $address_lon = $location[1]; //77 ~
                    } else {
                        /*                         * * logitude & latitude start ** */
                        $location = json_decode(file_get_contents('https://freegeoip.net/json/' . $address_ip));
                        if ($location && $location->latitude <> "") {
                            $address_lat = ($location && $location->latitude) ? $location->latitude : 0;
                            $address_lon = ($location && $location->longitude) ? $location->longitude : 0;
                        }
                        /*                         * * logitude & latitude end ** */
                    }
                }
            }
            //$url      ="http://maps.googleapis.com/maps/api/geocode/json?latlng=".$address_lat.','.$address_lon."&sensor=false";
            //$address  = Helpers::GetIpAddress($url);
            $browser_name = Helpers::BrowserName();
        } catch (Exception $e) {
            // Handle if execption comes
        }

        $result["ipaddress"] = ($address_ip) ? $address_ip : '';
        //$result["address"]    = ($address->status =="OK") ? $address->results[0]->formatted_address : 'Tamilnadu, India.';
        $result["browser"] = ($browser_name) ? $browser_name : '';
        $result["longitude"] = ($address_lon) ? $address_lon : '';
        $result["latitude"] = ($address_lat) ? $address_lat : '';

        return $result;
    }


    public static function GetIpAndLatAndLongitude(){
        $result["ipaddress"] = $result["browser"] = $result["longitude"] = $result["latitude"] = '';
        try{
            $result["ipaddress"] = request()->ip();
            $result["browser"] = '';
            $result["longitude"] = '';
            $result["latitude"] = '';
        } catch(Exception $e){
            \Log::inof("While getting ip details Error.".$e->getMessage());
        }
        return $result;
    }

    public static function GetIpAddress($url) {
        $result = array();
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $result = json_decode($response);
        } catch (Exception $e) {
            // Handle if execption comes
        }
        return $result;
    }

    public static function priceFormat($price, $enabledollar = '', $forExport= 0) {
        /* if(strpos($price, '-')!== false){
          $number =number_format($price, 2);
          $price = "<span class='med-amt-red'>".$number."</span>";
          return $price;
          } */
        try{
            // If array provided handle it.
            if(is_array($price)){
                \Log::info("Invalid price format given: ".$price );
                return "0.00";
            }
            // If already formatted then no need to reformat it.
            if(strpos($price, 'span') !== false)
                return $price;

            /* To avoid the comma between amount eg:1,08,909.00 */
            $price = (float) (str_replace(',', '', $price));
            if ($enabledollar == 'export')
                return number_format($price, 2);
            elseif ($enabledollar == 'yes')
                $price_new = '$' . number_format($price, 2);
            else
                $price_new = number_format($price, 2);
            //med-amt-red
            // For export no needs to have tag.
            if($forExport)
                return $price_new;
            return (strpos($price, '-') !== false && round($price) <> 0) ? '<span class="med-red" style="text-align:right">' . $price_new . '</span>' : $price_new;
        } catch(Exception $e){
            \Log::info("Exception occured on price format: ".$e->getMessage() );
            return "0.00";
        }
    }

    public static function getNameformat($last_name, $first_name, $middle_name) {
        //$name = array($last_name,$first_name,$middle_name);
        //$commaList = implode(', ', $name);
        if ($last_name == "" && $first_name == "" && $middle_name != "")
            return $middle_name;
        elseif ($last_name == "" && $first_name != "" && $middle_name == "")
            return $first_name;
        elseif ($last_name == "" && $first_name != "" && $middle_name != "")
            return $first_name . " " . $middle_name;
        elseif ($last_name != "" && $first_name == "" && $middle_name == "")
            return $middle_name;
        elseif ($last_name != "" && $first_name == "" && $middle_name != "")
            return $first_name . " " . $middle_name;
        elseif ($last_name != "" && $first_name == "" && $middle_name != "")
            return $last_name . " " . $middle_name;
        elseif ($last_name != "" && $first_name != "" && $middle_name == "")
            return $last_name . ", " . $first_name;
        elseif ($last_name != "" && $first_name != "" && $middle_name != "")
            return $last_name . ", " . $first_name . " " . $middle_name;
        //dd($commaList);
        //return $commaList;
        //exit;
        /*
          $get_name =str_replace("_"," ",$name);
          $place_holder = ucwords($get_name);
          if($type == "provider")
          {
          if($name =="last_name")
          $max_length       = "25";
          elseif($name =="first_name")
          $max_length       = "25";
          elseif($name =="middle_name")
          $max_length       = "1";
          $provider_name= '<input type="text" name="'.$name.'" value="'.$values.'" placeholder="'.$place_holder.'" class="form-control input-sm-modal-billing js-letters-caps-format" id="'.$name.'" maxlength="'.$max_length.'" />';
          return $provider_name;
          }
          elseif($type=="customer_user")
          {

          dd($type);
          } */
    }

    /*     * * Start to get patient budget period ** */

    public static function getPatientBudgetPeriod($pass_budget) {
        $budget_period = ceil($pass_budget['balance'] / $pass_budget['amount']);
        $datetime = new DateTime($pass_budget['start_date']);
        if ($pass_budget['plan'] == 'Monthly') {
            $budget_type = 'Month';
            $single_buget = $budget_period - 1;
            $datetime->modify('+' . $single_buget . ' months');
        } elseif ($pass_budget['plan'] == 'Bimonthly') {
            $budget_type = 'Bi-month';
            $double_buget = $budget_period * 2 - 2;
            $datetime->modify('+' . $double_buget . ' months');
        } elseif ($pass_budget['plan'] == 'Weekly') {
            $budget_type = 'Week';
            $single_buget = $budget_period - 1;
            $datetime->modify('+' . $single_buget . ' week');
        } elseif ($pass_budget['plan'] == 'Biweekly') {
            $budget_type = 'Bi-week';
            $double_buget = $budget_period * 2 - 2;
            $datetime->modify('+' . $double_buget . ' week');
        }

        return ['budget_period' => $budget_period . ' ' . $budget_type, 'budget_date' => $datetime->format('m/d/y')];
    }

    /*     * * End to get patient budget period ** */

    public static function splitPhoneNumber($phone) {
        $phone_arr['code'] = '';
        $phone_arr['no'] = '';

        if ($phone != '') {
            $replace_string = ['(', ')', ' ', '-'];
            $phone = str_replace($replace_string, '', $phone);
            $phone_arr['code'] = substr($phone, 0, 3);
            $phone_arr['no'] = substr($phone, 4);
        }
        return $phone_arr;
    }

    /*     * * Patient Back button set for common file Start ** */

    public static function patientBackButton($patients) {
        //Redirect to Ledger listing page
        return url('patients/' . $patients . '/ledger');
    }

    /*     * * Patient Back button set for common file End ** */

    public static function getnewticket() {
        return DBConnectionController::getUnreadTicket();
    }

    public static function getMyreadTicket() {
        return DBConnectionController::getMyreadTicket();
    }

    // Get Patient plan end date from insurance elgibility
    public static function getPatientPlanEndDate($patient_id, $insurance_id = '', $policy_id = '', $type = '') {
        return DBConnectionController::getPatientPlanEndDate($patient_id, $insurance_id, $policy_id, $type);
    }

    // Insurance elgibility already checked or not
    public static function checkInsEligiblity($patient_id, $insurance_id, $policy_id) {
        return DBConnectionController::checkInsEligiblity($patient_id, $insurance_id, $policy_id);
    }

    // common function to calculate adjustment starts here
    public static function getCalculatedAdjustment($adjust_amt=0, $withheld_amt=0) {
        // To handle non numeric value exception.
        if(!is_numeric($adjust_amt)) {
            $adjust_amt = (trim($adjust_amt) != '') ? str_replace(",", "", strip_tags($adjust_amt)) : 0;
        }
        if(!is_numeric($withheld_amt)) {
            $withheld_amt = (trim($withheld_amt) != '') ? str_replace(",", "", strip_tags($withheld_amt)) : 0;
        }
        return self::priceFormat($adjust_amt + $withheld_amt);
    }

    // common function to calculate adjustment ends here
    // Rand number generate
    public static function code_gen() {
        $totalChar = 1;
        $salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
        srand((double) microtime() * 1000000);
        $code = "";
        for ($i = 0; $i < $totalChar; $i++)
            $code = $code . substr($salt, rand() % strlen($salt), 1);
        return($code);
    }

    public static function checknpi_valid_process($npi_num, $type_allow = '') {
        $get_practiceAPI = DBConnectionController::getUserAPIIds('npi');
        $npi_api = ApiConfig::where('api_for', 'npi')->where('api_status', 'Active')->first();
        $is_valid_npi = 'Yes';
        if ($npi_api && $get_practiceAPI == 1) {
            try{
                $npi = $npi_num;
                $url = $npi_api->url . $npi . '&taxonomy_description=&first_name=&last_name=&organization_name=&address_purpose=&city=&state=&postal_code=&country_code=&limit=&skip=&version=2.0&pretty=true';
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_URL => $url,
                    CURLOPT_SSL_VERIFYPEER => false
                ));
                $resp = curl_exec($curl);

                if (!curl_exec($curl))
                    die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
                curl_close($curl);

                $result_array = json_decode($resp);
                $enumeration_type_back_validate = @$result_array->results[0]->enumeration_type;

                if (isset($result_array->Errors))
                    $is_valid_npi = 'No';
                elseif (isset($result_array) && !$result_array->result_count == 1)
                    $is_valid_npi = 'No';
                elseif ($type_allow != '') {
                    if ($enumeration_type_back_validate != $type_allow)
                        $is_valid_npi = 'No';
                    else
                        $is_valid_npi = 'Yes';
                } else
                    $is_valid_npi = 'Yes';
            } catch(Exception $e) {
                \Log::info("Error occured on Check NPI Valid Process ".$e->getMessage() );
            }
        }
        return $is_valid_npi;
    }

    #### Common validation updated BY created BY ###

    public static function shortname($id) {
        if ($id > 0) {
            $user = Users::where('id', $id)->pluck('short_name')->first();
            return ucfirst($user);
        } else {
            return '';
        }
    }

    public static function getUserFullName($id) {
        if ($id > 0) {
            $user = Users::where('id', $id)->pluck('name')->first();
            return ucfirst($user);
        } else {
            return '';
        }
    }

    public static function payer_shortname($id) {
        if ($id > 0) {
            $user = Insurance::where('id', $id)->pluck('short_name')->first();
            return ucfirst($user);
        } else {
            return 'NA';
        }
    }

    #### Common validation updated BY created BY ###
    /*  Date of birth age calculation  */

    public static function dob_age($dob) {
        $age = date_diff(date_create($dob), date_create('today'))->y;
        if ($age > 0) {
            if ($age == 1)
                return $age . ' Yr';
            return $age . ' Yrs';
        }
        else {
            $mths = date_diff(date_create($dob), date_create('today'))->m;
            if ($mths == 1)
                return $mths . ' Mth';
            return $mths . ' Mths';
        }
    }


    /* Getting all user names  */
    public static function user_names($user_id) {
        $User_name =  Users::whereIn('id', explode(',', $user_id))->where('status', 'Active')->pluck('short_name', 'id')->all();
        $User_name = implode(", ", array_unique($User_name));
        return $User_name;
    }


    /* Getting all user list  */

    public static function user_list($data = '') {
        $practice_id = 4;
        if (Session::get('practice_dbid'))
            $practice_id = Session::get('practice_dbid');
        else
            $practice_id = $data;
        //$practice_det    = Practice::with('speciality_details')->where('id',$practice_id)->first();
        if($practice_id){

            $practice_user_arr1 = Setpracticeforusers::where('practice_id', '=', $practice_id)->pluck('user_id')->all();

            $admin_practice_id_like = " admin_practice_id like '$practice_id' or admin_practice_id like '$practice_id,%' or admin_practice_id like '%,$practice_id' or admin_practice_id like '%,$practice_id,%' ";
            // Handled for cron call
            if(Auth::check() && isset(Auth::user()->customer_id) && Auth::user()->customer_id != 0){
                $practice_user_arr2 = Users::whereRaw("((customer_id = ? and practice_user_type = 'customer' and status = 'Active') or ($admin_practice_id_like))", array(Auth::user()->customer_id))->pluck('id')->all();
            } else {
                $practice_via_customer = Practice::where('id',$practice_id)->first();
                $practice_user_arr2 = Users::whereRaw("(((customer_id = ? or customer_id = 0) and practice_user_type = 'customer' and status = 'Active') or ($admin_practice_id_like))", array($practice_via_customer->customer_id))->pluck('id')->all();
            }
            $practice_user_arr = array_unique(array_merge($practice_user_arr1, $practice_user_arr2));
            $user_list = Users::whereIn('id', $practice_user_arr)->where('status', 'Active')->pluck('short_name', 'id')->all();
        }else{
            $user_list = DB::connection('responsive')->table('users')->where('status', 'Active')->pluck('short_name', 'id')->all();
        }
        return $user_list;
    }

    /* Get charge edit link based on status  starts here */

    public static function getChareEditLink($id, $ins_pmt_cnt, $type) {
        $url = "";
        // New payment flow related changes- claim details fetched from claim_info instead of claims.
        $claim_data = ClaimInfoV1::where("id", $id)->select("patient_id", "status", "charge_add_type", "claim_submit_count")->first();
        if (!empty($claim_data)) {
            $status = ($claim_data->status) ? $claim_data->status : "";
            $charge_type = $claim_data->charge_add_type;
            $submit_count = $claim_data->claim_submit_count;
            $patient_id = self::getEncodeAndDecodeOfId($claim_data->patient_id, 'encode');
            $claim_id = self::getEncodeAndDecodeOfId($id, 'encode');

            if (($status == 'Rejection' && $ins_pmt_cnt == 0) || ($status == 'Hold' && $ins_pmt_cnt == 0 )) {
                if ($type == "Charge") {
                    $url = url('charges/' . $claim_id . '/edit');
                } else {
                    $url = url('patients/' . $patient_id . '/billing/create/' . $claim_id);
                }
            } elseif ($type == "Charge" && ($status == "Denied" || $submit_count > 0
                || ( $ins_pmt_cnt > 0 && ($status == "Submitted" || $status == "Ready" || $status == 'Pending' || $status == 'Paid' || $status == 'Patient') )  )) {
                $url = url('charges/' . $claim_id . '/charge_edit');
            } elseif ($type == "Billing" && ($status == "Denied" || $submit_count > 0
                || ( $ins_pmt_cnt > 0 && ($status == "Submitted" || $status == "Ready" || $status == "Pending" || $status == "Paid" || $status == 'Patient') ) ) ) {
                $url = url('patients/' . $patient_id . '/billing/edit/' . $claim_id);
            } else {
                if ($status == "Submitted") {
                    $url = "javascript:void(0)";
                } elseif ($type == "Charge" && $status != "Submitted" ) {
                    $url = url('charges/' . $claim_id . '/edit');
                } elseif ($type == "Billing" && $status != "Submitted" ) {
                    $url = url('patients/' . $patient_id . '/billing/create/' . $claim_id);
                }
            }
        }
        return $url;
    }

    public static function getChargeEditLinkByDetails($id, $ins_pmt_cnt, $type, $data_arr) {
        $url = "";
        $patient_id = ($data_arr['patient_id']) ? $data_arr['patient_id'] : 0;
        $status = ($data_arr['status']) ? $data_arr['status'] : "";
        $charge_type = ($data_arr['charge_add_type']) ? $data_arr['charge_add_type'] : "";
        $submit_count = ($data_arr['claim_submit_count']) ? $data_arr['claim_submit_count'] : 0;

        $patient_id = self::getEncodeAndDecodeOfId($patient_id, 'encode');
        $claim_id = self::getEncodeAndDecodeOfId($id, 'encode');

        if (($status == 'Rejection' && $ins_pmt_cnt == 0) || ($status == 'Hold' && $ins_pmt_cnt == 0 )) {
            if ($type == "Charge") {
                $url = url('charges/' . $claim_id . '/edit');
            } else {
                $url = url('patients/' . $patient_id . '/billing/create/' . $claim_id);
            }
        } elseif ($type == "Charge" && ( $status == "Denied" || $submit_count > 0
                || ($ins_pmt_cnt > 0 && ( $status == "Submitted" || $status == "Ready" || $status == "Pending" || $status == "Paid" || $status == 'Patient'))  )) {
            $url = url('charges/' . $claim_id . '/charge_edit');
        } elseif ($type == "Billing" && ( $status == "Denied" || $submit_count > 0
                || ($ins_pmt_cnt > 0 && ( $status == "Submitted" || $status == "Ready" || $status == "Pending" || $status == "Paid" || $status == 'Patient') ) ) ) {
            $url = url('patients/' . $patient_id . '/billing/edit/' . $claim_id);
        } else {
            if ($status == "Submitted") {
                $url = "javascript:void(0)";
            } elseif ($type == "Charge" && $status != "Submitted") {
                $url = url('charges/' . $claim_id . '/edit');
            } elseif ($type == "Billing" && $status != "Submitted") {
                $url = url('patients/' . $patient_id . '/billing/create/' . $claim_id);
            }
        }
        return $url;
    }

    /* Get charge edit link based on status  starts here */

     public static function getInsuranceNameLists($patient_id = 0, $withFullname = 0) {
        if($patient_id > 0) {
            $insurance_list = Insurance::with('patient_insurance')->whereHas('patient_insurance', function($q) use($patient_id) {
                        $q->where('patient_id', $patient_id)->where('category', 'Primary');
                    })->select('short_name', 'id')
            ->where('status', 'Active')->where('short_name', '<>', '')->orderby('short_name','asc');
            if($withFullname) {
                $insurance_list = $insurance_list->selectRaw('CONCAT(short_name," - ",insurance_name) as concat_name, id')->pluck("concat_name", "id")->all();
            } else {
                $insurance_list = $insurance_list->pluck("short_name", "id")->all();
            }

        } else {
            $practice_id = (Session::get('practice_dbid')) ? $practice_id = Session::get('practice_dbid') : 4;
            $insurance_list = //Cache::remember('insurance_list' . $practice_id, 22 * 60, function() {
                    /*return*/  Insurance::where('status', 'Active')->where('short_name', '<>', '')->orderby('short_name','asc');
            if($withFullname) {
                $insurance_list = $insurance_list->selectRaw('CONCAT(short_name," - ",insurance_name) as concat_name, id')->pluck("concat_name", "id")->all();
            } else {
                $insurance_list = $insurance_list->pluck("short_name", "id")->all();
            }
        }
        return $insurance_list;
    }

    public static function getInsuranceFullNameLists($patient_id = 0) {
        if($patient_id > 0) {
            $insurance_list = Insurance::with('patient_insurance')->whereHas('patient_insurance', function($q) use($patient_id) {
                        $q->where('patient_id', $patient_id)->where('category', 'Primary');
                    })->select('insurance_name', 'id')
            ->where('status', 'Active')->where('insurance_name', '<>', '')->pluck("insurance_name", "id")->all();
        } else {
            $practice_id = (Session::get('practice_dbid')) ? $practice_id = Session::get('practice_dbid') : 4;
            $insurance_list = //Cache::remember('insurance_list' . $practice_id, 22 * 60, function() {
                    /*return*/  Insurance::where('status', 'Active')->where('insurance_name', '<>', '')->pluck("insurance_name", "id")->all();
                // });
        }

        return $insurance_list;
    }

    public static function getInsuranceName($id, $export = '') {
        $insurance_name = Insurance::where('id', $id)->pluck("short_name")->first();
        if ($export <> '') {
            return "<span>" . $insurance_name . "</span>";
        } else {
            return $insurance_name;
        }
    }

	public static function getInsuranceNameWithType($id, $export = '') {
        $insurance_name = Insurance::with('insurancetype')->where('id', $id)->first();

		$insuranceArr['insurance'] = $insuranceArr['insuranceType'] = '';
        if ($export <> '') {
            $insuranceArr['insurance'] =  "<span>" . $insurance_name->short_name . "</span>";
            $insuranceArr['insuranceType'] =  "<span>" . isset($insurance_name->insurancetype) ? $insurance_name->insurancetype->type_name : '-Nil-' . "</span>";
        } else {
            $insuranceArr['insurance'] =  $insurance_name->short_name;
			$insuranceArr['insuranceType'] = isset($insurance_name->insurancetype) ? $insurance_name->insurancetype->type_name : '-Nil-';
        }
		return $insuranceArr;
    }

    public static function getInsuranceTypeName() {
        $insurance_name = Insurancetype::pluck("type_name","id")->all();
        return $insurance_name;
    }

    public static function getInsuranceFullName($id, $export = '') {
        $insurance_name = Insurance::where('id', $id)->pluck("insurance_name")->first();
        if ($export <> '') {
            return "<span>" . $insurance_name . "</span>";
        } else {
            return $insurance_name;
        }
    }

    public static function getAnesthesiaCalculation($minute = null) {
        $anesthesia_unit = '';
        if (!empty($minute)) {
            $anesthesia_unit = $minute / 15;
            //$anesthesia_unit = Math.floor(anesthesia_unit);
            $remaining_muinute = $minute % 15;
            if ($remaining_muinute >= 10) {
                $anesthesia_unit = parseInt($anesthesia_unit) + parseInt(1);
            }
        }
        return $anesthesia_unit;
    }

    /* Set Page Title start */

    public static $pageTitle = '';

    public static function getPageTitle() {
        $pageTitle = 'Medcubics';
        $current_page = Route::getFacadeRoot()->current()->uri();
        $current_arr = explode('/', $current_page);


        if (self::$pageTitle == "") {
            switch ($current_arr[0]) {

                case 'patients':
                    $pageTitle = 'Patient | Medcubics';
                    break;

                case 'listfavourites':
                    $pageTitle = 'Favorite List | Medcubics';
                    break;

                case 'modifierlevel1':
                case 'modifierlevel2':
                    $pageTitle = 'Modifiers | Medcubics';
                    break;

                case 'feeschedule':
                    $pageTitle = 'Fee Schedule | Medcubics';
                    break;

                case 'practiceproviderschedulerlist':
                    $pageTitle = 'Provider List | Medcubics';
                    break;

                case 'practicefacilityschedulerlist':
                    $pageTitle = 'Facility List | Medcubics';
                    break;

                case 'patientstatementsettings':
                    $pageTitle = 'Patient Statement | Medcubics';
                    break;

                case 'apisettings':
                    $pageTitle = 'API Settings | Medcubics';
                    break;

                case 'userapisettings':
                    $pageTitle = 'User API Settings | Medcubics';
                    break;

                case 'bulkstatement':
                    $pageTitle = 'Bulk Statement | Medcubics';
                    break;

                case 'individualstatement':
                    $pageTitle = 'Individual Statement | Medcubics';
                    break;

                case 'statementhistory':
                    $pageTitle = 'Statement History | Medcubics';
                    break;

                default:
                    $pageTitle = ucwords(strtolower($current_arr[0])) . ' | Medcubics';
                    break;
            }
        } else {
            $pageTitle = self::$pageTitle;
        }

        /**
          Returning format:
          Patient Edit/View - Acct No | Medcubics     Eg: 10054 | Medcubics
          Charge Edit/View - Claim No | Medcubics     Eg: 50054 | Medcubics
          Module - ModuleName | Medcubics             Eg: Scheduler | Medcubics
          Reports - Report Name | Medcubics           Eg: Practice Setting Report | Medcubics
         */
        return $pageTitle;
    }

    public static function setPageTitle($pageFor, $details) {
        switch ($pageFor) {

            case 'patients':
                self::$pageTitle = (isset($details['account_no']) ? $details['account_no'] : 'Patients') . ' | Medcubics';
                break;

            case 'charges':
                self::$pageTitle = (isset($details['claim_no']) ? $details['claim_no'] : 'Charges') . ' | Medcubics';
                break;

            case 'reports':
                self::$pageTitle = (isset($details['type']) ? $details['type'] : '') . 'Report | Medcubics';
                break;

            case 'practice':
                self::$pageTitle = (isset($details['practice_name']) ? $details['practice_name'] : 'Practice') . ' | Medcubics';
                break;

            default:
                self::$pageTitle = ' Medcubics';
                break;
        }
    }

    /* Set Page Title end */

    public static function getPatientInsurance($patient_id) {
        $patient_insurances = PatientInsurance::with(array('insurance_details' => function($query) {
                        $query->select('id', 'insurance_name');
                    }))->whereHas('insurance_details', function($q) {
                    $q->where('status', 'Active');
                })->where('patient_id', $patient_id)->orderBy('insurance_id', 'ASC')->get();
        $insurance = array();
        if (!empty($patient_insurances)) {
            foreach ($patient_insurances as $patient_insurance) {
                $insurance[$patient_insurance->insurance_id] = @$patient_insurance->insurance_details->insurance_name;
            }
        }
        return $insurance;
    }


   /* Stats page functions start */
    public static function getTodayAndMonthUnbilled() {
        $result = [];
        $result["today"] = $result["month"] = 0.0;
        /*
        $result["today"] = Helpers::priceFormat(Claims::has('paymentclaimtransaction', '<', 1)->whereIn('status', ['Ready'])->whereRaw('Date(created_at) = DATE(CURDATE())')->sum('total_charge'), 'no');
        $result["month"] = Claims::has('paymentclaimtransaction', '<', 1)->whereIn('status', ['Ready'])->whereRaw('MONTH(created_at) = MONTH(CURDATE())')->sum('total_charge');
         */
        $result["today"] = Helpers::priceFormat(ClaimInfoV1::where('claim_submit_count', 0)
                          ->whereIn('status', ['Ready'])
                          ->whereRaw('Date(created_at) = DATE(UTC_TIMESTAMP())')->sum('total_charge'), 'no');
        $result["month"] = ClaimInfoV1::where('claim_submit_count', 0)
                          ->whereIn('status', ['Ready'])
						  ->whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())')
                          ->sum('total_charge');
		$result["total_allyear"] = ClaimInfoV1::where('claim_submit_count', 0)
                          ->whereIn('status', ['Ready'])
                          ->sum('total_charge');
        return $result;
    }

    public static function getTodayAndMonthRejection() {
        $result = [];
        $result["today"] = ClaimInfoV1::whereRaw('Date(created_at) = DATE(UTC_TIMESTAMP())')->where('status', 'Rejection')->count();
        $result["month"] = ClaimInfoV1::whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP())')->where('status', 'Rejection')->count();
        return $result;
    }

    // Stats to get current month year charges total
    public static function CurrentMonthYearCharges()
    {
        $result["month"] = ClaimInfoV1::whereNotIn('status', ['Hold','Rejection'])->whereRaw('MONTH(created_at) = MONTH(UTC_TIMESTAMP()) AND YEAR(created_at) = YEAR(UTC_TIMESTAMP())')->where('claim_submit_count', '>', 0)->sum('total_charge');
        $result["year"] = ClaimInfoV1::whereNotIn('status', ['Hold','Rejection'])->where('claim_submit_count', '>', 0)->whereRaw('YEAR(created_at) = YEAR(UTC_TIMESTAMP())')->sum('total_charge');
        return $result;
    }

    public static function getCurrentMonthYearTotalCollections($practice_id='') {

        $result = [];
        $result["month"] = $result["year"] = $result["till_date"] = 0.0;
        // switch practice page added condition for provider login based showing values
        // Revision 1 - Ref: MR-2719 22 Aug 2019: Selva
        $practice_timezone = Helpers::getPracticeTimeZone($practice_id);
        $start_date = date('Y-m-01',strtotime(Carbon::now($practice_timezone)));
        $end_date = date('Y-m-d',strtotime(Carbon::today($practice_timezone)));

        if(Auth::check() && Auth::user()->isProvider()){
        $provider_id = Auth::user()->provider_access_id;

            //Month Payment and Refund
            $result["payment_month"] = PMTClaimTXV1::has('claim')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->where('pmt_method','Insurance')->whereIn('pmt_type',['Payment'])->where('deleted_at',Null)->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })->sum(DB::raw('total_paid'));
            $result["ins_refund_month"] = PMTClaimTXV1::whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")
                    ->where('pmt_method','Insurance')
                    ->where('total_paid', '<', 0)
                    ->whereIn('pmt_type',['Refund'])->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })
                    //->where('deleted_at',Null)
                    ->sum(DB::raw('total_paid'));
            $wallet_amt_month = 0;
            /*$wallet_month=\DB::table('pmt_info_v1')->where('pmt_method','Patient')->whereIn('pmt_type', ['Payment','Credit Balance'])->selectRaw('(sum(pmt_amt)-sum(amt_used)) as pmt_amt')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'");
            $wallet_month = $wallet_month->whereNull('void_check')->whereNull('pmt_info_v1.deleted_at')->get();
            if($wallet_month[0]->pmt_amt!=null)
                $wallet_amt_month = $wallet_month[0]->pmt_amt;*/

            $patientPaymentMth = PMTClaimTXV1::has('claim')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")
                    ->where('pmt_method','Patient')
                    ->whereIn('pmt_type',['Payment','Credit Balance'])->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })
                    //->where('void_check',Null)
                    //->where('deleted_at',Null)
                    ->sum(DB::raw('total_paid'));


            $patientRefundMth = PMTClaimTXV1::has('claim')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")
                    ->where('pmt_method','Patient')
                    ->whereIn('pmt_type',['Refund'])->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })
                    //->where('source','posting')
                    //->where('void_check',Null)
                    //->where('deleted_at',Null)
                    ->sum(DB::raw('total_paid'));
            $result["pat_payment_month"] = $patientPaymentMth;
            if ($patientRefundMth < 0)
                $result["pat_refund_month"] = $patientRefundMth;
            else
                $result["pat_refund_month"] = -1*$patientRefundMth;
            //Month Collection = Month payment - Month Refund
            $result["month"] = (( $result["payment_month"] + $result["pat_payment_month"]+$wallet_amt_month+$result["ins_refund_month"]) + $result["pat_refund_month"] );
            //Year Payment and Refund
            $result["payment_year"] = PMTClaimTXV1::whereRaw("YEAR(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) = YEAR('".NOW()."')")
                    ->where('pmt_method','Insurance')
                    ->whereIn('pmt_type',['Payment'])
                    ->where('deleted_at',Null)->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })
                    ->sum(DB::raw('total_paid'));
            $result["ins_refund_year"] = PMTClaimTXV1::whereRaw("YEAR(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) = YEAR('".NOW()."')")
                    ->where('pmt_method','Insurance')
                    ->where('total_paid', '<', 0)
                    ->whereIn('pmt_type',['Refund'])->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })
                    //->where('deleted_at',Null)
                    ->sum(DB::raw('total_paid'));
            $wallet_amt = 0;
            /*$wallet=\DB::table('pmt_info_v1')->where('pmt_method','Patient')->whereIn('pmt_type', ['Payment','Credit Balance'])->selectRaw('(sum(pmt_amt)-sum(amt_used)) as pmt_amt')->whereRaw("YEAR(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) = YEAR('".NOW()."')");
            $wallet = $wallet->whereNull('void_check')->whereNull('pmt_info_v1.deleted_at')->get();
            if($wallet[0]->pmt_amt!=null)
                $wallet_amt = $wallet[0]->pmt_amt;*/
            $patientPaymentYr = PMTClaimTXV1::whereRaw("YEAR(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) = YEAR('".NOW()."')")
                    ->where('pmt_method','Patient')->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })
                    //->where('void_check',Null)
                    //->where('deleted_at',Null)
                    ->whereIn('pmt_type',['Payment','Credit Balance'])
                    ->sum(DB::raw('total_paid'));

             $patientRefundYr = PMTClaimTXV1::has('claim')->whereRaw("YEAR(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) = YEAR('".NOW()."')")
                    ->where('pmt_method','Patient')->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })
                    //->where('void_check',Null)
                    //->where('deleted_at',Null)
                    ->whereIn('pmt_type',['Refund'])
                    ->sum(DB::raw('total_paid'));
            $result["pat_payment_year"] = $patientPaymentYr;
            if ($patientRefundYr < 0)
                $result["pat_refund_year"] = $patientRefundYr;
            else
                $result["pat_refund_year"] = -1*$patientRefundYr;
            //Year Collection = Year Payment - Year Refund
            $result["year"] = (($result["payment_year"] + $result["pat_payment_year"]+$wallet_amt+$result["ins_refund_year"]) + $result["pat_refund_year"] );
        }else{
            //Month Payment and Refund
            $result["payment_month"] = PMTClaimTXV1::whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->where('pmt_method','Insurance')->whereIn('pmt_type',['Payment'])->where('deleted_at',Null)->sum(DB::raw('total_paid'));

            $result["ins_refund_month"] = PMTClaimTXV1::whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")
                    ->where('pmt_method','Insurance')
                    ->where('total_paid', '<', 0)
                    ->whereIn('pmt_type',['Refund'])
                    //->where('deleted_at',Null)
                    ->sum(DB::raw('total_paid'));
            $wallet_amt_month = 0;
            $wallet_month=\DB::table('pmt_info_v1')->where('pmt_method','Patient')->whereIn('pmt_type', ['Payment','Credit Balance'])->selectRaw('(sum(pmt_amt)-sum(amt_used)) as pmt_amt')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'");
            $wallet_month = $wallet_month->whereNull('void_check')->whereNull('pmt_info_v1.deleted_at')->get();
            if($wallet_month[0]->pmt_amt!=null)
                $wallet_amt_month = $wallet_month[0]->pmt_amt;
            $patientPaymentMth = PMTClaimTXV1::whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")
                    ->where('pmt_method','Patient')
                    ->whereIn('pmt_type',['Payment','Credit Balance'])
                    //->where('void_check',Null)
                    ->where('deleted_at',Null)
                    ->sum(DB::raw('total_paid'));


            $patientRefundMth = PMTClaimTXV1::whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")
                    ->where('pmt_method','Patient')
                    ->whereIn('pmt_type',['Refund'])
                    //->where('source','posting')
                    //->where('void_check',Null)
                    //->where('deleted_at',Null)
                    ->sum(DB::raw('total_paid'));
            $result["pat_payment_month"] = $patientPaymentMth;
            if ($patientRefundMth < 0)
                $result["pat_refund_month"] = $patientRefundMth;
            else
                $result["pat_refund_month"] = -1*$patientRefundMth;
            //Month Collection = Month payment - Month Refund
            $result["month"] = (( $result["payment_month"] + $result["pat_payment_month"]+$wallet_amt_month+$result["ins_refund_month"] ) + $result["pat_refund_month"] );
            //Year Payment and Refund
            $result["payment_year"] = PMTClaimTXV1::whereRaw("YEAR(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) = YEAR('".NOW()."')")
                    ->where('pmt_method','Insurance')
                    ->whereIn('pmt_type',['Payment'])
                    //->where('deleted_at',Null)
                    ->sum(DB::raw('total_paid'));
            $result["ins_refund_year"] = PMTClaimTXV1::whereRaw("YEAR(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) = YEAR('".NOW()."')")
                    ->where('pmt_method','Insurance')
                    ->where('total_paid', '<', 0)
                    ->whereIn('pmt_type',['Refund'])
                    //->where('deleted_at',Null)
                    ->sum(DB::raw('total_paid'));

            $patientPaymentYr = PMTClaimTXV1::whereRaw("YEAR(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) = YEAR('".NOW()."')")
                    ->where('pmt_method','Patient')
                    //->where('void_check',Null)
                    //->where('deleted_at',Null)
                    ->whereIn('pmt_type',['Payment','Credit Balance'])
                    ->sum(DB::raw('total_paid'));

             $patientRefundYr = PMTClaimTXV1::whereRaw("YEAR(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) = YEAR('".NOW()."')")
                    ->where('pmt_method','Patient')
                    //->where('void_check',Null)
                    //->where('deleted_at',Null)
                    ->whereIn('pmt_type',['Refund'])
                    ->sum(DB::raw('total_paid'));
            $wallet_amt = 0;
            $wallet=\DB::table('pmt_info_v1')->where('pmt_method','Patient')->whereIn('pmt_type', ['Payment','Credit Balance'])->selectRaw('(sum(pmt_amt)-sum(amt_used)) as pmt_amt')->whereRaw("YEAR(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) = YEAR('".NOW()."')");
            $wallet = $wallet->whereNull('void_check')->whereNull('pmt_info_v1.deleted_at')->get();
            if($wallet[0]->pmt_amt!=null)
                $wallet_amt = $wallet[0]->pmt_amt;
            $result["pat_payment_year"] = $patientPaymentYr;
            if ($patientRefundYr < 0)
                $result["pat_refund_year"] = $patientRefundYr;
            else
                $result["pat_refund_year"] = -1*$patientRefundYr;
            //Year Collection = Year Payment - Year Refund
            $result["year"] = (($result["payment_year"] + $result["pat_payment_year"]+$wallet_amt+$result["ins_refund_year"]) + $result["pat_refund_year"] );
        }

        //Payment and Refund up to date
        $result["payment_tillDate"] = PMTClaimTXV1::where('pmt_method','Insurance')
                ->whereIn('pmt_type',['Payment'])
                //->where('deleted_at',Null)
                ->sum(DB::raw('total_paid'));
        $result["ins_refund_tillDate"] = PMTClaimTXV1::where('pmt_method','Insurance')
                    ->where('total_paid', '<', 0)
                    ->whereIn('pmt_type',['Refund'])
                    //->where('deleted_at',Null)
                    ->sum(DB::raw('total_paid'));
        $wallet_amt_tillDate = 0;
        $wallet_tillDate=\DB::table('pmt_info_v1')->where('pmt_method','Patient')->whereIn('pmt_type', ['Payment','Credit Balance'])->selectRaw('(sum(pmt_amt)-sum(amt_used)) as pmt_amt');
        $wallet_tillDate = $wallet_tillDate->whereNull('void_check')->whereNull('pmt_info_v1.deleted_at')->get();
        if($wallet_tillDate[0]->pmt_amt!=null)
            $wallet_amt_tillDate = $wallet_tillDate[0]->pmt_amt;
        $patientPaymentTillDate = PMTClaimTXV1::where('pmt_method','Patient')
                //->where('void_check',Null)
                //->where('deleted_at',Null)
                ->whereIn('pmt_type',['Payment','Credit Balance'])
                //->where('created_at','!=','0000-00-00 00:00:00')
                ->sum(DB::raw('total_paid'));

         $patientRefundTillDate = PMTClaimTXV1::where('pmt_method','Patient')
                //->where('void_check',Null)
                //->where('deleted_at',Null)
                ->whereIn('pmt_type',['Refund'])
                ->sum(DB::raw('total_paid'));
	    $result["pat_payment_till"] = $patientPaymentTillDate;
        if ($patientRefundTillDate < 0)
            $result["pat_refund_till"] = $patientRefundTillDate;
        else
            $result["pat_refund_till"] = -1*$patientRefundTillDate;
        //Total Collection = Total Payment - Total Refund
        $result["till_date"] = ($result["payment_tillDate"] + $result["pat_payment_till"]+$wallet_amt_tillDate+$result["ins_refund_tillDate"])-$result["pat_refund_till"];
        return $result;
    }

    ### Provider dashboard month year total collection Start

    public static function getProviderCurrentMonthYearTotalCollections() {

        $result = [];
        $practice_timezone = Helpers::getPracticeTimeZone();
        $result["month"] = $result["year"] = $result["till_date"] = 0.0;
        //Month Payment and Refund
        $provider_id = Auth::user()->provider_access_id;
        $payment_month = PMTClaimTXV1::with('claim')->select(DB::raw('sum(pmt_claim_tx_v1.total_paid)'))->whereRaw('MONTH(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))="'.date('m').'"')
        ->whereRaw('YEAR(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))="'.date('Y').'"')->where('pmt_method','Insurance')->whereIn('pmt_type',['Payment','Refund'])->where('deleted_at',Null);
        $result['payment_month'] = $payment_month->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })->sum(DB::raw('total_paid'));
        $patientPaymentMth = PMTInfoV1::with('claim')->whereRaw('MONTH(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))="'.date('m').'"')
        ->whereRaw('YEAR(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))="'.date('Y').'"')
                ->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })
                ->where('pmt_method','Patient')
                ->whereIn('pmt_type',['Payment','Credit Balance'])
                ->where('void_check',Null)
                ->where('deleted_at',Null)
                ->sum(DB::raw('pmt_amt'));
        $patientRefundMth = PMTInfoV1::whereRaw('MONTH(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))="'.date('m').'"')
        ->whereRaw('YEAR(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))="'.date('Y').'"')
                ->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })
                ->where('pmt_method','Patient')
                ->whereIn('pmt_type',['Refund'])
                ->where('source','posting')
                ->where('void_check',Null)
                ->where('deleted_at',Null)
                ->sum(DB::raw('pmt_amt'));
        $result["pat_payment_month"] = $patientPaymentMth;
        $result["pat_refund_month"] = $patientRefundMth;
        //Month Collection = Month payment - Month Refund
        $result["month"] = (( $result["payment_month"] + $result["pat_payment_month"] ) - $result["pat_refund_month"] );

        //Year Payment and Refund
        $result["payment_year"] = PMTClaimTXV1::whereRaw('YEAR(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))="'.date('Y').'"')
                ->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })
                ->where('pmt_method','Insurance')
                ->whereIn('pmt_type',['Payment','Refund'])
                ->where('deleted_at',Null)
                ->sum(DB::raw('total_paid'));

        $patientPaymentYr = PMTInfoV1::whereRaw('YEAR(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))="'.date('Y').'"')
                ->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })
                ->where('pmt_method','Patient')
                ->where('void_check',Null)
                ->where('deleted_at',Null)
                ->whereIn('pmt_type',['Payment','Credit Balance'])
                ->sum(DB::raw('pmt_amt'));

         $patientRefundYr = PMTInfoV1::whereRaw('YEAR(CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'"))="'.date('Y').'"')
                ->whereHas('claim', function($q)use($provider_id) {
                    $q->where('rendering_provider_id', $provider_id);
                })
                ->where('pmt_method','Patient')
                ->where('void_check',Null)
                ->where('deleted_at',Null)
                ->whereIn('pmt_type',['Refund'])
                ->sum(DB::raw('pmt_amt'));
        $result["pat_payment_year"] = $patientPaymentYr;
        $result["pat_refund_year"] = $patientRefundYr;
        //Year Collection = Year Payment - Year Refund
        $result["year"] = (($result["payment_year"] + $result["pat_payment_year"]) - $result["pat_refund_year"] );

        //Payment and Refund up to date
        $result["payment_tillDate"] = PMTClaimTXV1::where('pmt_method','Insurance')
                ->whereIn('pmt_type',['Payment'])
                ->where('deleted_at',Null)
                ->sum(DB::raw('total_paid'));

        $patientPaymentTillDate = PMTInfoV1::where('pmt_method','Patient')
                ->where('void_check',Null)
                ->where('deleted_at',Null)
                ->whereIn('pmt_type',['Payment','Credit Balance'])
                ->where('created_at','!=','0000-00-00 00:00:00')
                ->sum(DB::raw('pmt_amt'));

         $patientRefundTillDate = PMTInfoV1::where('pmt_method','Patient')
                ->where('void_check',Null)
                ->where('deleted_at',Null)
                ->whereIn('pmt_type',['Refund'])
                ->sum(DB::raw('pmt_amt'));
        $result["pat_payment_till"] = $patientPaymentTillDate;
        $result["pat_refund_till"] = $patientRefundTillDate;
        //Total Collection = Total Payment - Total Refund
        $result["till_date"] = (($result["payment_tillDate"] + $result["pat_payment_till"]) );
        return $result;
    }
     // Author: Baskar End
    #### Provider dashboard Ar days Calculation
    ####  Author: Thilagavathy
    public static function Providerardays()
    {
        $practice_timezone = Self::getPracticeTimeZone();
        $provider_id = Auth::user()->provider_access_id;
        try{
            $days = 0;
            // Get start date and end date for last 6 month
            $start_date = Carbon::now($practice_timezone)->toDateString();
            $end_date = Carbon::now()->subMonths(6)->toDateString();


            $charge_min_date = ClaimInfoV1::selectRaw('MIN(DATE(date_of_service)) as date')->whereRaw('date_of_service >= DATE(UTC_TIMESTAMP() - INTERVAL 6 MONTH)')->where('rendering_provider_id',$provider_id)->pluck('date')->first();
        // Get no of days between two dates
            $date1=date_create($charge_min_date);
            $date2=date_create($start_date);
            $diff=date_diff($date1,$date2);
            $tot_days = $diff->format("%a")+1;
            // Total charge for between two dates
            $total_charge = ClaimInfoV1::whereRaw('date_of_service <= DATE(UTC_TIMESTAMP())')->whereRaw('date_of_service >= DATE(UTC_TIMESTAMP() - INTERVAL 6 MONTH)')->where('rendering_provider_id',$provider_id)->sum('total_charge');
            $avg_charge = $total_charge / $tot_days;

            // Total Outstanding AR calculation
            $total_ar = Self::ProvidergetTotalOutstandingAr();
            $days = ($avg_charge == 0)?0:round($total_ar / $avg_charge);
            return $days;

        } catch (\Exception $e) {
            \Log::info("Error occured while billed and unbilled".$e->getMessage() );
        }

    }
     public static function ProvidergetTotalOutstandingAr() {
        $provider_id = Auth::user()->provider_access_id;
        $unbilled = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where('claim_info_v1.insurance_id', '!=', 0)->where('claim_info_v1.claim_submit_count', '=', 0)->where('claim_info_v1.rendering_provider_id',$provider_id)->sum(DB::raw('(claim_info_v1.total_charge)-(pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)'));
        $billed = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where(function($qry){
                        $qry->where(function($query){
                            $query->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count','>' ,0);
                        })->orWhere('claim_info_v1.insurance_id',0);
                    })->where('claim_info_v1.rendering_provider_id',$provider_id)
                ->sum(DB::raw('(claim_info_v1.total_charge)-(pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)'));
        return $unbilled+$billed;
    }
    #### End Provider dashboard AR days Calculation Thilagavathy
    ### Provider dashboard month year total collection End

    public static function getCurrentYearOutstanding() {
        $patient_ar = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                ->whereRaw("YEAR(claim_info_v1.created_at) = YEAR(CURDATE())")
                ->where("claim_info_v1.insurance_id", "0")
                ->sum(DB::raw('pmt_claim_fin_v1.patient_due + pmt_claim_fin_v1.insurance_due'));

        $insurance_ar = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                ->whereRaw("YEAR(claim_info_v1.created_at) = YEAR(CURDATE())")
                ->where("claim_info_v1.insurance_id", "!=", "0")
                ->sum(DB::raw('pmt_claim_fin_v1.insurance_due - pmt_claim_fin_v1.patient_paid'));

        return $patient_ar + $insurance_ar;
    }

    public static function getTotalChargesAmount() {
        return Helpers::priceFormat(ClaimInfoV1::sum('total_charge'), 'yes');
    }

    public static function getSubmittedClaimCount(){
        // Current Submitted Claims
        // Revision 1 - Ref: MR-2376
        // Revision 2 - Ref: MR-2586 - 30 July 2019
        return ClaimInfoV1::whereIn('status', ['Submitted'])->count();
    }

     public static function getHoldClaimCount() {
        return ClaimInfoV1::where('status', 'Hold')->count();
    }

    public static function getRejectionClaimCount() {
        return ClaimInfoV1::where('status', 'Rejection')->count();
    }

    public static function getUnbilledClaimAmount() {
        return Helpers::priceFormat(ClaimInfoV1::where('claim_submit_count', '<', 1)->whereIn('insurance_id', '!=' , 0)->sum('total_charge'), 'yes');
        return 0;
    }

    public static function getClaimTotalPatpayments() {
        return '$' . Helpers::priceFormat(PMTClaimFINV1::sum('patient_paid'), 'no');
    }

    public static function getClaimTotalInspayments() {
        return '$' . Helpers::priceFormat(PMTClaimFINV1::sum('insurance_paid'), 'no');
        return 0.0;
    }

    public static function getTotalClaimAdjustmentAmount() {
        return '$' . Helpers::priceFormat(PMTClaimFINV1::sum(DB::raw('patient_adj+insurance_adj+withheld')), 'no');
    }

    public static function getTotalClaimCollections() {
        return '$' . Helpers::priceFormat(PMTClaimFINV1::sum(DB::raw('insurance_paid + patient_paid')), 'no');
    }

    public static function getCurrentMonthClaimCollections() {
        return '$' . Helpers::priceFormat(PMTClaimFINV1::whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())')->sum(DB::raw('insurance_paid + patient_paid')), 'no');
    }

    public static function getCurrentMonthClaimInsPmts() {
        return '$' . Helpers::priceFormat(PMTInfoV1::where('pmt_method', 'Insurance')->whereIn('pmt_type', ['Payment','Refund'])->whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())')->sum(DB::raw('pmt_amt')), 'no');
    }

    public static function getCurrentMonthClaimPatPmts() {
        return '$' . Helpers::priceFormat(PMTInfoV1::where('pmt_method', 'Patient')->whereIn('pmt_type', ['Payment','Credit Balance'])->whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())')->sum(DB::raw('pmt_amt')), 'no');
    }

    public static function getCurrentMonthChargeAmount($practice_id ='') {
		// switch practice page added condition for provider login based showing values
        // Revision 1 - Ref: MR-2719 22 Aug 2019: Selva

        if($practice_id != ""){
            $practice_timezone = Helpers::getPracticeTimeZone($practice_id);
            $cur_start_date = Carbon::now($practice_timezone)->startOfMonth()->toDateString();
            $cur_end_date = Carbon::now($practice_timezone)->toDateString();
        }else{
            $practice_timezone = '';
            $cur_start_date = Carbon::now()->startOfMonth()->toDateString();
            $cur_end_date = Carbon::now()->toDateString();
        }
        if(Auth::check() && Auth::user()->isProvider())
            return Helpers::priceFormat(ClaimInfoV1::whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$cur_start_date."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$cur_end_date."'")->where('rendering_provider_id',Auth::user()->provider_access_id)->sum('total_charge'), 'yes');
        else
            return Helpers::priceFormat(ClaimInfoV1::whereRaw("DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) >= '".$cur_start_date."' and DATE(CONVERT_TZ(claim_info_v1.created_at,'UTC','".$practice_timezone."')) <= '".$cur_end_date."'")->sum('total_charge'), 'yes');
    }

    public static function getCurrentMonthClaimAdjAmt() {
        return '$' . Helpers::priceFormat(PMTClaimTXV1::whereRaw('MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())')->sum(DB::raw('total_withheld+total_writeoff')), 'no');
    }

    public static function getTotalOutstandingAr() {

        $unbilled = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where('claim_info_v1.insurance_id', '!=', 0)->where('claim_info_v1.claim_submit_count', '=', 0)->sum(DB::raw('(claim_info_v1.total_charge)-(pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)'));
        $billed = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->where(function($qry){
                        $qry->where(function($query){
                            $query->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count','>' ,0);
                        })->orWhere('claim_info_v1.insurance_id',0);
                    })
                ->sum(DB::raw('(claim_info_v1.total_charge)-(pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)'));
        return $unbilled+$billed;
    }

    public static function getOutstandingInsuranceAr() {
        /*$total_amount = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                        ->where("claim_info_v1.patient_id", "!=", "0")
                        ->where("claim_info_v1.insurance_id", "!=", "0")
                        ->sum(DB::raw('insurance_due - (patient_paid+patient_adj)'));*/

        $claim_over = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                    //->where("claim_info_v1.patient_id", "!=", "0")
                    ->where("claim_info_v1.insurance_id","!=", 0)
                    ->sum(DB::raw('(claim_info_v1.total_charge)-(pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)'));

        return $claim_over;
    }

    public static function getOutstandingPateientAr() {
        $total_amount = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                        ->where("claim_info_v1.insurance_id", 0)
                        ->sum(DB::raw('(claim_info_v1.total_charge)-(pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj+pmt_claim_fin_v1.insurance_paid + pmt_claim_fin_v1.insurance_adj+pmt_claim_fin_v1.withheld)'));
        return $total_amount;
    }


    /* Stats page functions start */

    // Payment table changes
     //First date of service Aging Days
    public static function getPatientClaimDateOfService($patient_id = 0){
        $get_agingDays = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
        ->where('claim_info_v1.patient_id', $patient_id)
        //->where('pmt_claim_fin_v1.patient_due', '<>', 0)
        //->where('pmt_claim_fin_v1.insurance_due', '<>', 0)
        ->whereRaw("((patient_due+insurance_due) <> '0' )")
        ->orderBy('date_of_service', 'ASC');
        $submitted_date = $get_agingDays->pluck('last_submited_date')->first();
        if($submitted_date == "0000-00-00") {
            $submitted_date = $get_agingDays->where('claim_info_v1.last_submited_date', '!=', '0000-00-00')->pluck('last_submited_date')->first();
        } else {
             $submitted_date = $get_agingDays->pluck('date_of_service')->first();
        }
        return $submitted_date;
    }

    // Patient+insurance due amount
    public static function getPatientDueInsuranceDue($patient_id = 0,$bal=''){
        return '';
    }

    public static function getPatientDuebal($patient_id = 0){
        $val = Patient::getPatienttabData($patient_id);
        return $val['patient_due'];
    }

	public static function getPatientDuebalSTMT($patient_id = 0){
        $val = Patient::getPatienttabDataSTMT($patient_id);
        return $val['patient_due'];
    }

    // Billed Amount
    public static function getPatientBilledAmount($patient_id = 0){
        $billed_amt = ClaimInfoV1::where('claim_info_v1.patient_id', $patient_id)
                ->where(function($qry){
                $qry->where(function($query){
                    $query->where('insurance_id','!=',0)
                            ->where('claim_submit_count','>' ,0); })
                                    ->orWhere('insurance_id',0); })
                ->sum('total_charge');
        return Helpers::priceFormat($billed_amt);
    }

    // Billed Amount
    public static function getPatientTotalInsuranceDue($patient_id = 0){
        return 0.0;
    }

    //  Unbilled Amount
    public static function getPatientUnBilledAmount($patient_id = 0){
        $unbilled_amt = ClaimInfoV1::where('claim_info_v1.patient_id', $patient_id)
                ->where('insurance_id', '!=', 0)
                ->where('claim_submit_count', 0)
                ->sum('total_charge');
        return Helpers::priceFormat($unbilled_amt);
    }

    // Last payment amount for patient.
    public static function getPatientLastPaymentAmount($patient_id = 0,$method = ''){
         $practice_timezone = Self::getPracticeTimeZone();
        if($method == 'Patient') {
            /* Patient Individual Statement last payment date calaculation */
            $resp = PMTInfoV1::where('patient_id', $patient_id)->where('pmt_method', 'Patient')
            //->where('pmt_type','Payment')
            ->whereIn('pmt_type', ['Payment', 'Credit Balance'])
            ->orderBy('created_at', 'DESC')->select("pmt_amt as total_paid",DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->first();
            return $resp;
            /*
            $resp = PMTClaimTXV1::where('patient_id', $patient_id)->where('pmt_method', 'Patient')->orderBy('created_at', 'DESC')->select("total_paid",'created_at')->first();
            return $resp;
            */
        } else {
            $resp = PMTClaimTXV1::where('patient_id', $patient_id)->whereIn('pmt_method', ['Patient', 'Insurance'])->orderBy('created_at', 'DESC')->select("total_paid")->first();
        }
        if (!empty($resp)){
          $resp = $resp->toArray();
          return $resp['total_paid'];
        }
        return $resp['total_paid'] = 0.0;
    }
    // Last payment Date for patient.
    public static function getPatientLastPaymentDate($patient_id = 0){
        //$resp = PMTClaimTXV1::where('patient_id', $patient_id)->whereIn('pmt_method', ['Patient', 'Insurance'])->orderBy('created_at', 'DESC')->select("created_at")->first();
        $practice_timezone = Self::getPracticeTimeZone();
        $resp = PMTInfoV1::where('patient_id', $patient_id)->whereIn('pmt_method', ['Patient', 'Insurance'])->orderBy('created_at', 'DESC')->select(DB::raw('CONVERT_TZ(created_at,"UTC","'.$practice_timezone.'") as created_at'))->first();
        if (!empty($resp)){
          $resp = $resp->toArray();
          return $resp['created_at'];
        }
        return '';
    }

    // Patient Balance
    public static function getPatientBalance($patient_id = 0, $type='', $format = 1){
		//$claimID = ClaimInfoV1::where('patient_id',$patient_id)->where('status','!=','Hold')->pluck('id')->toArray();
		//->whereIn('claim_id',$claimID)
        $pat_payment = PMTClaimFINV1::where('patient_id', $patient_id)->sum('patient_due');
        if($format == 1)
            return Helpers::priceFormat($pat_payment);
        else
            return $pat_payment;
    }

    public static function getTotalUnappliedPmt(){
        $unapplied_amt = PMTInfoV1::whereIn('pmt_type', ['Payment', 'Credit Balance','Refund'])
                         ->whereIn('source', ['posting', 'addwallet', 'scheduler', 'charge'])
                         ->where('void_check', NULL)
                         ->whereNull('deleted_at')
                         ->sum(DB::raw('(pmt_info_v1.pmt_amt - pmt_info_v1.amt_used)'));

        return '$' . Helpers::priceFormat($unapplied_amt, 'no');
    }

    // Unapplied payments
    public static function getUnappliedPmt($patient_id = 0){
        return '$' . Helpers::priceFormat(PMTInfoV1::where('patient_id', $patient_id)->sum('balance'), 'no');
    }

    // Claim copay, coins, dedutable amount fetching
    public static function getClaimPaymentData($claim_id = 0){
        $value = PMTClaimCPTFINV1::where('claim_id', $claim_id)->select(
            DB::raw("SUM(co_ins) as coinsurance"),DB::raw("SUM(co_pay) as copay"),DB::raw("SUM(deductable) as deductable"),DB::raw("SUM(with_held) as withheld"))->first();
        return $value;
    }

    public static function getClaimPatPaidAmt($claim_id = 0){
        $pat_payment = PMTClaimFINV1::where('claim_id', $claim_id)->sum('patient_paid');
        return Helpers::priceFormat($pat_payment);
    }

    /* Claimwise transaction amount calculation  */
    public static function getClaimwiseAmt($claim_id = 0,$patient_id= 0) {
        $allowed =[];
        if(!is_numeric($claim_id))
            $claim_id = Helpers::getEncodeAndDecodeOfId($claim_id, 'decode');
        $allowed = PMTClaimFINV1::where('claim_id', $claim_id)
                 ->select( DB::raw("SUM(total_allowed) as total_allowed"), DB::raw("SUM(patient_paid) as patient_paid"), DB::raw("SUM(insurance_paid) as insurance_paid"), DB::raw("SUM(patient_adj) as patient_adj"), DB::raw("SUM(insurance_adj) as insurance_adj"), DB::raw("SUM(withheld) as withheld"))->first();

        //$allowed['total_allowed'] = PMTClaimFINV1::where('claim_id', $claim_id)->sum('total_allowed');
        //$allowed['patient_paid'] =  PMTClaimFINV1::where('claim_id', $claim_id)->sum('patient_paid');
        //$allowed['insurance_paid'] =  PMTClaimFINV1::where('claim_id', $claim_id)->sum('insurance_paid');
        $allowed['total_paid'] = @$allowed['insurance_paid'] + @$allowed['patient_paid'];
        //$allowed['patient_adj'] = PMTClaimFINV1::where('claim_id', $claim_id)->sum('patient_adj');
        //$allowed['insurance_adj'] = PMTClaimFINV1::where('claim_id', $claim_id)->sum('insurance_adj');
        //$allowed['withheld'] = PMTClaimFINV1::where('claim_id', $claim_id)->sum('withheld');
        $allowed['total_adj'] = @$allowed['withheld'] + @$allowed['insurance_adj'] + @$allowed['patient_adj'];
        return $allowed;
    }

    public static function checkForPaymnetRequirement($claimId, $claim_status, $type)
    {
       //$claim_status = ClaimInfoV1::where('id', $claimId)->pluck("status");
        if($claim_status == "Hold" && $type == "Insurance"){
            return "0";
        } else if($claim_status == "Hold" && $type == "Patient"){

            $claim_status = ClaimInfoV1::whereHas("claim_unit_details", function($query){
                $query->where(function($query){
                    $query->where('dos_from', '!=', '')
                        ->where('dos_to', '!=', '')
                        ->where('cpt_code', '!=', '')
                        ->where('cpt_icd_map_key', '!=', '')
                        ->where('charge', '!=', '0.00');
                     });
            })->where('id', $claimId)
                ->where(function($query){
                    $query->where('rendering_provider_id', '!=', '')
                          ->where('billing_provider_id', '!=', '')
                          ->where('facility_id', '!=', '')
                          ->where('pos_id', '!=', '')
                          ->where('total_charge', '!=', '0.00')
                          ->where('icd_codes', '!=', '');
                 })->count();
            return $claim_status;
        } else {
            return "1";
        }
    }

    public static  function GetLabelFields($pmt_mode = "")
    {
        if($pmt_mode == "Check") {
            $data['label_no'] = "Check No";
            $data['label_date'] = "Check Date";
        } else if($pmt_mode == "EFT") {
            $data['label_no'] = "EFT No";
            $data['label_date'] = "EFT Date";
        } else if($pmt_mode == "Money Order") {
            $data['label_no'] = "MO No";
            $data['label_date'] = "MO Date";
        } elseif($pmt_mode == "Credit") {
            $data['label_no'] = "CC No";
            $data['label_date'] = "CC Date";
        } else {
            $data['label_no'] = "Check No";
            $data['label_date'] = "Check Date";
        }
        return $data;
    }

    public static function getProviderlist() {
        $provider = DB::table('providers as p')->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')->selectRaw('CONCAT(p.provider_name," ",pd.degree_name) as concatname, p.id')->where('p.status', 'Active')->where('p.deleted_at', NULL)->orderBy('provider_name', 'ASC')->pluck('concatname', 'p.id')->all();
        return $provider;
    }
    public static function getRenderingProviderlist() {
        $provider = DB::table('providers as p')->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')->selectRaw('CONCAT(p.provider_name," ",pd.degree_name) as concatname, p.id')->where('p.status', 'Active')->where('p.deleted_at', NULL)->where('p.provider_types_id','=', 1)->orderBy('provider_name', 'ASC')->pluck('concatname', 'p.id')->all();
        return $provider;
    }
    public static function getBillingProviderlist() {
        $provider = DB::table('providers as p')->join('provider_degrees as pd', 'pd.id', '=', 'p.provider_degrees_id')->selectRaw('CONCAT(p.provider_name," ",pd.degree_name) as concatname, p.id')->where('p.status', 'Active')->where('p.deleted_at', NULL)->where('p.provider_types_id','=', 5)->orderBy('provider_name', 'ASC')->pluck('concatname', 'p.id')->all();
        return $provider;
    }

    public static function getFacilityLists(){
        $facilities = Facility::selectRaw('CONCAT(short_name,"-",facility_name) as facility_name, id')->orderBy('facility_name', 'asc')->where('status', 'Active')->pluck('facility_name', 'id')->all();
        return $facilities;
    }

    public static function getInsurancestatus($ins_id,$patient_id){

		$claim_status = ClaimInfoV1::with('claim_txt_info')->whereHas('claim_txt_info', function($query) use($ins_id){ $query->whereIn('transaction_type',["Submitted","Submitted Paper"])->where('responsibility',$ins_id); })->where('patient_id',$patient_id)->count();
        //$claim_status = DB::table('claim_info_v1 as c')->where('patient_insurance_id',$ins_id)->where('claim_submit_count','>',0)->get();
        if($claim_status != 0){
            return true;
        }else{
            return false;
        }
    }

    public static function getPatientNote($id) {
        //Initialize Fields
        $return_val = '';
        $deceased_date_val = '';
        $statement = '';
        //Check for Patient ID and if found gather required values for Alert
        if (isset($id) && !is_numeric($id)) {
            $id = Helpers::getEncodeAndDecodeOfId($id, 'decode');
            $pat_det = Patient::where('id', $id)->select('deceased_date', 'statements')->first();
            if (!empty($pat_det)) {
                //Get Desceased Date
                $deceased_date = $pat_det->deceased_date;
                if ($deceased_date != '' && $deceased_date != '0000-00-00') {
                    $deceased_date_val = date('m/d/Y', strtotime($deceased_date));
                }
                //Get Patient Statement Status
                $statement = $pat_det->statements;
            }
        }
        //Create Alert Message using return_val
        //Add patient note if exists
        $patNote = PatientNote::where('patient_notes_type', 'alert_notes')->where('status','Active')->where('notes_type_id', $id)->orderBy('id', 'desc')->first();
        //if (PatientNote::where('patient_notes_type', 'alert_notes')->where('status','Active')->where('notes_type_id', $id)->count()) {
        if(!empty($patNote)) {
            //$return_val = PatientNote::where('patient_notes_type', 'alert_notes')->where('status','Active')->where('notes_type_id', $id)->orderBy('id', 'desc')->first()->content;
            $return_val = $patNote->content;
            $return_val =  $return_val . "<br/>";
        }
        //Add deceased date if exists
        if ($deceased_date_val != '') {
            $return_val =  $return_val . "Deceased date: " . $deceased_date_val ."<br/>";
        }
        //Add patient statements if exists and if statement is not yes
        if($statement != '' && $statement != 'Yes'){
            $return_val =  $return_val . "Statements: " . $statement ."<br/>";
        }
        return $return_val;
    }

    public static function getPracticeNames($ids,$user_id){
        if(!empty($ids) && $ids != ''){
            $ids = explode(',',$ids);
            $practice_name = Practice::whereIn('id',$ids)->pluck('practice_name')->all();
            $practice_name = implode(', ',$practice_name);
            return $practice_name;
        }else{
            $practiceSetInfo = Setpracticeforusers::whereIn('user_id',(array)$user_id)->pluck('practice_id')->all();
            $practice_name = Practice::whereIn('id',$practiceSetInfo)->pluck('practice_name')->all();
            $practice_name = implode(', ',$practice_name);
            return $practice_name;
        }
    }

    /* Getting all category for document Module */
    public static function getDocumentCategory(){
        $category_list = DB::table('document_categories')->where(function($query){ $query->where('module_name', '=', 'patients');})->orderBy('category_value','ASC')->pluck('category_value', 'category_key')->all();
        return $category_list;
    }

    public static function daysSinceCreated($date){
        $date1=date_create($date);
        $date2=date_create(date('Y-m-d h:i:s'));
        $diff=date_diff($date1,$date2);
        return $diff->format("%a");
    }

    public static function daysSinceCreatedCount($date){
        $date1=date_create($date);
        $date2=date_create(date('Y-m-d'));
        $diff=date_diff($date1,$date2);
        return $diff->format("%a");
    }

    public static function getSearchUserDate($page_name, $url) {
        $searchField = SearchFields::where('page_name', $page_name)->select('id', 'page_name')->get()->first();
        if(empty($searchField)) {
            return '';
        }

        $searcUserData = SearchUserData::where('search_fields_id', $searchField->id)->orderBy('updated_at', 'desc')->select('id','search_fields_id', 'search_fields_data', 'more_field_data', 'updated_at')->take(2)->get();

        $date_list = '';
        $final_url = url($url);
        $count = 0;
        $practice_dbid = isset(Session::all()['practice_dbid']) ? Session::all()['practice_dbid'] : 0;
        foreach ($searcUserData as $list) {

            $date_list .= " <a data-url='" . $final_url . "' data-search-id='" . $list->id . "' data-page-name='" . $searchField->page_name . "' class='url_search_data cur-pointer someelem font600' data-id='search" . $list->id . "' id='someelemsearch" . $list->id . "'  href='#contact-info'>" . Helpers::timezone($list->updated_at,'m/d/y',Session::all()['practice_dbid']);
            if ($count == 0 && count($searcUserData) == 2)
                $date_list .= " <span class='med-gray'>| </span>";
            $date_list .= "</a>";
            // start tool tip for search filter
            $date_list .="<div class='js-tooltip_search" . $list->id . "' style='display:none;'><span style='border-bottom:1px dashed #e0d775; display:block; padding-bottom: 2px; margin-bottom: 5px;'>" .  Helpers::timezone($list->updated_at,'m/d/y', Session::all()['practice_dbid']) . "</span>";
            $count++;
            $report_tooltip = json_decode($list->search_fields_data, true);
            foreach ($report_tooltip as $items) {
                if ($items['value'] != '') {
                    $label_name = rtrim(ucwords(str_replace('_', ' ', $items['label_name'])),"[]");
                    if($label_name == "Patient Type") { $label_name = "Patient Type"; }
                    else if($label_name == "Rendering Provider Id") {$label_name = "Rendering Provider";}
                    else if($label_name == "Rendering") {$label_name = "Rendering";}
                    else if($label_name == "Billing Provider Id") {$label_name = "Billing Provider";}
                    else if($label_name == "Facility Id") {$label_name = "Facility";}
                    else if($label_name == "Insurance Charge") {$label_name = "Payer";}
                    else if($label_name == "Insurance Id") {$label_name =($page_name == 'charges_listing') ? "Payer" : "Insurance";}
                    else if($label_name == "Include Cpt Option") {$label_name = "Include";}
                    else if($label_name == "Dos") {$label_name = "DOS Date";}
                    else if($label_name == "Rendering Id") {$label_name = "Rendering Provider";}
                    else if($label_name == "Billing Id") {$label_name = "Billing Provider";}
                    else if($label_name == "Created By") {$label_name = "User";}
                    else if($label_name == "Eligible") {$label_name = "Eligibility";}
                    else if($label_name == "Responsibility") {$label_name = "Insurance";}
                    else if($label_name == "Exclude Zero Ar") {$label_name = "$0 Line Item";}
                    else if($page_name == "denial_analysis" && $label_name == "Created At" ) { $label_name = "Denied Date"; }
                    else if($label_name == "Select Date Of Service" ) { $label_name = "DOS"; }
                    else if($label_name == "Select Transaction Date" ) { $label_name = "Transaction Date"; }
                    else if($label_name == "Cpt Type" && $searchField->page_name == 'procedurereport') {
                           $label_name = "CPT/HCPCS Type";
                    }

                    $date_list .="<span style='display:block; padding: 2px 0px; color:#98924d;'><em>" . $label_name . ": </em>";
                    if ($label_name == 'Facility') {
                        $search_name = Facility::select('facility_name');
                        if (strpos($items['value'], ',') !== false) {
                            $facility_names = $search_name->whereIn('id', explode(',', $items['value']))->get();
                            foreach ($facility_names as $name) {
                                $facility_ids[] = $name['facility_name'];
                            }
                            $date_list .= implode(", ", array_unique($facility_ids));
                        } else {
                            $facility_names = $search_name->where('id', explode(',', $items['value']))->get();
                            foreach($facility_names as $facility_na){
                                $date_list .= $facility_na['facility_name'];
                            }
                        }
                    } else if ($label_name == 'Billing Provider' || $label_name == 'Billing') {
                        $peoviders_id = explode(',', $items['value']);
                        foreach ($peoviders_id as $id) {
                            $billing_ids[] = App\Models\Provider::getProviderShortName($id);
                        }
                        $date_list .= implode(", ", array_unique($billing_ids));
                    } else if ($label_name == 'Rendering Provider' || $label_name == 'Rendering') {
                        $renders_id = explode(',', $items['value']);
                        foreach ($renders_id as $id) {
                            $renders_ids[] = App\Models\Provider::getProviderShortName($id);
                        }
                        $date_list .= implode(", ", array_unique($renders_ids));
                    } else if ($label_name == 'Refering Provider' || $label_name == 'Refering') {
                        $refering_id = explode(',', $items['value']);
                        foreach ($refering_id as $id) {
                            $refering_ids[] = App\Models\Provider::getProviderShortName($id);
                        }
                        $date_list .= implode(", ", array_unique($refering_ids));
                    } else if ($label_name == 'Insurance' || ($page_name == "charges_listing" && $label_name == "Payer")) {
                        $value_names = [];
                       $search_name = Insurance::select('short_name');
                       if (strpos($items['value'], ',') !== false) {
                           if(in_array(0, explode(',', $items['value'])))
                              $value_names[] = 'Patient';
                           $insurance_names = $search_name->whereIn('id', explode(',', $items['value']))->get();
                           foreach ($insurance_names as $name) {
                               $value_names[] = $name['short_name'];
                           }

                           $date_list .= implode(", ", array_unique($value_names));
                       } else {
                           if($items['value'] != 0) {
                               $insurance_names = $search_name->where('id',  $items['value'])->get();
                                foreach($insurance_names as $insurance){
                                  $date_list .= $insurance['short_name'];
                               }
                           } else {
                               $date_list .= "Patient";
                           }
                       }
                    } else if($label_name == 'Include'){
                        $show_name = explode(',',$items['value']);
                        foreach($show_name as $shows){
                            if($shows == 'include_cpt_description'){
                                $show_fulname = "CPT Description";
                            } else if($shows == 'include_icd'){
                                $show_fulname = "ICD";
                            } else {
                                $show_fulname = "Modifiers";
                            }
                            $test[] = $show_fulname;
                        }
                        $date_list .= implode(", ", array_unique($test));
                    } else if($label_name == 'Payer' && $page_name != "charges_listing"){
                        if($items['value'] == "self" || $items['value'] == "all" || $items['value'] =="insurance"){
                            if($items['value'] == "insurance"){
                                $date_list .= "All Insurance";
                            } else {
                                $date_list .= ucwords($items['value']);
                            }
                        } else {
                            $search_name = Insurance::select('short_name');
                            $insurance_names = $search_name->where('id',  $items['value'])->get();
                            foreach($insurance_names as $insurance){
                               $date_list .= $insurance['short_name'];
                            }
                        }
                    } else if($label_name == 'Provider Type'){
                        if($items['value'] == 0){
                             $name = 'All';
                        } else if($items['value'] == 1){
                            $name = 'Rendering';
                        } else {
                            $name = 'Billing';
                        }
                        $date_list .= $name;
                    } else if($label_name == 'User'){
                        $User_name =  Users::whereIn('id', explode(',', $items['value']))->where('status', 'Active')->pluck('name', 'id')->all();
                        $User_name = implode(", ", array_unique($User_name));
                        $date_list .= $User_name;
                    }  else if($label_name == "$0 Line Item") {
                         $date_list .= ($items['value'] == 'Include') ? 'Contains $0 Line Item' : 'Remove $0 Line Item';
                    } else if($label_name == "Choose Date" && $searchField->page_name == 'procedurereport') {
                        $date_list .= ucwords(str_replace('_', ' ', $items['value']));
                    }else if($label_name == "CPT/HCPCS Type" && $searchField->page_name == 'procedurereport') {
                        $cpt_type = ucwords(str_replace('_', ' ', $items['value']));
                        if($cpt_type == 'Cpt Code'){
                            $cpt_type_name = 'CPT/HCPCS Code';
                        }else if($cpt_type == 'Custom Type'){
                            $cpt_type_name = 'Custom Range';
                        }else{
                            $cpt_type_name = 'All';
                        }
                        $date_list .= $cpt_type_name;
                    }else if($label_name == "Sort By" && $searchField->page_name == 'procedurereport') {
                        $sort_by = ucwords(str_replace('_', ' ', $items['value']));
                        if($sort_by == 'CPT'){
                            $cpt_type_name = 'CPT/HCPCS';
                        }else{
                            $cpt_type_name = $sort_by;
                        }
                        $date_list .= $cpt_type_name;
                    }else {
                        $date_list .= str_replace(',', ', ', $items['value']);
                    }
                }
                $date_list .="<br></span>";
            }
            // start tool tip for search filter
            $date_list .="</div>";
        }
        return $date_list;
    }


	/* Static function  */
	 public static function getClaimStats($type, $date_range='All', $patient_id=0){
        // Billed, Unbilled,
        $resp = [];
        $type = strtolower($type);

        /*
        Unbilled Charges:       Claim Submit Count = 0 and Status = Ready
        Billed Charges:         Claim Submit Count = 0 and Status = Ready   Or      Status = Patient, Paid, Submitted, Denied
        Rejections:             Status = Rejection
        Charges On Hold:        Status = Hold
        */

        $claims = ClaimInfoV1::select(DB::raw('sum(total_charge) as `total_amt`'),DB::raw('COUNT(id) as `total_charges`'));
        switch ($type) {

            case 'billed':
                $claims->where(function($qry){
                        $qry->where(function($query){
                            $query->where('insurance_id','!=',0)->where('claim_submit_count','>' ,0);
                        })->orWhere('insurance_id',0);
                    });
                break;

            case 'unbilled':
                $claims = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')->select(DB::raw('sum(claim_info_v1.total_charge) - SUM(insurance_paid+patient_paid+withheld+patient_adj+insurance_adj) as `total_amt`'),DB::raw('COUNT(claim_info_v1.id) as `total_charges`'));
                $claims->where('claim_info_v1.insurance_id','!=',0)->where('claim_info_v1.claim_submit_count', 0);
                break;

            case 'hold':
                $claims->whereIn('status', ['Hold']);
                break;

            case 'rejected':
                $claims->whereIn('status', ['Rejection']);
                break;

            case 'all':
                $billed = SELF::getClaimStats('billed',$date_range);
                $unbilled = SELF::getClaimStats('unbilled',$date_range);
                $hold = SELF::getClaimStats('hold',$date_range);
                $rejected = SELF::getClaimStats('rejected',$date_range);
                return array_merge_recursive($billed, $unbilled, $hold, $rejected);
                break;
        }

        // Date range handled date fo service field
        if(trim($date_range) != '' && trim(strtolower($date_range)) != 'all') {
            $date = explode('-',trim($date_range));
            $from = date("Y-m-d", strtotime(@$date[0]));
            if($from == '1970-01-01'){
                $from = '0000-00-00';
            }
            $to = date("Y-m-d", strtotime(@$date[1]));
            $claims->where(function($query) use ($from, $to){
                $query->where('claim_info_v1.created_at', '>=', $from)->where('claim_info_v1.created_at', '<=', $to);
            });
        }
        if($patient_id != 0) {
            $claims->where('patient_id', $patient_id);
        }

        $rec = $claims->first();

        if(!empty($rec)) {
            $total_amount = (!empty($rec['total_amt']) )? $rec['total_amt'] : 0;
            $resp[$type]['total_amount'] =	$total_amount;
            $resp[$type]['total_charges'] = isset($rec['total_charges']) ? $rec['total_charges'] : 0;
        } else {
            $resp[$type]['total_amount'] = $resp[$type]['total_charges'] = 0;
        }
        return $resp;
    }
	/* 	Getting BackDate Status
		Created at : 23:Aug:18
		Author : Selvakumar V
	*/
	public static function getBackDate(){
		if (Session::get('practice_dbid')){
            $practice_id = Session::get('practice_dbid');
			$practice_bkdata = Practice::where('id',$practice_id)->pluck('backDate')->first();
			return $practice_bkdata;
		} else {
			return 'No';
		}
	}

    /*  For Send email
    *   $deta = array('name', 'email', 'cc_mail','subject', 'msg', 'attachment')
    *   $template
    */
    public static function sendMail($deta, $template='general') {

        set_time_limit(0);
        try {
            $url_info = parse_url(url('/'));
            if (isset($url_info['host']) ) {    // Host restriction removed due to handle pms.medcubics.com, avec.medcubics.com
                $tpl = (isset($template) && $template != '' ) ? 'emails.'.$template : 'emails.general';

                if(isset($deta['email']) && $deta['email'] != '') {
                    $to_email   = isset($deta['email']) ? $deta['email'] : "";
                    $to_name    = isset($deta['name']) ? $deta['name'] : $to_email;
                    $cc         = (isset($deta['cc_email'])) ? $deta['cc_email'] : "";
                    $sub        = isset($deta['subject']) ? $deta['subject'] : "Medcubics";
                    $msg        = isset($deta['msg']) ? $deta['msg'] : "";
                    $attachment = (isset($deta['attachment']) && !empty($deta['attachment']) )? $deta['attachment'] : "";
                    // Handling mail send and copy to admin mail
                    \Mail::send($tpl, ['msg' => $msg], function($message) use ($to_name, $to_email, $sub, $attachment, $cc) {
                        // $message->from('yourEmail@domain.com', 'From name');
                        // $message->cc('bar@example.com')->bcc('bar@example.com');
                        $message->to($to_email, $to_name)->subject($sub);

                        // If mail sent to admin no needs to have a copy
                        if($to_email != 'admin@medcubics.com')
                            //$message->bcc('admin@medcubics.com');

                        // Include attachment if provided
                        if($attachment != '')
                            $message->attach($attachment);

                        // Handle CC email address if provided.
                        if($cc != ''){
                            // Handle comma separated emails in cc
                            $cc_mails = explode(",", $cc);
                            if(!empty($cc_mails)){
                                foreach ($cc_mails as $cc_id) {
                                    if($cc_id != '') $message->cc($cc_id);
                                }
                            }
                        }
                    });
    				\Log::info('Mail Send Successfully to user '.$to_email." Subj:". $sub);
                    return true;
                } else {
                    \Log::info("Invalid Send E-Mail call. receiver id not provided");
                }
            }
        } catch (\Exception $e) {
            \Log::info("Error occured while send mail. Message".$e->getMessage() );
            return true;
        }
    }

    /*
    * To check whether given insurance is available in multicategory
    */
    public static function checkIsMultiInsurance($insurance_id, $patient_id){
        $pat_ins_cnt = PatientInsurance::where('patient_id', $patient_id)->where('insurance_id', $insurance_id)
                            ->whereIn('category', ['Primary', 'Secondary', 'Tertiary'])
                            ->count();

        if(!empty($pat_ins_cnt) && $pat_ins_cnt > 1){
            return 1;
        }
        return 0;
    }

    /*
    * To check whether given insurance is available in other category
    */
    public static function checkIsPatientOtherInsurance($insurance_id, $patient_id){
        $pat_ins_cnt = PatientInsurance::where('patient_id', $patient_id)->where('insurance_id', $insurance_id)
                            ->where('category', 'Others')
                            ->count();

        if(!empty($pat_ins_cnt) && $pat_ins_cnt > 0){
            return 1;
        }
        return 0;
    }


    // Billed and Unbilled condition By baskar
    public static function BilledUnbilled($charge)
    {
        $response = [];
        try{
            if( (!empty($charge->insurance_id) && $charge->claim_submit_count == 0) ) {
                    //Unbilled
                    $response['unbilled'] =$charge->total_charge;
                    $response['billed'] = 0;
                }else {
                    //Billed
                    $response['unbilled'] = 0;
                    $response['billed'] = $charge->total_charge;
                }
        } catch (\Exception $e) {
            \Log::info("Error occured while billed and unbilled".$e->getMessage() );
        }
        return $response;
    }

    // AR DAYS
    // Author: Baskar
    public static function ardays($patient_id='')
    {
        $practice_timezone = Self::getPracticeTimeZone();
        try{
            $days = 0;
            // Get start date and end date for last 6 month
            $start_date = Carbon::now($practice_timezone)->toDateString();
            $end_date = Carbon::now($practice_timezone)->subMonths(6)->toDateString();

            if(!is_numeric($patient_id))
                $patient_id = self::getEncodeAndDecodeOfId($patient_id, 'decode');

            if($patient_id)
                $charge_min_date = ClaimInfoV1::selectRaw('MIN(DATE(date_of_service)) as date')->where('patient_id',$patient_id)->whereRaw('date_of_service >= MONTH(UTC_TIMESTAMP() - INTERVAL 6 MONTH)')->pluck('date')->first();
            else
                $charge_min_date = ClaimInfoV1::selectRaw('MIN(DATE(date_of_service)) as date')->whereRaw('date_of_service >= DATE(UTC_TIMESTAMP() - INTERVAL 6 MONTH)')->pluck('date')->first();

            // Get no of days between two dates
                $date1=date_create($charge_min_date);
                $date2=date_create($start_date);
                $diff=date_diff($date1,$date2);
                $tot_days = $diff->format("%a")+1;

            // Total charge for between two dates
            if($patient_id)
                $total_charge = ClaimInfoV1::whereRaw('date_of_service <= DATE(UTC_TIMESTAMP())')->where('patient_id',$patient_id)->whereRaw('date_of_service >= DATE(UTC_TIMESTAMP() - INTERVAL 6 MONTH)')->sum('total_charge');
            else
                $total_charge = ClaimInfoV1::whereRaw('date_of_service <= DATE(UTC_TIMESTAMP())')->whereRaw('date_of_service >= DATE(UTC_TIMESTAMP() - INTERVAL 6 MONTH)')->sum('total_charge');

            $avg_charge = $total_charge / $tot_days;

            // Total Outstanding AR calculation
            if($patient_id){
                $patient_ar = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                    ->where("claim_info_v1.insurance_id", "0")
                    ->where('claim_info_v1.patient_id',$patient_id)
                    ->sum(DB::raw('pmt_claim_fin_v1.patient_due + pmt_claim_fin_v1.insurance_due'));

                $insurance_ar = ClaimInfoV1::join('pmt_claim_fin_v1', 'claim_info_v1.id', '=', 'pmt_claim_fin_v1.claim_id')
                    ->where("claim_info_v1.insurance_id", "!=", "0")
                    ->where('claim_info_v1.patient_id',$patient_id)
                    ->sum(DB::raw('pmt_claim_fin_v1.insurance_due - (pmt_claim_fin_v1.patient_paid + pmt_claim_fin_v1.patient_adj)'));

                $total_ar = $insurance_ar+$patient_ar;
            }
            else{
                $total_ar = Self::getTotalOutstandingAr();
            }

            $days = ($avg_charge == 0)?0:round($total_ar / $avg_charge);
            return $days;

        } catch (\Exception $e) {
            \Log::info("Error occured while billed and unbilled".$e->getMessage() );
            return 0;
        }

    }

	/*
	*
	* This function used for getting practice created year to to date
	* Author : seVakumar
	* Date : 10/04/2018
	*/

    public static function getPracticeYearList($option = ''){
        $data = $dataOption = [];
        $practiceCreatedDate = Practice::where('id',Session::get('practice_dbid'))->pluck('created_at')->first();
        if(date('Y',strtotime($practiceCreatedDate)) == date("Y")){
            $data[date('Y',strtotime($practiceCreatedDate))] = date('Y',strtotime($practiceCreatedDate));
            $dataOption['current_year'] = "Current Year";
        }else{
            $dataOption['current_year'] = "Current Year";
            $dataOption['previous_year'] = 'Previous Year';
            $dataOption['select_year'] = 'Select Year';
            // for getting created at year for year end report
            $datas = ClaimInfoV1::select(DB::raw("YEAR(created_at) as year"))->whereNull('deleted_at')->orderBy('year','asc')->first();
            $data = range(date("Y"), $datas['year']);
            $data = array_combine($data,$data);
        }
        if(!empty($option))
            return $dataOption;
        if(empty($option))
            return $data;
    }

	public static function downloadExportFile($fileName){
		set_time_limit(0);

        $exists = Storage::disk('local')->exists($fileName);
        if($exists){
    		$fs = Storage::disk('local')->getDriver();
    		$metaData = $fs->getMetadata($fileName);
    		$handle = $fs->readStream($fileName);
    		$tempName = explode('X0X',$metaData['path']);
    		$downloadFileName = (!isset($tempName[1])) ? $metaData['path'] : $tempName[1];
    		header('Pragma: public');
    		header('Expires: 0');
    		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    		header('Cache-Control: private', false);
    		header('Content-Transfer-Encoding: binary');
    		header('Content-Disposition: attachment; filename="' . $downloadFileName . '";');
    		//header('Content-Disposition: attachment; filename="downloaded.pdf"');
    		header('Content-Type: ' . $metaData['type']);

    		$chunkSize = 1024 * 1024;

    		while (!feof($handle)) {
    			$buffer = fread($handle, $chunkSize);
    			echo $buffer;
    			ob_flush();
    			flush();
    		}

    		fclose($handle);
    		exit;
        } else {
            \Log::info("File Not Exists: ".$fileName);
            echo "<h1>File Not Found!!!</h1>";
            exit;
        }
	}


	public static  function getReportNotification(){
		$ExportInfo = ReportExport::where('practice_id',Session::get('practice_dbid'))->where('created_by',Auth::user()->id)->where('deleted_at', '=', '0000-00-00 00:00:00');

        $pendingExports = clone $ExportInfo;
        $ExportInfo = $ExportInfo->skip(0)->take(5)->orderBy('id','desc')->get()->toArray();

		$count = 0;
        $pendingExports = $pendingExports->whereIn('status', ['Pending','Inprocess'])->count();
        /*
		foreach($ExportInfo as $list){
			if($list['status'] == 'Pending' || $list['status'] == 'Inprocess')
				$count++;
		}
        */
		$data['ExportInfo'] = $ExportInfo;
		$data['pendingExportCount'] = $pendingExports;
		return $data;
    }

    public static  function getReportNotificationCount(){
        $pendingExports = ReportExport::where('practice_id',Session::get('practice_dbid'))->where('created_by',Auth::user()->id)->where('deleted_at', '=', '0000-00-00 00:00:00');
        $count = 0;
        $pendingExports = $pendingExports->whereIn('status', ['Pending','Inprocess'])->count();
        return $pendingExports;
    }

    public static function getCustomername() {
        $empty = array('' => 'All' );
        $custArr = Customer::pluck('customer_name', 'id')->all();
        $customers = $empty + $custArr;
		return $customers;
	}

    /*
    *
    * This function used for filter <=,>= and =
    * Author : Baskar
    * Date : 09/01/2019
    */
    public static  function paymentFilter($data){
        if(!empty($data)){
            if (preg_match('/</', $data)){
                $exp = explode('<',$data);
                $result['condition'] = '<=';
                $result['val'] = $exp[1];
            }elseif (preg_match('/>/', $data)){
                $exp = explode('>',$data);
                $result['condition'] = '>=';
                $result['val'] = $exp[1];
            }else{
                $result['condition'] = '=';
                $result['val'] = $data;
            }
            return $result;
        }
    }

    public static function getProcedureCategory() {
        $procedure_category = ProcedureCategory::orderBy('created_at', 'desc')->where('status', 'Active')->pluck('procedure_category','id')->all();
        return $procedure_category;
    }

    /*
    * To check whether date between two dates
    */
    public function check_in_range($start_date, $end_date, $date_from_user) {
        // Convert to timestamp
        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        $user_ts = strtotime($date_from_user);

        // Check that user date is between start & end
        return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
    }
	/* claim filter changed practice created date to till now setting on created at filed in filter section */
	public static function getPracticeCreatedDate(){

		// Claims: Created Date: It should be updated from the Claim Created Date(with the Back Date options) to till now
        //Revision 1 - Ref: MR-2494 12 Aug 2019: Selva

		$createdDate = ClaimInfoV1::whereNull('deleted_at')->orderBy('created_at','asc')->skip(0)->limit(1)->pluck('created_at')->first();

		//$createdDate = Practice::where('id',Session::get('practice_dbid'))->pluck('created_at');
		$startDate = Self::dateFormat($createdDate);
		$todayDate = Self::dateFormat(date('Y-m-d'));
		return $startDate." - ".$todayDate;
	}

    // Date time conversion based on practice time zone
    // Author: Baskar
    // 04/04/19
	public static function timezone($datetime,$format,$practice_id=''){

        if ($datetime != '' && $datetime != '-0001-11-30 00:00:00' && $datetime != '0000-00-00' && $datetime != '1901-01-01' && $datetime != '01/01/1970') {
            $session_practice_id = Session::get('practice_dbid');
            if(($session_practice_id == $practice_id) && Session::has('timezone')) {
                $timezone = Session::get('timezone');
            } else {
                $practice_id = (!empty($practice_id)) ? $practice_id : $session_practice_id;
                //$practice = Practice::where('id',$practice_id)->select('timezone')->first();
                $practice = Cache::remember('practice_details'.$practice_id , 30, function() use($practice_id) {
                    $practice = Practice::where('id', $practice_id)->select('practice_name', 'timezone','avatar_name', 'avatar_ext')->first();
                    return $practice;
                });

                $timezone = (isset($practice->timezone) && $practice->timezone != NULL) ? $practice->timezone : 'UTC';
            }
            $date = new DateTime($datetime, new DateTimeZone('UTC'));
            $date->setTimezone(new DateTimeZone($timezone));
            return $date->format($format);
        } else {
            return '-Nil-';
        }
    }

    public static function getPracticeTimeZone($practice_id=''){
       $session_practice_id = Session::get('practice_dbid');
       if(($session_practice_id == $practice_id) && Session::has('timezone')) {
            $timezone = Session::get('timezone');
       } else {
           $practice_id = (!empty($practice_id)) ? $practice_id : $session_practice_id;
           $practice = Cache::remember('practice_details'.$practice_id , 30, function() use($practice_id) {
                $practice = Practice::where('id', $practice_id)->select('practice_name', 'timezone','avatar_name', 'avatar_ext')->first();
                return $practice;
            });
           //Practice::where('id',$practice_id)->pluck('timezone')->first();
            $timezone = (isset($practice->timezone) && $practice->timezone != NULL) ? $practice->timezone : 'UTC';
       }
       return $timezone;
    }
    // Date time conversion based on facility time zone
    // Author: Baskar
    // 03/07/19
         ### Date time timezone
    ### Thilagavathy P
    public static function getdefaulttimezone(){
        $practice_timezone = '';
        if(Auth::check()) {
            $user_details = Auth::user();
            if ($user_details->user_type == 'Practice' || ($user_details->user_type == 'Medcubics' && Session::get('practice_dbid') != '')) {
                $practice_timezone = \App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"),'m/d/Y');
            } else {
                $practice_timezone = '';
            }
        }
        return $practice_timezone;
    }
    ###Thilagavathy.P
    ### Date time timezone
    public static function facilityTimezone($datetime,$format,$timezone=''){
        if($timezone!=NULL)
            $timezone = $timezone;
        else
            $timezone = 'UTC';
        $date = new DateTime($datetime, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($timezone));
        return $date->format($format);
    }

    public static function utcTimezoneStartDate($start_date){
        $practice = Practice::where('id',Session::get('practice_dbid'))->select('timezone')->first();
        if($practice['timezone']!=NULL)
            $timezone = $practice['timezone'];
        else
            $timezone = 'UTC';
        $date = new DateTime(date('Y-m-d H:i:s', strtotime($start_date.' 00:00:00')), new DateTimeZone($timezone));
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->format('Y-m-d  H:i:s');
    }

    public static function utcTimezoneEndDate($end_date){
        $practice = Practice::where('id',Session::get('practice_dbid'))->select('timezone')->first();
        if($practice['timezone']!=NULL)
            $timezone = $practice['timezone'];
        else
            $timezone = 'UTC';
        $date = new DateTime(date('Y-m-d H:i:s', strtotime($end_date.' 23:59:59')), new DateTimeZone($timezone));
        $date->setTimezone(new DateTimeZone('UTC'));
        return $date->format('Y-m-d  H:i:s');
    }

    /** Statistics details common function starts */

    public static function getTotalPatientStatements() {
        $statement_sent = Patient::where('status', 'Active')->sum('statements_sent');
        return $statement_sent;
    }

    public static function getTotalDocuments(){
        $documents = Document::count();
        return $documents;
    }

    public static function getTotalPayments(){
        $total_payments = PMTInfoV1::where('pmt_type','Payment')->sum('pmt_amt');
        return $total_payments;
    }

    public static function getPatientPayments(){
        $patient_payments = PMTInfoV1::where('pmt_type','Payment')->where('pmt_method','Patient')->where('pmt_mode','Cash')->orWhere('pmt_mode','Check')->orWhere('pmt_mode','Credit')->orWhere('pmt_mode','Money Order')->count();
        return $patient_payments;
    }

    public static function getInsurancePayments(){
        $insurance_payments = PMTInfoV1::where('pmt_type','Payment')->where('pmt_method','Insurance')->where('pmt_mode','EFT')->orWhere('pmt_mode','Check')->orWhere('pmt_mode','Credit')->count();
        return $insurance_payments;
    }

    public static function getTotalCharges(){
        $total_charges = ClaimInfoV1::sum('total_charge');
        return $total_charges;
    }

    public static function getTotalAdjustment(){
        $total_adjustment = PMTInfoV1::where('pmt_type','Adjustment')->sum('pmt_amt');
        return $total_adjustment;
    }

    public static function getTotalRejections(){
        $rejection = ClaimInfoV1::where('status','Rejection')->count();
        return $rejection;
    }

    public static function getTotalDenial(){
        $denails = ClaimInfoV1::where('status','Denied')->count();
        return $denails;
    }

    public static function getTotalSubmittedClaims(){
        $submitted_claims = ClaimInfoV1::sum('claim_submit_count');
        return $submitted_claims;
    }

    public static function getUsers(){
        $total_users = Users::where('status','Active')->count();
        return $total_users;
    }

    public static function getProviders(){
        $total_providers = Provider::where('status','Active')->count();
        return $total_providers;
    }

    public static function getTemplatesSent(){
        $templates_sent = Template::where('status','Active')->count();
        return $templates_sent;
    }
    /** Statistics details common function ends */

    public static function getPatientOtherIns($patient_id=0) {
        if (!is_numeric($patient_id))
            $patient_id = Helpers::getEncodeAndDecodeOfId($patient_id, 'decode');

        $patIns = PatientInsurance::whereIn('category', ['Primary','Secondary','Tertiary'])->where('patient_id',$patient_id)->pluck('insurance_id')->all();
        // For post insurance payment, from other then have to get confirmation popup related check start
        $patIns = !empty($patIns) ? $patIns : [];
        $patOthIns = PatientInsurance::where('category', 'Others')->where('patient_id',$patient_id)->whereNotIn('insurance_id',$patIns)->pluck('insurance_id')->all();
        $other_ins = (!empty($patOthIns)) ? implode(",", $patOthIns) : 0;
        return $other_ins;
    }
    public static function wishList($id,$url){
        $list = Wishlist::where('created_by',$id)->pluck('url', 'id')->all();
        $wish = Wishlist::where('created_by',$id)->get();
        $wishlist = array($list,$wish);
        return $wishlist;
    }
    public static function getAccNo($id){

    }
    public static function getclaimId($mode, $id){
        $mode_id['id'] = "";
        $mode_id['icon'] = "";
        $mode = array_values($mode);
        // dd($mode);
        if($mode[0] == "Patient" || $mode[0] == "Patients"){
            if($id!=""){
                $val = Patient::where('id',$id)->select('account_no')->first();
                $mode_id['id'] = $val->account_no;
            }
            $mode_id['icon'] = "fa fa-user";
        } elseif ($mode[0] == "Charges"){
            if($id!=""){
                $val = ClaimInfoV1::where('id',$id)->select('claim_number')->first();
                $mode_id['id'] = $val->claim_number;
            }
            $mode_id['icon'] = "fa fa-list";
        } elseif ($mode[0] == "Practice"){
            $mode_id['icon'] = "fa fa-medkit";
            switch ($mode[1]) {
                case "Facility":
                    if($id!=""){
                        $val = Facility::where('id',$id)->select('short_name')->first();
                        $mode_id['id'] = $val->short_name;
                    }
                    break;

                case "Provider":
                    if($id!=""){
                        $val = Provider::where('id',$id)->select('short_name')->first();
                        $mode_id['id'] = $val->short_name;
                    }
                    break;

                case "Insurance":
                    if($id!=""){
                        $val = Insurance::where('id',$id)->select('insurance_name', 'short_name')->first();
                        if($val->short_name=="")
                            $mode_id['id'] = substr($val->insurance_name,0,5);
                        else
                            $mode_id['id'] = $val->short_name;
                    }
                    break;

                case "ICD 10":
                    if($id!=""){
                        $val = Icd::where('id',$id)->select('icd_code')->first();
                        $mode_id['id'] = $val->icd_code;
                    }
                    break;

                case "CPT / HCPCS":
                    if($id!=""){
                        $val = Cpt::where('id',$id)->select('cpt_hcpcs')->first();
                        $mode_id['id'] = $val->cpt_hcpcs;
                    }
                    break;

                case "Modifiers":
                    if($id!=""){
                        $val = Modifier::where('id',$id)->select('code')->first();
                        $mode_id['id'] = $val->code;
                    }
                    break;

                case "Remittance Codes":
                    // $val = Facility::where('id',$id)->first();
                    $mode_id['id'] = "codes";
                    break;

                case "Employer":
                    if($id!=""){
                        $val = Employer::where('id',$id)->select('employer_name')->first();
                        $mode_id['id'] = $val->employer_name;
                    }
                    break;

                case "Templates":
                    if($id!=""){
                        $val = Template::where('id',$id)->select('name')->first();
                        $mode_id['id'] = $val->name;
                    }
                    break;
            }
        }
        elseif ($mode[0] =="Reports") {
            $mode_id['icon'] = "fa fa-bar-chart";
        }
        elseif ($mode[0] =="Payments") {
            $mode_id['icon'] = "fa fa-money";
        }
        elseif ($mode[0] =="Claims") {
            $mode_id['icon'] = "fa fa-cart-arrow-down";
        }
        elseif ($mode[0] =="AR Management") {
            $mode_id['icon'] = "fa fa-laptop";
        }
        elseif ($mode[0] =="Scheduler") {
            $mode_id['icon'] = "fa fa-calendar-o";
        }
        elseif ($mode[0] =="Documents") {
            $mode_id['icon'] = "fa fa-book";
        }
        elseif ($mode[0] =="Practice Analytics" || $mode[0] =="Payment Analytics") {
            $mode_id['icon'] = "fa fa-tachometer";
        }elseif ($mode[1] =="Provider Analytics") {
            $mode_id['icon'] = "fa fa-money";
        }
        return $mode_id;
    }

    public static function startsWith($haystack, $needle) {
         $length = strlen($needle);
         return (substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith($haystack, $needle) {
        // search forward starting from end minus needle length characters
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
    }

    public static function favCptsList(){
        $cpts = Cpts::join('favouritecpts', 'cpts.id', '=', 'favouritecpts.cpt_id')->orderby('cpt_hcpcs', 'asc')->pluck('cpt_hcpcs','cpt_hcpcs')->all();
        return $cpts;
    }

    public static function CptsRangeBetween($type_from, $type_to){
        $cpts = Cpts::join('favouritecpts', 'cpts.id', '=', 'favouritecpts.cpt_id')->where('cpt_hcpcs','>=',$type_from)->where('cpt_hcpcs','<=',$type_to)->orderby('cpt_hcpcs', 'asc')->pluck('cpt_hcpcs')->all();
        return $cpts;
    }

    public static function getBrowser()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) {
            $bname = 'Internet Explorer';
            $ub = "MSIE";
        } elseif(preg_match('/Firefox/i',$u_agent)) {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";
        } elseif(preg_match('/Chrome/i',$u_agent)) {
            $bname = 'Google Chrome';
            $ub = "Chrome";
        } elseif(preg_match('/Safari/i',$u_agent)) {
            $bname = 'Apple Safari';
            $ub = "Safari";
        } elseif(preg_match('/Opera/i',$u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif(preg_match('/Netscape/i',$u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            } else {
                $version= $matches['version'][1];
            }
        } else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {   $version="?";   }

        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    }

    public static function getPrac(){
        $prac = DB::connection('responsive')->table('practices')->where('status', 'Active')->pluck('practice_name', 'id')->all();
        return $prac;
    }

    public static function getCus(){
        $cus = DB::connection('responsive')->table('customers')->where('status', 'Active')->pluck('short_name', 'id')->all();
        return $cus;
    }


	public static function EdiFileLogGenerate($type = '', $filename = '',$org_fileSize = '', $fileSize = ''){
		Log::useDailyFiles(storage_path().'/logs/EdiFileLog/'.date('Y-m-d').'.log');
		Log::info('====================================================================================================================================================');
		Log::info("Date and Time: ".date('Y-m-d h:i:s'));
		Log::info('Module: '.$type);
		Log::info('File Name: '.$filename);
		Log::info('Target File Size: '.$org_fileSize);
		Log::info('Copied File Size: '.$fileSize);
	}

	public static function claimsNotificationNo(){
		$dataArr['claimEdits'] = ClaimInfoV1::where('status','Ready')->where('no_of_issues','!=',0)->where('error_message','!=','')->count();
		$dataArr['EdiRejection'] = ClaimInfoV1::where('status','Rejection')->count();
		$dataArr['Submitted'] = ClaimInfoV1::where('status','Submitted')->count();
		return $dataArr;
	}

    public static function getClaimHoldReasons() {
        $hold_options = Holdoption::where('status', 'Active')->pluck('option', 'id')->all();
        return $hold_options;
    }

	/* Timezone list populate function start */
    public static function populateTimeZoneList() {
        $timezones = [];
       // $timezone_identifiers = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, 'US');
        $timezone_identifiers = DateTimeZone::listIdentifiers(DateTimeZone::AMERICA);
        foreach($timezone_identifiers as $timezone_identifier) {
            $timezones[$timezone_identifier] = $timezone_identifier;
            echo "<br>$timezone_identifier\n";
        }
        return $timezones;
    }
	/* Timezone list populate function end */

    public static function isProvider($userId)  {
        $export = ReportExport::where('id',$userId)->first();
        $user = User::where('id',$export->created_by)->first();
        $data['status'] = ($user->practice_user_type == 'provider' && $user->provider_access_id != 0) ? true : false;
        $data['provider_id'] = $user->provider_access_id;
        return $data;
    }

    /* Google Bucket Start */


    /*
    * Upload files into google bucket
    * $category = reports / documents / statement / edi / era / clearinghouseacceptance / etc
    * $path = practice_id / category / created_by / created_at(mmyy)

    */
    public static function uploadResourceFile($category, $prefix='', $data = []){
        try {

            switch ($category) {
                case 'reports':
                        $filename = $data['filename'];
                        if(isset($data['target_dir']) && $data['target_dir'] != '') {
                            $directory = $data['target_dir'];
                        } else {
                            $currDate = date('my',strtotime(Carbon::now()));
                            $directory = $prefix.DS.$category.DS.Auth::user()->id.DS.$currDate;
                        }

                        // Create directory in cloud
                        Storage::disk('gcs')->makeDirectory($directory, 0775);
                        // Store file into cloud
                        $store_file_val = $data['contents']; // file_get_contents($destPath);
                        // \Log::info($directory.DS.$filename);
                        $res = Storage::disk('gcs')->put($directory.DS.$filename,  $store_file_val);
                        if($res) {
                            return Response::json(array('status'=>'success', 'message'=> "File uploaded successfully."));
                        } else {
                            return Response::json(array('status'=>'error', 'message'=> "File uploaded failed. Please try again later."));
                        }
                        break;

                case 'statements':
                        //
                        break;

                case 'documents':
                        //
                        break;

                default:
                        return Response::json(array('status'=>'error', 'message' => "File uploaded not handled."));
            }

        } catch(Exception $e) {
            $errMsg = $e->getMessage();
            \Log::info("File not uploaded due to error. Message: ".$errMsg);
            return Response::json(array('status'=>'error', 'message'=> "File not uploaded."));
            exit;
        }
    }

    /*
    * Remove resource file from Google Bucket
    * $category = reports / documents / patient_statement / edi / era / clearinghouseacceptance / etc
    */

    public function removeResourceFile($category, $id, $type, $file_name) {
        try {
            $disk = Storage::disk('gcs');
            $prefix = $practice_id = Session::get('practice_dbid');
            $userdetails = Auth::user();
            $recId = ($id != '') ?  Helpers::getEncodeAndDecodeOfId($id,'decode') : "";
            switch ($category) {

                case 'reports':
                    $reportInfo = ReportExport::where('practice_id', $practice_id)
                                  ->where('id', $recId)->first();
                    $filename = 'reports'.DS.$prefix.DS.$reportInfo->report_file_name;
                    if($exists = $disk->exists($filename)) {
                        \Log::info("Report: Removed resource: ".$filename." ## By ".\Auth::user()->username);
                        $disk->delete($filename);
                    } else {
                        \Log::info("Report: Resource unavailable: ".$filename." ## By ".\Auth::user()->username);
                    }
                    break;

                case 'patient_statement':
                    break;

                case 'documents':
                    /*
                    $type_arr  = explode("_",$type);
                    $prefix = @$type_arr[0]; // Practice prefix
                    $category = @$type_arr[1]; // Document category
                    $filename = 'documents'.DS.$prefix.DS.$category.DS.$file_name;
                    if($exists = $disk->exists($filename)) {
                        \Log::info("DOC: Removed resource: ".$filename." ## By ".\Auth::user()->username);
                        $disk->delete($filename);
                    } else {
                        \Log::info("DOC: Resource unavailable: ".$filename." ## By ".\Auth::user()->username);
                    }
                    */
                    break;

                default:
                    \Log::info(" Trying to remove resource ".$file_type. " ID #". $id. "Type: ".$type."Filename :".$file_name." ## By ".\Auth::user()->username);
                    break;
            }

        } catch(Exception $e){
            \Log::info("Exception occured while remove resource ".$filename." ## By ".\Auth::user()->username. " Error: ".$e->getMessage() );
        }
    }

    public static function getResourceDownloadLink($module, $id, $file_name) {
        $file_name = str_ireplace("downloads/", "", $file_name);
        $id = (is_numeric($id)) ? Helpers::getEncodeAndDecodeOfId($id) : $id;
        $downLink = url('download/'.$module.'/'.$id.'/'.$file_name);
        return $downLink;
    }

    /*
    * /{category}/{id}/{file_name}
    * category = reports / statment / ...
    * id = Resource ID
    * $file_name = download file name
    */

    public function downloadResourceFile($category, $id, $file_name='') {
        $request = Request::all();
        try {
            $userdetails = Auth::user();
            $prefix = $practice_id = Session::get('practice_dbid');
            $recId = ($id != '') ?  Helpers::getEncodeAndDecodeOfId($id,'decode') : "";
            // Check invalid ID handling here
            if(!is_numeric($recId)) {
                \Log::info(" User ".$userdetails['username']. " Unauthorized Access Module:".$file_type." #".$id." Type: ".$type." File: ".$file_name);
                return Redirect::to('home')->with('message', Lang::get("common.invalid_id_msg"));
            }

            $disk = Storage::disk('gcs');
            switch ($category) {

                case 'reports':
                    $reportInfo = ReportExport::where('practice_id', $practice_id)
                    ->where('id', $recId)->where('is_active', 1)->first();

                    if (count($reportInfo) > 0) {

                        // Check in google cloud
                        $filename = 'reports' . DS . $prefix. DS . $type. DS. $reportInfo->report_file_name;
                        if($exists = $disk->exists($filename)) {
                            $file = $disk->get($filename);
                            $fSize = $disk->size($filename);
                            //dd("")
                            header ( 'Content-Length: ' . $fSize );
                            header('Content-type: application/pdf');
                            echo $file;
                        } else {
                            \Log::info("Error occured while download Report File unavailable ".$filename );
                            $msg = "File not found !!!";
                            return Redirect::to('home')->with('message', $msg);
                        }
                        exit;

                    } else {
                        \Log::info(" User ".$userdetails['username']. " tried to access Report File. Invalid File ID:".$recId);
                        return Redirect::to('home')->with('message', Lang::get("common.invalid_id_msg"));
                    }
                    break;

                case 'documents':
                    /*
                    $dDet =  Documents::where('id', $recId);
                    $dDet = $dDet->where('is_active', 1)->first();
                    if (count($dDet) > 0) {

                        $id = $dDet->id;
                        $docType = $dDet->category;
                        $prefix_dir = $userPractice['prefix'];
                        $file_name = $dDet->pdf_attachment;

                        // Check existing location end
                        $filename = 'documents'.DS.$prefix_dir.DS.$docType.DS.$dDet->pdf_attachment;
                        if($exists = $disk->exists($filename)) {
                            $file = $disk->get($filename);
                            $fSize = $disk->size($filename);
                            header ( 'Content-Length: ' . $fSize );
                            header('Content-type: application/pdf');
                            echo $file;
                        } else {
                            \Log::info(" User ".$userdetails['username']. " tried to access unavailable DOC-resource : ".$filename);
                            $msg = "File not found !!!";
                            return Redirect::to('home')->with('message', $msg);
                        }

                        exit;
                    }
                    */
                    break;

                default:
                    break;
            }
        } catch(Exception $e) {
            \Log::info("Error occured while downloadResourceFile. Error ".$e->getMessage() );
        }
    }

    /* Google Bucket End */
       #### get new feature update count Author:: Thilagavathy Start###
    public static function getNewUpdatesCount() {
        //echo $module.'/'.$id.'/'.$file_name;
        $practice_timezone = Self::getPracticeTimeZone();
        $end_date = date('Y-m-d',strtotime(Carbon::now()));
        $start_date = date('Y-m-d',strtotime(Carbon::now()->subDays(30)));
        $getblogs_public = Blog::on('responsive')->where(DB::raw('DATE(created_at)'),'>=',$start_date)->where(DB::raw('DATE(created_at)'),'<=',$end_date)->where('status','=','Active')->where('privacy','=','Public')->count();

        return $getblogs_public;
    }
    #### get new feature update count Author:: Thilagavathy end###

	public static function getIdToDos($claimIds){
		if(!empty($claimIds)){
			$claimsId = explode(',',$claimIds);
			$dos = ClaimInfoV1::whereIn('id',$claimsId)->select(DB::raw('DATE_FORMAT(date_of_service, "%m/%d/%Y") as date_of_service'))->pluck('date_of_service')->toArray();
			$dos = implode(', ',$dos);
			return $dos;
		}else{
			return '-Nil-';
		}
	}

	public static function getPatientBudgetBalence($patient_id){
		$budgetInfo = PatientBudget::where('patient_id',$patient_id)->whereNull('deleted_at')->where('status','Active')->get()->first();
		$budgetPlanType = $budgetInfo->plan;
		$budgetTotalAmount = $budgetInfo->budget_total;
		$budgetDueTotalAmount = 0;
		$start_date = date('Y-m-d',strtotime($budgetInfo->statement_start_date));
		$end_date = date('Y-m-d');
		$practice_timezone = Self::getPracticeTimeZone();
		switch ($budgetPlanType) {
			case 'Weekly':
				$date1=date_create(date('Y-m-d',strtotime($budgetInfo->statement_start_date)));
				$date2=date_create(date('Y-m-d'));
				$diff=date_diff($date1,$date2);
				$totaldays = $diff->format("%a");
				$week = $totaldays / 7;
				if(strpos($week, ".") !== false){
					$week = explode('.',$week);
					$week = $week[0];
					if($week == 0){
						$budgetDueTotalAmount = $budgetInfo->budget_amt;
						$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();
						/* \Log::info('Total Paid');
						\Log::info($pmtInfo->total_paid); */
						if($pmtInfo->total_paid >= $budgetDueTotalAmount){
							if($pmtInfo->total_paid >= $budgetTotalAmount){
								$budgetDueTotalAmount = 0;
							}else{
								$bTAAfterPaid = $budgetTotalAmount - $pmtInfo->total_paid;
								if($bTAAfterPaid <= $budgetInfo->budget_amt ){
									$budgetDueTotalAmount = $bTAAfterPaid;
								}else{
									$budgetDueTotalAmount = $budgetInfo->budget_amt;
								}
							}
						}else{
							$budgetDueTotalAmount = $budgetDueTotalAmount - $pmtInfo->total_paid;
						}
					}else{
						$week = $week + 1;
						$budgetDueTotalAmount = $budgetInfo->budget_amt * $week;

						$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();

						if($pmtInfo->total_paid >= $budgetDueTotalAmount){
							if($pmtInfo->total_paid >= $budgetTotalAmount){
								$budgetDueTotalAmount = 0;
							}else{
								$bTAAfterPaid = $budgetTotalAmount - $pmtInfo->total_paid;
								if($bTAAfterPaid <= $budgetInfo->budget_amt ){
									$budgetDueTotalAmount = $bTAAfterPaid;
								}else{
									$budgetDueTotalAmount = $budgetInfo->budget_amt;
								}
							}
						}else{
							$budgetDueTotalAmount = $budgetDueTotalAmount - $pmtInfo->total_paid;
						}

					}
				}else{
					if($week != 0)
						$budgetDueTotalAmount = $budgetInfo->budget_amt * $week;
					else
						$budgetDueTotalAmount = $budgetInfo->budget_amt;
					$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();
					if($pmtInfo->total_paid >= $budgetDueTotalAmount){
						if($pmtInfo->total_paid >= $budgetTotalAmount){
							$budgetDueTotalAmount = 0;
						}else{
							$bTAAfterPaid = $budgetTotalAmount - $pmtInfo->total_paid;
							if($bTAAfterPaid <= $budgetInfo->budget_amt ){
								$budgetDueTotalAmount = $bTAAfterPaid;
							}else{
								$budgetDueTotalAmount = $budgetInfo->budget_amt;
							}
						}
					}else{
						$budgetDueTotalAmount = $budgetDueTotalAmount - $pmtInfo->total_paid;
					}
				}
				break;
			case 'Biweekly':
				$date1=date_create(date('Y-m-d',strtotime($budgetInfo->statement_start_date)));
				$date2=date_create(date('Y-m-d'));
				$diff=date_diff($date1,$date2);
				$totaldays = $diff->format("%a");
				$week = $totaldays / 14;
				if(strpos($week, ".") !== false){
					$week = explode('.',$week);
					$week = $week[0];
					if($week == 0){
						$budgetDueTotalAmount = $budgetInfo->budget_amt;
						$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();
						if($pmtInfo->total_paid >= $budgetDueTotalAmount){
							if($pmtInfo->total_paid >= $budgetTotalAmount){
								$budgetDueTotalAmount = 0;
							}else{
								$bTAAfterPaid = $budgetTotalAmount - $pmtInfo->total_paid;
								if($bTAAfterPaid <= $budgetInfo->budget_amt ){
									$budgetDueTotalAmount = $bTAAfterPaid;
								}else{
									$budgetDueTotalAmount = $budgetInfo->budget_amt;
								}
							}
						}else{
							$budgetDueTotalAmount = $budgetDueTotalAmount - $pmtInfo->total_paid;
						}
					}else{
						$week = $week + 1;
						$budgetDueTotalAmount = $budgetInfo->budget_amt * $week;
						$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();
						if($pmtInfo->total_paid >= $budgetDueTotalAmount){
							if($pmtInfo->total_paid >= $budgetTotalAmount){
								$budgetDueTotalAmount = 0;
							}else{
								$bTAAfterPaid = $budgetTotalAmount - $pmtInfo->total_paid;
								if($bTAAfterPaid <= $budgetInfo->budget_amt ){
									$budgetDueTotalAmount = $bTAAfterPaid;
								}else{
									$budgetDueTotalAmount = $budgetInfo->budget_amt;
								}
							}
						}else{
							$budgetDueTotalAmount = $budgetDueTotalAmount - $pmtInfo->total_paid;
						}
					}
				}else{
					if($week != 0)
						$budgetDueTotalAmount = $budgetInfo->budget_amt * $week;
					else
						$budgetDueTotalAmount = $budgetInfo->budget_amt;
					$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();
					if($pmtInfo->total_paid >= $budgetDueTotalAmount){
						if($pmtInfo->total_paid >= $budgetTotalAmount){
							$budgetDueTotalAmount = 0;
						}else{
							$bTAAfterPaid = $budgetTotalAmount - $pmtInfo->total_paid;
							if($bTAAfterPaid <= $budgetInfo->budget_amt ){
								$budgetDueTotalAmount = $bTAAfterPaid;
							}else{
								$budgetDueTotalAmount = $budgetInfo->budget_amt;
							}
						}
					}else{
						$budgetDueTotalAmount = $budgetDueTotalAmount - $pmtInfo->total_paid;
					}
				}
				break;
			case 'Monthly':
				$date1=date_create(date('Y-m-d',strtotime($budgetInfo->statement_start_date)));
				$date2=date_create(date('Y-m-d'));
				$diff=date_diff($date1,$date2);
				$totaldays = $diff->format("%a");
				$week = $totaldays / 30;
				if(strpos($week, ".") !== false){
					$week = explode('.',$week);
					$week = $week[0];
					if($week == 0){
						$budgetDueTotalAmount = $budgetInfo->budget_amt;
						$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();
						if($pmtInfo->total_paid >= $budgetDueTotalAmount){
							if($pmtInfo->total_paid >= $budgetTotalAmount){
								$budgetDueTotalAmount = 0;
							}else{
								$bTAAfterPaid = $budgetTotalAmount - $pmtInfo->total_paid;
								if($bTAAfterPaid <= $budgetInfo->budget_amt ){
									$budgetDueTotalAmount = $bTAAfterPaid;
								}else{
									$budgetDueTotalAmount = $budgetInfo->budget_amt;
								}
							}
						}else{
							$budgetDueTotalAmount = $budgetDueTotalAmount - $pmtInfo->total_paid;
						}
					}else{
						$week = $week + 1;
						$budgetDueTotalAmount = $budgetInfo->budget_amt * $week;
						$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();
						if($pmtInfo->total_paid >= $budgetDueTotalAmount){
							if($pmtInfo->total_paid >= $budgetTotalAmount){
								$budgetDueTotalAmount = 0;
							}else{
								$bTAAfterPaid = $budgetTotalAmount - $pmtInfo->total_paid;
								if($bTAAfterPaid <= $budgetInfo->budget_amt ){
									$budgetDueTotalAmount = $bTAAfterPaid;
								}else{
									$budgetDueTotalAmount = $budgetInfo->budget_amt;
								}
							}
						}else{
							$budgetDueTotalAmount = $budgetDueTotalAmount - $pmtInfo->total_paid;
						}
					}
				}else{
					if($week != 0)
						$budgetDueTotalAmount = $budgetInfo->budget_amt * $week;
					else
						$budgetDueTotalAmount = $budgetInfo->budget_amt;
					$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();
					if($pmtInfo->total_paid >= $budgetDueTotalAmount){
						if($pmtInfo->total_paid >= $budgetTotalAmount){
							$budgetDueTotalAmount = 0;
						}else{
							$bTAAfterPaid = $budgetTotalAmount - $pmtInfo->total_paid;
							if($bTAAfterPaid <= $budgetInfo->budget_amt ){
								$budgetDueTotalAmount = $bTAAfterPaid;
							}else{
								$budgetDueTotalAmount = $budgetInfo->budget_amt;
							}
						}
					}else{
						$budgetDueTotalAmount = $budgetDueTotalAmount - $pmtInfo->total_paid;
					}
				}
				break;
			case 'Bimonthly':
				$date1=date_create(date('Y-m-d',strtotime($budgetInfo->statement_start_date)));
				$date2=date_create(date('Y-m-d'));
				$diff=date_diff($date1,$date2);
				$totaldays = $diff->format("%a");
				$week = $totaldays / 60;
				if(strpos($week, ".") !== false){
					$week = explode('.',$week);
					$week = $week[0];
					if($week == 0){
						$budgetDueTotalAmount = $budgetInfo->budget_amt;
						$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();
						if($pmtInfo->total_paid >= $budgetDueTotalAmount){
							if($pmtInfo->total_paid >= $budgetTotalAmount){
								$budgetDueTotalAmount = 0;
							}else{
								$bTAAfterPaid = $budgetTotalAmount - $pmtInfo->total_paid;
								if($bTAAfterPaid <= $budgetInfo->budget_amt ){
									$budgetDueTotalAmount = $bTAAfterPaid;
								}else{
									$budgetDueTotalAmount = $budgetInfo->budget_amt;
								}
							}
						}else{
							$budgetDueTotalAmount = $budgetDueTotalAmount - $pmtInfo->total_paid;
						}
					}else{
						$week = $week + 1;
						$budgetDueTotalAmount = $budgetInfo->budget_amt * $week;
						$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();
						if($pmtInfo->total_paid >= $budgetDueTotalAmount){
							if($pmtInfo->total_paid >= $budgetTotalAmount){
								$budgetDueTotalAmount = 0;
							}else{
								$bTAAfterPaid = $budgetTotalAmount - $pmtInfo->total_paid;
								if($bTAAfterPaid <= $budgetInfo->budget_amt ){
									$budgetDueTotalAmount = $bTAAfterPaid;
								}else{
									$budgetDueTotalAmount = $budgetInfo->budget_amt;
								}
							}
						}else{
							$budgetDueTotalAmount = $budgetDueTotalAmount - $pmtInfo->total_paid;
						}
					}
				}else{
					if($week != 0)
						$budgetDueTotalAmount = $budgetInfo->budget_amt * $week;
					else
						$budgetDueTotalAmount = $budgetInfo->budget_amt;
					$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();
					if($pmtInfo->total_paid >= $budgetDueTotalAmount){
						if($pmtInfo->total_paid >= $budgetTotalAmount){
							$budgetDueTotalAmount = 0;
						}else{
							$bTAAfterPaid = $budgetTotalAmount - $pmtInfo->total_paid;
							if($bTAAfterPaid <= $budgetInfo->budget_amt ){
								$budgetDueTotalAmount = $bTAAfterPaid;
							}else{
								$budgetDueTotalAmount = $budgetInfo->budget_amt;
							}
						}
					}else{
						$budgetDueTotalAmount = $budgetDueTotalAmount - $pmtInfo->total_paid;
					}
				}
				break;

		}
		$dataArr['budgetTotalAmount'] = $budgetTotalAmount;
		$dataArr['budgetDueTotalAmount'] = $budgetDueTotalAmount;
		/* \Log::info($dataArr); */
		return $dataArr;
	}

	public static function getPatientBudgetTotalBalence($patient_id){
		$budgetInfo = PatientBudget::where('patient_id',$patient_id)->whereNull('deleted_at')->where('status','Active')->get()->first();
		$budgetPlanType = $budgetInfo->plan;
		$budgetTotalAmount = $budgetInfo->budget_total;
		$budgetDueTotalAmount = 0;
		$start_date = date('Y-m-d',strtotime($budgetInfo->statement_start_date));
		$end_date = date('Y-m-d');
		$practice_timezone = Self::getPracticeTimeZone();

		$pmtInfo = PMTClaimTXV1::where('patient_id',$patient_id)->whereIn('pmt_type',['Payment','Refund','Credit Balance'])->where('pmt_method','Patient')->selectRaw('sum(total_paid) as total_paid')->whereRaw("DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) >= '".$start_date."' and DATE(CONVERT_TZ(created_at,'UTC','".$practice_timezone."')) <= '".$end_date."'")->get()->first();
		$totalBalence = $budgetTotalAmount - $pmtInfo->total_paid;
		if($totalBalence > 0){
			$totalBalence = $totalBalence;
		}else{
			$totalBalence = '0.00';
		}

		return $totalBalence;
	}



}