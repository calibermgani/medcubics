<?php namespace App\Models\Medcubics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model {

	use SoftDeletes;
	protected $dates = ['deleted_at'];

	protected $fillable=['question','answer','category','status','created_by','updated_by'];
	
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
	
	public static $rules = [
			'answer' 	=> 'required'
	];
		
	public static $messages = [
			'question.required' 				=> 'Question is required!',
			//'question.unique' 				=> 'Question is Already exit!',
			'answer.required' 				=> 'Answer is required!'
	];

	public static function getFaqByCategory($category, $search_keyword = '')
	{
		$faq_query = Faq::where('category', $category)->where('status','Active');
		if($search_keyword != '')
			$faq_query->whereRaw('(question LIKE "%'.$search_keyword.'%" or answer LIKE "%'.$search_keyword.'%")');
		$faq = $faq_query->get();
		return $faq;
	}	
}
