<?php 
namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Patients\Patient as Patient;
use App\Http\Controllers\Api\CommonApiController as CommonApiController;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Config;
use App\Models\EmailTemplate;

class PatientBudget extends Model {
	use SoftDeletes;
    protected $table = 'patient_budget';
	protected $fillable = ['patient_id', 'plan','budget_amt','statement_start_date','budget_balance','budget_period','budget_count','last_statement_sent_date','status','created_by','updated_by','budget_total'];
	
	public static $rules = [
		'plan' => 'required',
		'budget_amt' => 'required',
		'statement_start_date' => 'required'
	];
	public static $messages = [
		'plan.required' => 'Select plan',
		'budget_amt.required' => 'Enter amount',
		'statement_start_date.required' => 'Enter date'
	];

	public function user()
	{
		return $this->belongsTo('App\User','created_by','id');
	}
	
	public static function get_patient_info($id)
	{
		patient::where('id','=',$id)->select('last', 'email')->get();
	}
	
	public function patient()
	{
		return $this->belongsTo('App\Models\Patients\Patient','patient_id','id');
	}
	
	
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
	
		
	/* public static function send_budget_email()
	{
		$current_date = date('Y-m-d'); 
		$PatientBudget = PatientBudget::with('patient')->where('statement_start_date','>=',$current_date)->get();
		// Get all scheduled patient budget plan details.
		if(count($PatientBudget)>0)
		{
			$budget_expected_date = Config::get('siteconfigs.budget.budget_expected_date'); 
			foreach($PatientBudget as $budget) 
			{
				 $dbconnection = new DBConnectionController();
				 $get_patientbalance = $dbconnection->get_PatientBalance($budget->patient_id);
				 
				// Check patient budget balance is or not.
				if($get_patientbalance != '0')
				{
					// Check each patient "last statement sent date" is null or not.
					if($budget->last_statement_sent_date == null)
					{
						if(@$budget->patient->email!='')
						{
							$templates = EmailTemplate::where('template_for','patient_budget')->first();
							$get_Email_Template = $templates->content;	
							
						$arr = [
								'##LASTNAME##' => @$budget->patient->last_name,
								"##FIRSTNAME##" =>@$budget->patient->first_name,
								"##MIDDLENAME##" =>@$budget->patient->middle_name,
								"##BUDGETAMOUNT##" =>$budget->budget_amt,
								"##BUDGETDATE##" => $budget_expected_date
							]; 
						$email_content = strtr($get_Email_Template, $arr);
							
							$res = array('email'	=>	@$budget->patient->email,
							'subject'	=>	'Hi '.@$budget->patient->last_name.', '.@$budget->patient->first_name.' '.@$budget->patient->middle_name,
							'msg'		=>	$email_content,
							'name'		=>	@$budget->patient->last_name.', '.@$budget->patient->first_name.' '.@$budget->patient->middle_name
							);
							$msg_status = CommonApiController::connectEmailApi($res); 
							PatientBudget::where('patient_id','=',$budget->patient_id)->update(['last_statement_sent_date' => $current_date,'budget_balance'=>$budget->budget_amt]);
							Patient::where('id','=',$budget->patient_id)->update(['statements_sent'=> '1']);
						}
					}
					else
					{
						// Check patient "statment sent" count. We will send email to patient only less than 3 count.
						if(@$budget->patient->statements_sent != 3 && @$budget->patient->statements_sent != 'Pre Collection' && @$budget->patient->statements_sent != 'Unknown')
						{
							// subtract month or week from current date.
							if($budget->plan == 'Monthly')
								$get_dayarg = "-1 months";	
							elseif($budget->plan == 'Bimonthly')
								$get_dayarg = "-2 months";	
							elseif($budget->plan == 'Weekly')
								$get_dayarg = "-1 week";	
							elseif($budget->plan == 'Biweekly')
								$get_dayarg = "-2 week";	
							
							$statementDate = date("Y-m-d", strtotime($get_dayarg));	
							// Ex. Send email, If plan is weekly, then "last statement sent date" is to be before 7 days. 
							if($budget->last_statement_sent_date<=$statementDate)
							{
								$get_budget_balance = $budget->budget_amt+$budget->budget_balance; 
								if(@$budget->patient->email!='')
								{
									$templates = EmailTemplate::where('template_for','patient_budget')->first();
									$get_Email_Template = $templates->content;	
									
									$arr = [
										'##LASTNAME##' => @$budget->patient->last_name,
										"##FIRSTNAME##" =>@$budget->patient->first_name,
										"##MIDDLENAME##" =>@$budget->patient->middle_name,
										"##BUDGETAMOUNT##" =>$get_budget_balance,
										"##BUDGETDATE##" => $budget_expected_date
									]; 
									$email_content = strtr($get_Email_Template, $arr);
						
									$res = array('email'	=>	@$budget->patient->email,
									'subject'	=>	'Hi '.@$budget->patient->last_name.', '.@$budget->patient->first_name.' '.@$budget->patient->middle_name,
									'msg'		=>	$email_content,
									'name'		=>	@$budget->patient->last_name.', '.@$budget->patient->first_name.' '.@$budget->patient->middle_name
									);
									
									$msg_status = CommonApiController::connectEmailApi($res); 
									PatientBudget::where('patient_id','=',$budget->patient_id)->update(['last_statement_sent_date' => $current_date,'budget_balance'=>$budget->budget_balance+$budget->budget_amt]);
									
									// If patient budget balance is 0 then set statment sent to 1 other wise increase statment sent count. 
									if($budget->budget_balance == '0.00')
									{
										Patient::where('id','=',$budget->patient_id)->update(['statements_sent'=> '1']);
									}
									else
									{										
										Patient::where('id','=',$budget->patient_id)->update(['statements_sent'=>$budget->patient->statements_sent+1]);	
									}
								 }
							}
						}	
					}
				}
			}
		}
	} */
	
	public static function collect_patient_payment($patient_id, $amt){
		PatientBudget::where('patient_id', $patient_id)->update(['last_statement_sent_date' =>  '', 'budget_balance' => 0]);	
		Patient::where('id', $patient_id)->update(['statements_sent' => 0]);
	}
}