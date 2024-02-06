<?php

namespace App\Http\Controllers\Reports\Practicesettings;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Request;
use Redirect;
use View;
use Auth;
use Config;
use App\Http\Controllers\Api\CommonExportApiController as CommonExportApiController;
use App\Models\Practice;
use App\Http\Helpers\Helpers as Helpers;
use Excel;
use PDF;
use App\Models\ReportExport as ReportExportTask;
use App\Exports\BladeExport;

class FacilitylistController extends Api\FacilitylistApiController {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct() {
        View::share('heading', 'Reports');
        View::share('selected_tab', 'practice-report');
        View::share('heading_icon', 'fa-line-chart');
    }

    public function facilitylist() {
        $heading_icon = "fa-line-chart";
        $heading = "Reports";
        $selected_tab = "practice-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        $api_response = $this->getFacilitylistApi();
        $api_response_data = $api_response->getData();
        $cliam_date_details = $api_response_data->data->cliam_date_details;
        $search_fields = $api_response_data->data->search_fields;

        foreach ($cliam_date_details as $year_list) {
            $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);

        $pos_list = $api_response_data->data->pos_list;
        $pos_list1['all'] = "All";
        foreach ($pos_list as $pos_lists) {
            $pos_list1[$pos_lists->id] = $pos_lists->code . '-' . $pos_lists->pos;
        }


        return view('reports/practicesettings/facilitylist/list', compact('pos_list1', 'groupby', 'selected_tab', 'search_fields'));
    }

    public function filter_result() {

        $api_response = $this->getFilterResultApi();
        $api_response_data = $api_response->getData();
        $filter_group_fac_list = $api_response_data->data->filter_group_fac_list;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $practice_opt = $api_response_data->data->practiceopt;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $search_by = $api_response_data->data->search_by;
        return view('reports/practicesettings/facilitylist/facility_reportlist', compact('start_date', 'end_date', 'filter_group_fac_list', 'practice_opt', 'unit_details', 'pagination','search_by'));
    }

    /*     * * Blade - export Facilitylist_Reports ** */

    public function facilityListExport($export = '') {

        $api_response = $this->getFilterResultApi($export);
        $api_response_data = $api_response->getData();
        $filter_group_fac_list = $api_response_data->data->filter_group_fac_list;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $practice_opt = $api_response_data->data->practiceopt;
        $date = date('m-d-Y_hia');
        $name = 'Facilitylist_Reports_' . $date;

        if ($export == 'pdf') {
            $html = view('reports/practicesettings/facilitylist/facility_listexport_pdf', compact('start_date', 'end_date', 'filter_group_fac_list', 'practice_opt', 'export'));
            return PDF::loadHTML($html, 'A4', 'landscape')->download($name . ".pdf");
        } elseif ($export == 'xlsx') {
            /* Excel::create($name, function($excel) use ($filter_group_fac_list, $start_date, $end_date, $practice_opt, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($filter_group_fac_list, $start_date, $end_date, $practice_opt, $export) {
                    $sheet->loadView('reports/practicesettings/facilitylist/facility_listexport')->with("filter_group_fac_list", $filter_group_fac_list)->with("start_date", $start_date)->with("end_date", $end_date)->with("practice_opt", $practice_opt)->with("export", $export);
                });
            })->export('xls');
             */
            $filePath = 'reports/practicesettings/facilitylist/facility_listexport';
            $data['filter_group_fac_list'] = $filter_group_fac_list;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['practice_opt'] = $practice_opt;
            $data['export'] = $export;
            Excel::store(new BladeExport($data,$filePath), $name.'.xls');
        } elseif ($export == 'csv') {
            /* Excel::create($name, function($excel) use ($filter_group_fac_list, $start_date, $end_date, $practice_opt, $export) {
                $excel->sheet('New Sheet', function($sheet) use ($filter_group_fac_list, $start_date, $end_date, $practice_opt, $export) {
                    $sheet->loadView('reports/practicesettings/facilitylist/facility_listexport')->with("filter_group_fac_list", $filter_group_fac_list)->with("start_date", $start_date)->with("end_date", $end_date)->with("practice_opt", $practice_opt)->with("export", $export);
                });
            })->export('csv');
             */
            $filePath = 'reports/practicesettings/facilitylist/facility_listexport';
            $data['filter_group_fac_list'] = $filter_group_fac_list;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['practice_opt'] = $practice_opt;
            $data['export'] = $export;
            Excel::store(new BladeExport($data,$filePath), $name.'.csv');
        }
    }

    //------------------------------- START FACILITY SUMMARY BY BASKAR -----------------------

    public function facilitySummary() {
        $heading_icon = "fa-bar-chart";
        $heading = "Reports";
        $selected_tab = "practice-report";
        $groupby = array();
        $groupby['enter_date'] = 'Choose Date';
        $groupby['today'] = 'Today';
        $groupby['current_month'] = 'Current Month';
        $groupby['last_month'] = 'Last Month';
        $groupby['current_year'] = date('Y');

        $api_response = $this->getFacilitySummarylistApi();
        $api_response_data = $api_response->getData();
        $search_fields = $api_response_data->data->search_fields;
        $searchUserData = $api_response_data->data->searchUserData;
        $cliam_date_details = $api_response_data->data->cliam_date_details;

        foreach ($cliam_date_details as $year_list) {
            $groupby[$year_list->year] = $year_list->year;
        }
        $groupby = array_unique($groupby);

        $pos_list = $api_response_data->data->pos_list;
        $pos_list1['all'] = "All";
        foreach ($pos_list as $pos_lists) {
            $pos_list1[$pos_lists->id] = $pos_lists->code . '-' . $pos_lists->pos;
        }
        return view('reports/practicesettings/facilitylist/summary', compact('pos_list1', 'groupby', 'selected_tab', 'search_fields','heading','heading_icon','searchUserData'));
    }

