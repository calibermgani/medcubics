<?php

namespace App\Http\Controllers\Api;

use File;
use Response;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App;
use Excel;
use Illuminate\Http\Request;
use PDF;
use DB;
use Config;
use App\Http\Helpers\Helpers as Helpers;

class CommonExportApiController extends Controller {

    public $expiryduration = 99556567567;

    /**
     *
     * This function generates a csv document as Report and
     * returns it as file download option.
     *
     * @author Sriram Balasubramanian
     * @param array $param
     * @param Object $data
     */
    public function generatemultipleExports($param, $data, $download_type) {
        $modulefields = $param['fields'];
        $heading = $param['heading'];
        $modulename = $param['filename'] . '_' . date('m-d-Y');
        /*  Excel::create($modulename, function($excel) use($modulefields, $heading, $data) {
          $excel->sheet('csv sheet', function($sheet) use($modulefields, $heading, $data) {
          $sheet->setOrientation('landscape');
          $sheet->loadView('practice/provider/view', compact ( 'modulefields', 'heading', 'data'));
          });
          })->export($download_type); */

        $results = array();
        if ($param["heading"] != "aging_report") { //Aging report has diff values directly sended total array
            //echo "<pre>";	print_r($param);
            // Heading 
            foreach ($param['fields'] as $key => $value) { //print_r($value);die;
                if (is_array($value)) {
                    /* changes done here-bhuvana-icdworksheet report */
                    $get_value[] = isset($value['label']) ? $value['label'] : $value;
                } else {
                    $get_value[] = $value;
                }
            }
            array_push($results, $get_value);
            // Content
            if (count($data) > 0) {
                foreach ($data as $row) {

                    $temp = array();
                    foreach ($param['fields'] as $key => $value) {
                        /// Check if value is array or not ///
                        if (is_array($value)) {
                            // Check table is empty or not. (GEtting records from same table but combine with multiple records means we no need to set table name, if relationala table going to use/referr we should pass table relation in controller(EX: patient_insurance->insurance_details (no need to send patient table)))
                            if ($value['table'] != '') {
                                $table_name = $row->$value['table'];
                                $explode_value = explode(".", $value['table']);
                                if (count($explode_value) > 1 && count($explode_value) < 3)
                                    $table_name = $row->$explode_value[0]->$explode_value[1];
                            } else {
                                $table_name = $row;
                            }

                            /// Check if we used use function - Use function - getting record by passing id/value with another modle or functions 
                            if (isset($value['use_function'])) {
                                $use_function = $value['use_function'][0];
                                // Declare model or controller 
                                $use_function_name = new $use_function;

                                /// Call function by passing single id. If we want to pass more than one parameter, need to define new below.
                                if (!is_array($value['column']) && $value['column'] != '' && strpos($value['column'], '.') !== false) {
                                    $column = explode(".", $value['column']);
                                    $count = 1;
                                    foreach ($column as $column_key => $column_val) {
                                        $result_val = ($count > 1) ? $table_name->$result_val->$column_val : $column_val;
                                        $result_val = $result_val;
                                        $count++;
                                    }
                                    $get_function_value = @$use_function_name::$value['use_function'][1]($result_val);
                                } else {
                                    $get_function_value = @$use_function_name::$value['use_function'][1]($table_name->$value['column']);
                                }
                                if ($get_function_value != '') {
                                    $temp[] = $get_function_value;
                                } else {
                                    if (strpos($key, 'Insurance Name') !== false) {
                                        $temp[] = 'Self Pay';    
                                    } else {
                                        $temp[] = '-';
                                    }
                                }
                                
                            } elseif (isset($value['column'])) {

                                // Check column has more than one value/parameter
                                if (is_array($value['column'])) {
                                    $combin_value = [];
                                    foreach ($value['column'] as $col_value) {
                                        if (strlen($col_value) > 1)
                                            $combin_value[] = @$table_name->$col_value;
                                        else
                                            $combin_value[] = $col_value;
                                    }

                                    // Include comma for first name & last name option.
                                    if ((in_array('lastname', $value['column']) && in_array('firstname', $value['column'])) || (in_array('first_name', $value['column']) && in_array('last_name', $value['column']))) {
                                        $temp[] = implode(', ', $combin_value);
                                    } else {
                                        $temp[] = implode(' ', $combin_value);
                                    }
                                } elseif (!is_array($value['column'])) {
                                    // Single paramater within same table
                                    if (@$table_name->$value['column']) {
                                        $temp[] = $table_name->$value['column'];
                                    } else {
                                        $temp[] = ' ';
                                    }
                                }
                            } else {
                                $temp[] = ' ';
                            }
                        } else { // If not an array, directly print the value using key
                            if ($key == "appointment_time") { // Replace appt time
                                $app_time = explode("-", $row->$key);
                                $temp[] = $app_time[0];
                            } elseif ((strpos($key, 'created_at') !== false) || (strpos($key, 'updated_at') !== false) || (strpos($value, 'Date') !== false) || (strpos($value, 'end_date') !== false)) {
                                // Replace date format. Used calim date format
                                if ($row->$key != "0000-00-00")
                                    $temp[] = Helpers::dateFormat($row->$key, 'date');
                                elseif ($row->$key == "0000-00-00") {
                                    $temp[] = "Never";
                                }
                            } elseif ((strpos($key, 'DOB') !== false) || (strpos($key, 'dos') !== false) || (strpos($key, 'dob') !== false) || (strpos($key, 'check_date') !== false) || (strpos($key, 'date_of_service') !== false)) {
                                if ($row->$key != "0000-00-00")
                                    $temp[] = Helpers::dateFormat($row->$key, 'dob');
                                else
                                    $temp[] = Helpers::dateFormat($row->$key = '', 'dob');
                            } elseif ((strpos($key, 'tot_patient_due') !== false) || (strpos($key, 'tot_insurance_due') !== false) || (strpos($key, 'tot_balance_amt') !== false)) {
                                $temp[] = Helpers::priceFormat($row->$key, 'export');
                            } else {
                                $temp[] = (isset($row->$key) && $row->$key != '') ? $row->$key : " ";  /* This is changed for ICD report */
                            }
                        }
                    }
                    
                    array_push($results, $temp);
                }
            } else {
                $temp = ['0' => "No Records found"];
                array_push($results, $temp);
            }
        } else {
            $results[0] = $param['fields'];
            $get_value = count($param['fields']);
            foreach ($data as $key_count => $col_value) {
                foreach ($col_value as $key => $value) {
                    if ($value == 0)
                        $col_value[$key] = (string) $value;
                }
                array_push($results, $col_value);
            }
        }
        $excel = App::make('excel');
        $column_count = count($get_value);
        try{
        Excel::create($modulename, function($excel) use($results, $column_count, $download_type, $param) {
            //      Excel::filter('chunk')->load($modulename.'.csv')->chunk(250, function($excel) use($results) {
            
            $excel->sheet('Sheet', function($sheet) use($results, $column_count, $download_type, $param) {
                $totalcolumn = count($results[0]);
                $totalrow = count($results) + 4;
                $sheet->setOrientation('landscape');
                $select_head1 = array('1' => 'A1', '2' => 'B1', '3' => 'C1', '4' => 'D1', '5' => 'E1', '6' => 'F1', '7' => 'G1', '8' => 'H1', '9' => 'I1', '10' => 'J1', '11' => 'K1', '12' => 'L1', '13' => 'M1', '14' => 'N1', '15' => 'O1', '16' => 'P1', '17' => 'Q1', '18' => 'R1', '19' => 'S1', '20' => 'T1', '21' => 'U1', '22' => 'V1', '23' => 'W1', '24' => 'X1', '25' => 'Y1', '26' => 'Z1');
                $select_head2 = array('1' => 'A2', '2' => 'B2', '3' => 'C2', '4' => 'D2', '5' => 'E2', '6' => 'F2', '7' => 'G2', '8' => 'H2', '9' => 'I2', '10' => 'J2', '11' => 'K2', '12' => 'L2', '13' => 'M2', '14' => 'N2', '15' => 'O2', '16' => 'P2', '17' => 'Q2', '18' => 'R2', '19' => 'S2', '20' => 'T2', '21' => 'U2', '22' => 'V2', '23' => 'W2', '24' => 'X2', '25' => 'Y2', '26' => 'Z2');
                $select_head3 = array('1' => 'A3', '2' => 'B3', '3' => 'C3', '4' => 'D3', '5' => 'E3', '6' => 'F3', '7' => 'G3', '8' => 'H3', '9' => 'I3', '10' => 'J3', '11' => 'K3', '12' => 'L3', '13' => 'M3', '14' => 'N3', '15' => 'O3', '16' => 'P3', '17' => 'Q3', '18' => 'R3', '19' => 'S3', '20' => 'T3', '21' => 'U3', '22' => 'V3', '23' => 'W3', '24' => 'X3', '25' => 'Y3', '26' => 'Z3');
                $selectedcolumn1 = $select_head1[$totalcolumn];
                $selectedcolumn2 = $select_head2[$totalcolumn];
                $selectedcolumn3 = $select_head3[$totalcolumn];
                $practice_name = \App\Models\Practice::getPracticeName(\Session::all()['practice_dbid']);
                $report_name = (!empty($param['heading']))?$param['heading']:$param['filename'];
                $session_login_id = \Session::get('login_session_id');
                $session_id     =   Helpers::getEncodeAndDecodeOfId($session_login_id,"decode");
                $get_login_qry  =   explode("::::",$session_id);
                $user = \App\User::where('id',$get_login_qry[1])->value('short_name');
                $sheet->row(1, array($practice_name));
                $sheet->row(2, array($report_name));
                $row3 = 'User: '.$user.' | Created: '.App\Http\Helpers\Helpers::timezone(date("m/d/y H:i:s"), 'm/d/y');
                $sheet->row(3, array($row3));
                $sheet->setHeight(1, 20);
                $sheet->setHeight(2, 20);
                $sheet->setHeight(3, 30);
                $sheet->mergeCells('A1:' . $selectedcolumn1);
                $sheet->mergeCells('A2:' . $selectedcolumn2);
                $sheet->mergeCells('A3:' . $selectedcolumn3);
                                
                
                if ($param["heading"] == "aging_report") {
                    //Aging report has diff values dir send total array
                    $sheet->mergeCells('B2:C2');
                    $sheet->mergeCells('D2:E2');
                    $sheet->mergeCells('F2:G2');
                    $sheet->mergeCells('H2:I2');
                    $sheet->mergeCells('J2:K2');
                    $sheet->mergeCells('L2:M2');
                    $sheet->mergeCells('N2:O2');
                    $sheet->mergeCells('P2:Q2');
                }
                
                $sheet->cells('A1:' . $selectedcolumn1, function($cells) use($download_type) {
                    $cells->setAlignment('center');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize(13);
                    $cells->setFontColor('#00837c');
                });
                $sheet->cells('A2:' . $selectedcolumn2, function($cells) use($download_type) {
                    $cells->setAlignment('center');
                    $cells->setFontSize(11);
                });

                 $sheet->cells('A3:' . $selectedcolumn3, function($cells) use($download_type) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center');
                    $cells->setFontSize(11);
                });

                $select_head = array('1' => 'A4', '2' => 'B4', '3' => 'C4', '4' => 'D4', '5' => 'E4', '6' => 'F4', '7' => 'G4', '8' => 'H4', '9' => 'I4', '10' => 'J4', '11' => 'K4', '12' => 'L4', '13' => 'M4', '14' => 'N4', '15' => 'O4', '16' => 'P4', '17' => 'Q4', '18' => 'R4', '19' => 'S4', '20' => 'T4', '21' => 'U4', '22' => 'V4', '23' => 'W4', '24' => 'X4', '25' => 'Y4', '26' => 'Z4');
                $selectedcolumn = $select_head[$totalcolumn];

                $select_head10 = array('1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D', '5' => 'E', '6' => 'F', '7' => 'G', '8' => 'H', '9' => 'I', '10' => 'J', '11' => 'K', '12' => 'L', '13' => 'M', '14' => 'N', '15' => 'O', '16' => 'P', '17' => 'Q', '18' => 'R', '19' => 'S', '20' => 'T', '21' => 'U', '22' => 'V', '23' => 'W', '24' => 'X', '25' => 'Y', '26' => 'Z');
                $sel2 = $select_head10[$totalcolumn];

                $sheet->cells('A4:' . $selectedcolumn, function($cells) {
                    $cells->setFontWeight('bold');
                });
                $year = 'Copyright © ' . date("Y") . ' Medcubics. All rights reserved.';
               // echo "<br>**********<pre>";print_r($results);
                $sheet->fromArray($results, null, 'A4', false, false);
                $sheet->appendRow(array($year));
                $sheet->mergeCells('A' . $totalrow . ':' . $sel2 . $totalrow);

                if ($download_type == 'pdf') {
                    if ($column_count > 4) {
                        $range = range("A", "Z");
                        $width = 150;
                        if ($column_count > 14)
                            $width = 120;
                        if ($column_count > 16)
                            $width = 80;
                        $col_width = ceil($width / $column_count);
                        for ($i = 0; $i < $column_count; $i++) {
                            $sheet->setWidth($range[$i], $col_width);
                        }
                    }
                }                
                /*$sheet->row($sheet->getHighestRow(), function ($row) {
                    $row->setFontColor('#00837c');
                });*/
            });            
        })->export($download_type);
        } catch(Exception $e){
            //dd("Exception occured: ".$e->getMessage());
        }
    }

    public function generateExports($param, $data) {

        $modulefields = $param['fields'];
        $heading = [$param['heading']];
        $timestamp = strtotime("now");
        $modulename = $param['filename'] . "_" . date('m-d-y', $timestamp) . ".csv";
        $http_headers = ['Content-Type' => 'application/vnd.ms-excel', 'Content-Disposition' => 'attachment; filename="' . $modulename . '"'];
        $results = array();

        foreach ($data as $row) {
            $temp = array();
            foreach ($modulefields as $key => $value) {
                if (is_array($value)) {
                    if ($row->$value['table']) {
                        if (is_array($value['column'])) {
                            $temp[$key] = $row->$value['table']->$value['column'][0] . ', ' . $row->$value['table']->$value['column'][1];
                        } else {
                            $temp[$key] = $row->$value['table']->$value['column'];
                        }
                    } else {
                        $temp[$key] = '';
                    }
                } else {
                    $temp[$key] = $row->$key;
                }
            }
            array_push($results, $temp);
        }

        //Resetting headers labels
        foreach ($modulefields as $key => $value) {
            if (is_array($value)) {
                $modulefields[$key] = ucfirst($value['label']);
            }
        }

        //Below code is used to store report as CSV file
        //The generated report will be deleted in subsequent 
        //request if the report is older than expiry duration 
        //chdir('../storage/reports');
        if (App::environment() == Config::get('siteconfigs.production.defult_production'))
            $public_path = $_SERVER['DOCUMENT_ROOT'] . '/medcubics';
        else
            $public_path = public_path();

        $path = $public_path . '/media/reports';
        $dirs = File::directories($path);

        if (count($dirs) > 0) {
            foreach ($dirs as $dir) {
                $dirname = File::name($dir);
                if ($dirname < $timestamp - ($this->expiryduration)) {
                    File::deleteDirectory($path . '/' . $dirname);
                }
            }
        }

        File::makeDirectory($path . '/' . $timestamp);
        $report = array();
        $filename = $path . '/' . $timestamp . '/' . $modulename;
        $file = fopen($filename, 'w');
        fputcsv($file, $heading);
        fputcsv($file, $modulefields);
        foreach ($results as $result) {
            fputcsv($file, $result);
        }
        fclose($file);
        $filename = $filename;

        return Response::download($filename, $modulename, $http_headers);
    }

    // Get practice model records.
    public function collectmodal($table, $data, $columns) {
        foreach ($data as $key => $value) {
            // Change date format in admin records.
            for ($i = 3; $i < count($columns); $i++) {
                if ((strpos($columns[$i], 'effectivedate') !== false) || (strpos($columns[$i], 'inactivedate') !== false)) { // Replace date format.
                    if ($value->$columns[$i] != "0000-00-00" || $value->$columns[$i] != "")
                        $value->$columns[$i] = Helpers::dateFormat($value->$columns[$i], 'date');
                }
            }

            // Replace data with practice records.
            if (DB::table($table)->where($columns[0], $value->$columns[0])->count() > 0) {
                $collect_array = DB::table($table)->where($columns[0], $value->$columns[0])->first();
                // Change date format in practice records.
                for ($i = 2; $i < count($columns); $i++) {
                    if ((strpos($columns[$i], 'effectivedate') !== false) || (strpos($columns[$i], 'inactivedate') !== false)) { // Replace date format.
                        if ($collect_array->$columns[$i] != "0000-00-00" || $collect_array->$columns[$i] != "")
                            $value->$columns[$i] = Helpers::dateFormat($collect_array->$columns[$i], 'date');
                    } elseif (isset($columns[$i])) {
                        $value->$columns[$i] = $collect_array->$columns[$i];
                    }
                }
            }
            $collect[] = $value;
        }
        return $collect;
    }

    public function generatebulkexport($table, $columns, $filename, $columnheading, $export, $with_table = '', $con_response = 'No', $pcon = '',$columns_index = '') {
        

        $modulename = $filename . '_' . date('m-d-y');

        if ($export == 'pdf') {
            if ($con_response == 'yes') {
                $db_name = getenv('DB_DATABASE');
                $get_array = DB::connection('responsive')->table($table);
            } else {
                $get_array = DB::table($table);
            }
            if ($with_table != "") {
                $with_table_name = explode(".", $with_table);
                if (count($with_table_name) == 2) {
                    $get_array->leftjoin($with_table_name[0], $with_table_name[0] . '.id', '=', $table . '.' . $with_table_name[1]);
                    $get_array->whereNull($table . '.deleted_at');
                }
            }
            $data = $get_array->select($columns)->get();

            // get record using connect responsive table with practice table.
            if ($pcon == 'yes') {
                $data = $this->collectmodal($table, $data, $columns);
            }
			
            PDF::loadHTML(view('admin/insurance/record', compact('data', 'columns', 'columnheading','columns_index')))->filename($modulename . '.pdf')->download();
        } else {
            $excel = App::make('excel');
            Excel::create($modulename, function($excel) use($export, $table, $columns, $columnheading, $with_table, $con_response, $pcon) {
                $insurances_count = DB::connection(getenv('DB_DATABASE'))->table($table)->count();
                if ($export == 'xlsx') {

                    for ($i = 0; $i <= $insurances_count;) {
                        if ($con_response == 'yes') {
                            $db_name = getenv('DB_DATABASE');
                            $get_array = DB::connection($db_name)->table($table);
                        } else {
                            $get_array = DB::table($table);
                        }
                        if ($with_table != "") {
                            $with_table_name = explode(".", $with_table);
                            if (count($with_table_name) == 2) {
                                $get_array->leftjoin($with_table_name[0], $with_table_name[0] . '.id', '=', $table . '.' . $with_table_name[1]);
                                $get_array->whereNull($table . '.deleted_at');
                            }
                        } else {
                            $get_array->whereNull('deleted_at');
                        }
                        $data = $get_array->select($columns)->skip($i)->take(15000)->get();

                        // get record using connect responsive table with practice table.
                        if ($pcon == 'yes') {
                            $data = $this->collectmodal($table, $data, $columns);
                        }

                        $excel->sheet('Sheet' . $i, function($sheet) use($data, $columnheading) {
                            $collect_array = '';
                            $heading_array[] = $columnheading;
                            $array = json_decode(json_encode($data), true);
                            $collect_array = array_merge($heading_array, $array);
                            //  $sheet->fromArray($collect_array, null, 'A1', false, false);
                            // $sheet->fromArray($collect_array, null, 'A1', false, false);
                            $sheet->setOrientation('landscape');
                            $select_head1 = array('1' => 'A1', '2' => 'B1', '3' => 'C1', '4' => 'D1', '5' => 'E1', '6' => 'F1', '7' => 'G1', '8' => 'H1', '9' => 'I1', '10' => 'J1', '11' => 'K1', '12' => 'L1', '13' => 'M1', '14' => 'N1', '15' => 'O1', '16' => 'P1', '17' => 'Q1', '18' => 'R1', '19' => 'S1', '20' => 'T1', '21' => 'U1', '22' => 'V1', '23' => 'W1', '24' => 'X1', '25' => 'Y1', '26' => 'Z1');
                            $totalcolumn = count($columnheading);
                            $selectedcolumn1 = $select_head1[$totalcolumn];

                            $sheet->row(1, array('Medcubics'));

                            $sheet->setHeight(1, 25);
                            $sheet->mergeCells('A1:' . $selectedcolumn1);
                            $sheet->cells('A1:' . $selectedcolumn1, function($cells) {
                                $cells->setAlignment('left');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(15);
                                $cells->setFontColor('#00837c');
                            });
                            $select_head = array('1' => 'A2', '2' => 'B2', '3' => 'C2', '4' => 'D2', '5' => 'E2', '6' => 'F2', '7' => 'G2', '8' => 'H2', '9' => 'I2', '10' => 'J2', '11' => 'K2', '12' => 'L2', '13' => 'M2', '14' => 'N2', '15' => 'O2', '16' => 'P2', '17' => 'Q2', '18' => 'R2', '19' => 'S2', '20' => 'T2', '21' => 'U2', '22' => 'V2', '23' => 'W2', '24' => 'X2', '25' => 'Y2', '26' => 'Z2');
                            $selectedcolumn = $select_head[$totalcolumn];
                            $sheet->cells('A2:' . $selectedcolumn, function($cells) {
                                $cells->setBackground('#00837c');
                                $cells->setFontColor('#ffffff');
                            });
                            $year = 'Copyright © ' . date("Y") . ' Medcubics. All rights reserved.';
                            $sheet->fromArray($collect_array, null, 'A2', false, false);
                            $sheet->appendRow(array($year));
                            //$sheet->mergeCells('A'.$totalrow.':'.$sel2.$totalrow);	
                            $sheet->row($sheet->getHighestRow(), function ($row) {
                                $row->setFontColor('#00837c');
                            });
                        });

                        $i = $i + 15000;
                    }
                } elseif ($export == 'csv') {
                    if ($con_response == 'yes') {
                        $db_name = getenv('DB_DATABASE');
                        $get_array = DB::connection('responsive')->table($table);
                    } else {
                        $get_array = DB::table($table);
                    }

                    if ($with_table != "") {
                        $with_table_name = explode(".", $with_table);
                        if (count($with_table_name) == 2) {
                            $get_array->leftjoin($with_table_name[0], $with_table_name[0] . '.id', '=', $table . '.' . $with_table_name[1]);
                            $get_array->whereNull($table . '.deleted_at');
                        }
                    } else {
                        $get_array->whereNull('deleted_at');
                    }
                    $data = $get_array->select($columns)->get();

                    // get record using connect responsive table with practice table.
                    if ($pcon == 'yes') {
                        $data = $this->collectmodal($table, $data, $columns);
                    }

                    $excel->sheet('Sheet', function($sheet) use($data, $columnheading) {
                        $sheet->setOrientation('landscape');
                        $collect_array = '';
                        $heading_array[] = $columnheading;
                        $array = json_decode(json_encode($data), true);
                        $collect_array = array_merge($heading_array, $array);
                        //$sheet->fromArray($collect_array, null, 'A1', false, false);
                        // $sheet->fromArray($collect_array, null, 'A1', false, false);

                        $sheet->setOrientation('landscape');
                        $select_head1 = array('1' => 'A1', '2' => 'B1', '3' => 'C1', '4' => 'D1', '5' => 'E1', '6' => 'F1', '7' => 'G1', '8' => 'H1', '9' => 'I1', '10' => 'J1', '11' => 'K1', '12' => 'L1', '13' => 'M1', '14' => 'N1', '15' => 'O1', '16' => 'P1', '17' => 'Q1', '18' => 'R1', '19' => 'S1', '20' => 'T1', '21' => 'U1', '22' => 'V1', '23' => 'W1', '24' => 'X1', '25' => 'Y1', '26' => 'Z1');
                        $totalcolumn = count($columnheading);
                        $selectedcolumn1 = $select_head1[$totalcolumn];

                        $sheet->row(1, array('Medcubics'));
                        $sheet->setHeight(1, 25);
                        $sheet->mergeCells('A1:' . $selectedcolumn1);
                        $sheet->cells('A1:' . $selectedcolumn1, function($cells) {
                            $cells->setAlignment('left');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(15);
                            $cells->setFontColor('#00837c');
                        });
                        $select_head = array('1' => 'A2', '2' => 'B2', '3' => 'C2', '4' => 'D2', '5' => 'E2', '6' => 'F2', '7' => 'G2', '8' => 'H2', '9' => 'I2', '10' => 'J2', '11' => 'K2', '12' => 'L2', '13' => 'M2', '14' => 'N2', '15' => 'O2', '16' => 'P2', '17' => 'Q2', '18' => 'R2', '19' => 'S2', '20' => 'T2', '21' => 'U2', '22' => 'V2', '23' => 'W2', '24' => 'X2', '25' => 'Y2', '26' => 'Z2');
                        $selectedcolumn = $select_head[$totalcolumn];
                        $year = 'Copyright © ' . date("Y") . ' Medcubics. All rights reserved.';
                        $sheet->fromArray($collect_array, null, 'A2', false, false);
                        $sheet->appendRow(array($year));
                        //$sheet->mergeCells('A'.$totalrow.':'.$sel2.$totalrow);									
                    });
                }
            })->export($export);
        }
    }

    public function getStorageFile($filename) {
        return File::make(storage_path('public/' . $filename))->response();
    }
}