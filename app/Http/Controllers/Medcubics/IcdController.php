<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use Auth;
use View;
use DB;
use Config;

class IcdController extends Api\IcdApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'ICD' );  
		View::share ( 'selected_tab', 'admin/icd' );
		View::share( 'heading_icon', Config::get('cssconfigs.common.icd'));
	}  
	
	/*** Icd lists page Starts ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$icd_arr = $api_response_data->data->icd_arr;
		$heading = 'Customers';
		$heading_icon = 'fa-users';
		return view('admin/icd/icd',  compact('icd_arr','heading','heading_icon'));
	}
	/*** Icd lists page Ends ***/
	
	/*** Icd create page Starts ***/
	public function create()
	{
		$heading = 'Customers';
		$heading_icon = 'fa-users';
        return view('admin/icd/create',compact('heading','heading_icon'));
	}
	/*** Icd create page Ends ***/
	
	/*** Icd form submission Starts ***/
	public function store(Request $request)
	{	                
        $api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/icd/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/icd/create')->withInput()->withErrors($api_response_data->message);
		}      
              
	}
	/*** Icd form submission Ends ***/
	
	/*** Icd details show page Starts ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		$heading = 'Customers';
		$heading_icon = 'fa-users';
		if($api_response_data->status == 'success')
		{
			$icd = $api_response_data->data->icd;
			return view('admin/icd/show',  compact('icd','heading','heading_icon'));	
		}
		else
		{
			return Redirect::to('admin/icd')->with('error','Invalid Icd');
		}
	}
	/*** Icd details show page Ends ***/
	
	/*** Icd details edit page Starts ***/
	public function edit($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		$heading = 'Customers';
		$heading_icon = 'fa-users';
		if($api_response_data->status == 'success')
		{
			$icd = $api_response_data->data->icd;		
			return view('admin/icd/edit', compact('icd','heading','heading_icon'));	
		}
		else
		{
			return Redirect::to('admin/icd')->with('error','Invalid Icd');
		}
	}
	/*** Icd details edit page Ends ***/
	
	/*** Icd details update Starts ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/icd/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/icd/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** Icd details update Ends ***/
	
	/*** Icd details delete Starts ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/icd')->with('success',$api_response_data->message);
	}
	/*** Icd details delete Ends ***/
	
	/*** Icd import Starts ***/
    public function getImport()
	{
		$table_name = "icd_10";
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
		return view('admin/icd/import-10/upload',['delimiters' => $delimiters, 'fields'=>$fields]);
	}
	/*** Icd import Ends ***/
	
	/*** Icd import Starts ***/
	public function postImport(Request $request)
	{
		$tablename		= 'icd_10';
		$neglectedfields = ['id','created_at','updated_at'];
		$validator = Validator::make(Request::all(),[
		'frm_filename'=>'required',
		'frm_delimiter'=>'required'
		]);
		if($validator->fails())
		{
			return redirect()->back()->withErrors($validator)->withInput(Request::all());
		}
		//setting time limit for continuous script execution
		set_time_limit(3600);
		$details		= Request::except('_token');
		//getting form details
		$delimiter		= $details['frm_delimiter'];
		//getting uploaded file extensions
		$file			= Request::file('frm_filename');
		$ext			= $file->getClientOriginalExtension();
		$tmp_filename	= time().'.'.$ext;
		$filesize		= $file->getSize();
		//getting mysql database table structure
		$mysqlconn = DB::Connection('mysql');
		$results = $mysqlconn->select('DESC ' .$tablename );
		$totalfieldcount = count($results)-count($neglectedfields);
		//checking if the file is uploaded correctly ($_FILE['error'])
		if($file->isValid())
		{
			//moving the file to temporary location
			$dir = './uploadedfile';
			$file->move($dir, $tmp_filename );
			//reading the file contents
			$file = fopen($dir.'/'.$tmp_filename,'r');
			$data = fread($file,$filesize);
			fclose($file);
			//reading the table field structure
			$fields = [];
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
			//performing operation based on uploaded file extension
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
					foreach ($data as $line) 
					{
						$record = explode($delimiter,$line);
						if(count($record) != $totalfieldcount)
						{
							$errors[] = ++$i;
						}
						else{
							$i++;
						}
					}
					//redirect back upon column variation
					if(count($errors) > 0)
					{
						return redirect()->back()->with('message', 'Line number (' . implode(',',$errors). ') column count mismatch.');
					}
					//perform insert operation by appending double quotes(") before and after all columns for smooth insertion in table
					//appending double quotes for empty columns
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
					//performing regular expression search of all strings inbetween double quotes
					$pattern = "/\".*?\",*/";
					preg_match_all($pattern, $data,$matches,PREG_PATTERN_ORDER);
					//saving the values of all entries which are inside double quotes for avoiding conflicts while seperating columns based on comma(,) before inserting into table
					global $sample;
					foreach($matches[0] as $key=> $value)
					{
						$keyword = 'randomize' . $key;
						$sample[$value] = $keyword;
					}
					//we are creating a unique name for columns which are inside double quotes("...") because they might contain comma(,) which might result in conflict while seperating columns
					$i=0;
					$errors = [];
					$data = explode('<br />',$data);
					foreach ($data as $key=>$value) 
					{
						$data[$key] = $value = preg_replace_callback($pattern,function($match)
						{
							if(substr_count($match[0], '",') == 0){
								return $GLOBALS['sample'][$match[0]];
							}
							else
							{
								return $GLOBALS['sample'][$match[0]] . ',';
							}
						},$value);
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
					//redirect back upon column variation
					if(count($errors) > 0)
					{
						return redirect()->back()->with('message', 'Line number (' . implode(',',$errors). ') column count mismatch.');
					}
					//perform insert operation by appending double quotes(") before and after all columns for smooth insertion in table
					//replacing unique name with origin contents enclosed within double quotes as present in attached file
					//appending double quotes for empty columns
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
		else{
			return redirect()->back()->with('message', 'File not uploaded correctly! Try Again.')->withInput($request->all());
		}
	}
	/*** Icd import Ends ***/
	
	/*** Icd details search page Starts ***/
	public function searchIndex()
	{
        return view('admin/icd/search');
	}
	/*** Icd details search page Ends ***/
}
