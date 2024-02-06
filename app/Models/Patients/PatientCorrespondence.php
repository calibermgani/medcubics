<?php namespace App\Models\Patients;

use Illuminate\Database\Eloquent\Model;

class PatientCorrespondence extends Model {

	protected $table = 'patient_correspondence';

	public function creator(){
		return $this->belongsTo('App\User', 'created_by', 'id');
	}

	public function templates(){
		return $this->belongsTo('App\Models\Template', 'template_id', 'id')->where('template_type_id', '!=', '0')->where('status','=','Active');
	}

	public function template_detail(){
		return $this->belongsTo('App\Models\Template', 'template_id', 'id')->with('templatetype');
	}

	public function insurance(){
		return $this->belongsTo('App\Models\Insurance', 'insurance_id', 'id');
	}

	public static $rules = [			
		'email_id' => 'required|email',
		'message' => 'required',
		'subject' => 'required',		
		];	

	public static $messages = [		
		'email_id.required' => 'Email address is required!',
		'email_id.email' => 'Email address in not an vaid format!',
		'message.required' => 'Message field is required!',
		'subject.required' => 'Subject field is required!',			
		];
		
	protected $fillable = [	
		'patient_id',
		'template_id',
		'email_id',
		'message',
		'subject',
		'claim_number',
		'dos',
		'insurance_id'
	];
	
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
}