<?php namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lang;
use Auth;
use DB;
use App\Http\Helpers\Helpers as Helpers;
class DocumentFollowupList extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'document_followup_list';
	
	public static function boot() {
       	parent::boot();
       	// create a event to happen on saving
       	static::saving(function($table)  {
            foreach ($table->toArray() as $name => $value) {
                if (empty($value) && $name <> 'deleted_at') {
                    $table->{$name} = '';
                }
            }
            return true;
       	});
    }

	protected $fillable = ['document_id','patient_id','claim_id','assigned_user_id','priority','followup_date','status','created_by','created_at','updated_at','notes','Assigned_status'];
	
	public function document(){
		return $this->belongsTo('App\Models\Patients\PatientDocument','document_id','id');
	}

	public function assigned_user(){
		return $this->belongsTo('App\Models\Medcubics\Users','assigned_user_id','id');
	}

	public static function getDocumentAssignedCount($id)
	{	
		$patient_id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$assigned_document_count	=	DocumentFollowupList::whereHas('document',function($query) use($patient_id){
										$query->where('document_type','patients')->whereRaw('temp_type_id = "" and (document_type = "patients" or document_type = "patient_document") and ((document_sub_type = "" and type_id = ?))', array($patient_id))
										->orderBy('id','DESC');	
										})
										->where('assigned_user_id',Auth::user()->id)
										->where('status','!=','Completed')->where('Assigned_status','Active')
										->groupBy('document_id')
										->get()
										->count();
		return $assigned_document_count;
	}
}