    public function filter_result_summary() {
        
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getFilterResultSummaryApiSP(); // Stored Procedure
        } else {
            $api_response = $this->getFilterResultSummaryApi(); // DB
        }
        $api_response_data = $api_response->getData();
        //$facilities = $api_response_data->data->facilities->data;
        $facilities = $api_response_data->data->facilities;
        $charges = $api_response_data->data->charges;
        $adjustments = $api_response_data->data->adjustments;
        $patient = $api_response_data->data->patient;
        $insurance = $api_response_data->data->insurance;
        $patient_bal = $api_response_data->data->patient_bal;
        $insurance_bal = $api_response_data->data->insurance_bal;
        $unit_details = $api_response_data->data->unit_details;
        $wallet = $api_response_data->data->wallet;
        $header = $api_response_data->data->header;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $practice_opt = $api_response_data->data->practiceopt;
        $pagination = $api_response_data->data->pagination;
        $pagination->pagination_prt = $api_response->original['data']['pagination']['pagination_prt'];
        $search_by = $api_response_data->data->search_by;
        return view('reports/practicesettings/facilitylist/facility_reportlist', compact('start_date', 'end_date', 'charges', 'practiceopt', 'pagination','unit_details','adjustments','patient','insurance','patient_bal','insurance_bal','facilities','search_by','header','wallet'));
    }

    public function facilityListSummaryExport($export = '',$data = '') {
        
        if(Config::get('siteconfigs.reports_use_stored_procedure') == 1)  {
            $api_response = $this->getFilterResultSummaryApiSP($export, $data); // Stored Procedure
        } else {
            $api_response = $this->getFilterResultSummaryApi($export, $data); // DB
        }
        $api_response_data = $api_response->getData();
        $facilities = $api_response_data->data->facilities;
        $facilities_count1 = count((array)$facilities)+10;
        $facilities_count2 = count((array)$facilities)+15;
        $facilities_count_summary = "C".$facilities_count1.":"."C".$facilities_count2;
        $charges = $api_response_data->data->charges;
        $adjustments = $api_response_data->data->adjustments;
        $patient = $api_response_data->data->patient;
        $insurance = $api_response_data->data->insurance;
        $patient_bal = $api_response_data->data->patient_bal;
        $insurance_bal = $api_response_data->data->insurance_bal;
        $unit_details = $api_response_data->data->unit_details;
        $wallet = $api_response_data->data->wallet;
        $start_date = $api_response_data->data->start_date;
        $end_date = $api_response_data->data->end_date;
        $practice_opt = $api_response_data->data->practiceopt;
        $search_by = $api_response_data->data->search_by;
        $header = $api_response_data->data->header;
        $date = date('m-d-Y');
        // $name = $data['export_id'].'X0X'.'Facility_Summary_' . $date;
        $createdBy = isset($data['created_user']) ? $data['created_user'] : Auth::user()->name;
        $practice_id = isset($data['practice_id']) ? $data['practice_id'] : '';

        $request = Request::all();
        if (isset($request['exports']) && $request['exports'] == 'pdf') {
            $view_path = 'reports/practicesettings/facilitylist/facility_listexport_pdf';
            $report_name = "Facility Summary";
            $data = ['facilities' => $facilities, 'createdBy' => $createdBy, 'practice_id' => $practice_id, 'view_path' => $view_path, 'report_name' => $report_name, 'charges' => $charges, 'adjustments' => $adjustments, 'patient' => $patient, 'insurance' => $insurance, 'patient_bal' => $patient_bal, 'insurance_bal' => $insurance_bal, 'unit_details' => $unit_details, 'wallet' => $wallet, 'practice_opt' => $practice_opt, 'search_by' => $search_by,'header'=>$header];
            return $data;
        }   
        if ($export == 'xlsx' || $export == 'csv') {
            $filePath = 'reports/practicesettings/facilitylist/facility_listexport';
            $data['facilities_count_summary'] = $facilities_count_summary;
            $data['start_date'] = $start_date;
            $data['end_date'] = $end_date;
            $data['charges'] = $charges;
            $data['practice_opt'] = $practice_opt;
            $data['unit_details'] = $unit_details;
            $data['wallet'] = $wallet;
            $data['adjustments'] = $adjustments;
            $data['patient'] = $patient;
            $data['insurance'] = $insurance;
            $data['patient_bal'] = $patient_bal;
            $data['insurance_bal'] = $insurance_bal;
            $data['facilities'] = $facilities;
            $data['search_by'] = $search_by;
            $data['createdBy'] = $createdBy;
            $data['practice_id'] = $practice_id;
            $data['header'] = $header;
            $data['export'] = $export;
            $data['file_path'] = $filePath;
            return $data;
            // Excel::store(new BladeExport($data,$filePath), $name.'.xls');
            $type = '.xls';
        }
        if(isset($data['export_id']))
            ReportExportTask::where('id',$data['export_id'])->update(['status'=>'Completed']);
    }

    //------------------------------- END FACILITY SUMMARY BY BASKAR -----------------------

    /* public function filter_group_result(){	

      $api_response = $this->getGroupFilterResultApi();
      $api_response_data = $api_response->getData();

      $filter_group_fac_list = $api_response_data->data->filter_group_fac_list;

      $start_date = $api_response_data->data->start_date;
      $end_date = $api_response_data->data->end_date;
      //dd($filter_group_fac_list);
      return view('reports/practicesettings/facilitylist/facilityGroup_report_list',compact('start_date','end_date','name','filter_group_fac_list'));
      } */
}
