<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use View;
use DB;
use Config;
use App\Http\Helpers\Helpers as Helpers;

class CodeController extends Api\CodeApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'Codes' );  
		View::share ( 'selected_tab', 'admin/code' );  
		View::share ( 'heading_icon', Config::get('cssconfigs.common.codes'));
	} 
	/*** Listing the codes start ***/	
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$codes = $api_response_data->data->codes;
		$codecategory = $api_response_data->data->codecategory;		
		$heading = 'Customers';
		$heading_icon = 'fa-users';
		return view('admin/code/code',  compact('codes','codecategory','heading','heading_icon'));
	}
	/*** Listing the codes end ***/
	
	/*** Create the code detail start ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$codecategory = $api_response_data->data->codecategory;
		$codecategory_id = $api_response_data->data->codecategory_id;
		$heading = 'Customers';
		$heading_icon = 'fa-users';
		return view('admin/code/create',  compact('codecategory','codecategory_id','heading','heading_icon'));
	}
	/*** Create the code detail end ***/
	
	/*** Store the code detail start ***/
	public function store(Request $request)
	{
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			$insertid = $api_response_data->data;
			return Redirect::to('admin/code/'.$insertid)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/code/create')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** Store the code detail end ***/
	
	/*** Edit the code detail start ***/
	public function edit($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$api_response = $this->getEditApi($id);
		$api_response_data = $api_response->getData();
		$heading = 'Customers';
		$heading_icon = 'fa-users';
		if($api_response_data->status == 'success')
		{
			$code = $api_response_data->data->code;
			$codecategory = $api_response_data->data->codecategory;
			$codecategory_id = $api_response_data->data->codecategory_id;
			return view('admin/code/edit',  compact('code','codecategory','codecategory_id','heading','heading_icon'));
		}
		else
		{
			return Redirect::to('admin/code')->with('message',$api_response_data->message);
		}
	}
	/*** Edit the code detail end ***/
	
	/*** Update the code detail start ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/code/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/code/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** Update the code detail end ***/
	
	/*** Delete the code detail start ***/
	public function destroy($id)
	{
		$id = Helpers::getEncodeAndDecodeOfId($id,'decode');
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/code')->with('success',$api_response_data->message);
	}
	/*** Delete the code detail end ***/
	
	/*** View the code details start ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		$heading = 'Customers';
		$heading_icon = 'fa-users';		
		if($api_response_data->status == 'success')
		{
			$code		 		= 	$api_response_data->data->code;
			$codecategory		= 	$api_response_data->data->codecategory;
			return view ('admin/code/show', ['code' => $code,'codecategory'=> $codecategory, 'heading_icon' => $heading_icon, 'heading' => $heading] );
		}
		else
		{
			return Redirect::to('admin/code')->with('message',$api_response_data->message);
		}
	}
	/*** View the code detail end ***/
	
	/*** Import the code start ***/
    public function getImport()
	{
		$table_name = "codes";
		$mysqlconn = DB::Connection('mysql');
		$results = $mysqlconn->select('DESC '.$table_name . ';');
		$fields = [];
		$neglectedfields = ['id','created_at','updated_at'];
		foreach ($results as $result)
		{
			if(!in_array($result->Field, $neglectedfields))
			{
				$fields[] = $result->Field;
			}
		}
		$fields = array_flatten($fields);
		$delimiters = array(''=>'Select','tab'=>'Tab', '|'=>'Pipe', ','=>'Comma');
		return view('admin/code/import/upload',['delimiters' => $delimiters, 'fields'=>$fields]);
	}	
	/*** Import code end ***/
	
	/*** Load the import code start ***/
	public function postImport(Request $request)
	{
		$tablename		= 'codes';
		$neglectedfields = ['id','created_at','updated_at','created_by','updated_by'];
		$validator = Validator::make(Request::all(),[
				'frm_filename'=>'required',
				'frm_delimiter'=>'required'
		]);
		if($validator->fails()){
			return redirect()->back()->withErrors($validator)->withInput(Request::all());
		}
		//setting time limit for continuous script execution
		set_time_limit(3600);
		$details		= Request::except('_token');
		$delimiter		= $details['frm_delimiter'];
		$file			= Request::file('frm_filename');
		$ext			= $file->getClientOriginalExtension();
		$tmp_filename	= time().'.'.$ext;
		$filesize		= $file->getSize();
		$mysqlconn = DB::Connection('mysql');
		$results = $mysqlconn->select('DESC ' .$tablename );
		$totalfieldcount = count($results)-count($neglectedfields);
		if($file->isValid())
		{
			$dir = './uploadedfile';
			$file->move($dir, $tmp_filename );
			$file = fopen($dir.'/'.$tmp_filename,'r');
			$data = fread($file,$filesize);
			fclose($file);
			$fields = [];
			array_pop($neglectedfields);
			array_pop($neglectedfields);
			foreach($results as $result)
			{
				if(in_array($result->Field, $neglectedfields))
				{
				}
				else
				{
					$fields[] = "`".$result->Field."`";
				}
			}
			switch(strtolower($ext))
			{
				case 'txt':
					$data = nl2br($data);
					$data = str_replace("\r\n", "", $data);
					$data = explode('<br />',$data);
					//analyzing lines in the updated file for column count variation
					$i = 0;
					$errors = [];
					if ($delimiter == "tab")
					{
						$delimiter = "\t";
					}
					$totalfieldcount = $totalfieldcount + 2;
					foreach ($data as $line) 
					{
						$record = explode($delimiter,$line);
						$record[] = Auth::user()->id;		//for created_by field
						$record[] = Auth::user()->id;		//for updated_by field
						if(count($record) != $totalfieldcount)
						{
							$errors[] = ++$i;
						}
						else
						{
							$i++;
						}
					}
					if(count($errors) > 0)
					{
						return redirect()->back()->with('message', 'Line number (' . implode(',',$errors). ') column count mismatch.');
					}
					$fields = implode(',',$fields);
					foreach($data as $line)
					{
						$record = explode($delimiter, $line);
						$record[] = Auth::user()->id;		//for created_by field
						$record[] = Auth::user()->id;		//for updated_by field
						for($i=0;$i<count($record);$i++)
						{
							if ($record[$i] == '')
							{
								$record[$i] = '""';
							}
							else
							{
								$record[$i] = str_replace('"', '\"', $record[$i]);
								$record[$i] = str_replace("'", "\'", $record[$i]);
								$record[$i] = '"'.$record[$i].'"';
							}
						}
						$record = implode(',',$record);
						$mysqlconn->insert("INSERT INTO " . $tablename . "(".$fields.") ". " VALUES(".$record.")");
					}
					break;
				case 'csv':
					$delimiter = ',';
					$data = nl2br($data);
					$data = str_replace("\r\n", "", $data);
					$pattern = "/\".*?\",*/";
					preg_match_all($pattern, $data,$matches,PREG_PATTERN_ORDER);
					global $sample;
					foreach($matches[0] as $key=> $value)
					{
						$keyword = 'randomize' . $key;
						$sample[$value] = $keyword;
					}
					$i=0;
					$errors = [];
					$data = explode('<br />',$data);
					foreach ($data as $key=>$value) 
					{
						$data[$key] = $value = preg_replace_callback($pattern,function($match)
						{
							if(substr_count($match[0], '",') == 0)
							{
								return $GLOBALS['sample'][$match[0]];
							}
							else
							{
								return $GLOBALS['sample'][$match[0]] . ',';
							}
						},
						$value);
						$record = explode($delimiter,$value);
						if(count($record) != $totalfieldcount)
						{
							$errors[] = ++$i;
						}
						else
						{
							$i++;
						}
					}
					if(count($errors) > 0)
					{
						return redirect()->back()->with('message', 'Line number (' . implode(',',$errors). ') column count mismatch.');
					}
					if(count($sample)>0)
					{
						$sample = array_flip($sample);
					}
					$fields = implode(',',$fields);
					foreach($data as $line)
					{
						$record = explode($delimiter, $line);
						for($i=0;$i<count($record);$i++)
						{
							if ($record[$i] == '')
							{
								$record[$i] = '""';
							}
							elseif(isset($sample[$record[$i]]))
							{
								$record[$i] = trim($sample[$record[$i]],',');
								$record[$i] = trim(str_replace('"', '\"', substr($record[$i], 1, strlen($record[$i])-2)));
								$record[$i] = str_replace("'", "\'", $record[$i]);
								$record[$i] = '"'.$record[$i].'"';
							}
							else
							{
								$record[$i] = '"'.$record[$i].'"';
							}
						}
						$record = implode(',',$record);
						$mysqlconn->insert("INSERT INTO " . $tablename . "(".$fields.") ". " VALUES(".$record.")");
					}
					break;
				default:
					break;
			}
			return redirect()->back()->with('message', 'Process Successfull.');
		}
		else
		{
			return redirect()->back()->with('message', 'File not uploaded correctly! Try Again.')->withInput($request->all());
		}
	}        
    /*** Load the import code end***/    
}