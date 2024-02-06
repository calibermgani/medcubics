<?php
namespace App\Http\Controllers\ExportPDF;
use Request;
use DB;
use Session;
use App\Http\Controllers\Controller;
use App\Models\ReportExport as ReportExport;
use App\Http\Helpers\Helpers as Helpers;

class GenerateReportController extends Controller
{
    public function index($report_name = '', $id=''){
        $user_id = Auth()->user()->id;
        $practice_id = Session::get('practice_dbid');
    	if (!empty($id)) {
    		ReportExport::where('id','=',$id)->update(['status'=>'Completed']);
    		return 'success';
    	}else{
    		$url = Request::url();
            // $report_export = ReportExport::select('report_name','report_file_name','status','id','parameter','report_type')->where('created_by','=',$user_id)->where('report_name', '=', $report_name)->where('practice_id','=',$practice_id)->where(function($qry){
            //                 $qry->where(function($query){
            //                     $query->where('status', '=' ,'Inprocess');
            //                 })->orWhere('status', '=' ,'Completed')->orWhere('status', '=' ,'Pending');
            //             })->get();
            $report_export = ReportExport::select('report_name','report_file_name','status','id','parameter','report_type')
                                ->where('created_by','=',$user_id)->where('report_name', '=', $report_name)
                                ->where('status','!=','Completed')->get();
            $resp = [];
            foreach ($report_export as $key => $list) {
                $resp[$key] = $list;
                $resp[$key]['download_link']  = Helpers::getResourceDownloadLink('reports', $list['id'], @$list['report_file_name']);
            }
	    	return $resp;
    	}
    }
}
