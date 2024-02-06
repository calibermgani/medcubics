<?php namespace App\Http\Controllers\Medcubics;

use Request;
use Redirect;
use Auth;
use View;
use DB;
use Config;

class CptController extends Api\CptApiController 
{
	public function __construct() 
	{ 
		View::share ( 'heading', 'CPT' );  
		View::share ( 'selected_tab', 'admin/cpt' );
		View::share( 'heading_icon', Config::get('cssconfigs.Practicesmaster.contact_detail'));
	}  
	/*** Cpt lists page Starts ***/
	public function index()
	{
		$api_response = $this->getIndexApi();
		$api_response_data = $api_response->getData();
		$cpt_arr = $api_response_data->data->cpt_list;	
		$heading = "Customers";
		$heading_icon = 'fa-users';
		return view('admin/cpt/cpt',  compact('cpt_arr','heading','heading_icon','heading_icon'));
	}
	/*** Cpt lists page Ends ***/
	
	/*** Cpt create page Starts ***/
	public function create()
	{
		$api_response = $this->getCreateApi();
		$api_response_data = $api_response->getData();
		$pos = $api_response_data->data->pos;
		$qualifier = $api_response_data->data->qualifier;
		$modifier = $api_response_data->data->modifier;	
		$heading = "Customers";
		$heading_icon = 'fa-users';
		return view('admin/cpt/create', compact('modifier','pos','qualifier','heading','heading_icon'));
	}
	/*** Cpt create page Ends ***/
	
	/*** Cpt form submission Starts ***/
	public function store(Request $request)
	{	                
		$api_response = $this->getStoreApi($request::all());
		$api_response_data = $api_response->getData();
		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/cpt/'.$api_response_data->data)->with('success', $api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/cpt/create')->withInput()->withErrors($api_response_data->message);
		}                   
	}
	/*** Cpt form submission Ends ***/
	
	/*** Cpt details show page Starts ***/
	public function show($id)
	{
		$api_response 		= 	$this->getShowApi($id);
		$api_response_data 	= 	$api_response->getData();
		$heading = "Customers";
		$heading_icon = 'fa-users';
		if($api_response_data->status == 'success')
		{
			$cpt = $api_response_data->data->cpt;
			return view('admin/cpt/show',  compact('cpt','heading'));	
		}
		else
		{
			return Redirect::to('admin/cpt')->with('error','Invalid cpts','heading','heading_icon');
		}
	}
	/*** Cpt details show page Ends ***/
	
	/*** Cpt details edit page Starts ***/
	public function edit($id)
	{
		$api_response 		= 	$this->getEditApi($id);
		$api_response_data 	= 	$api_response->getData();
		$heading = "Customers";
		$heading_icon = 'fa-users';
		if($api_response_data->status == 'success')
		{
			$cpt = $api_response_data->data->cpt;
			$pos = $api_response_data->data->pos;
			$qualifier = $api_response_data->data->qualifier;		
			$modifier = $api_response_data->data->modifier;	
			return view('admin/cpt/edit', compact('cpt','pos','qualifier','modifier','heading'));
		}
		else
		{
			return Redirect::to('admin/cpt')->with('error','Invalid cpts','heading','heading_icon');
		}
	}
	/*** Cpt details edit page Ends ***/
	
	/*** Cpt details update Starts ***/
	public function update($id,Request $request)
	{
		$api_response = $this->getUpdateApi(Request::all(), $id);
		$api_response_data = $api_response->getData();

		if($api_response_data->status == 'success')
		{
			return Redirect::to('admin/cpt/'.$id)->with('success',$api_response_data->message);
		}
		else
		{
			return Redirect::to('admin/cpt/'.$id.'/edit')->withInput()->withErrors($api_response_data->message);
		}        
	}
	/*** Cpt details update Ends ***/
	
	/*** Cpt details delete Starts ***/
	public function destroy($id)
	{
		$api_response = $this->getDeleteApi($id);
		$api_response_data = $api_response->getData();
		return Redirect::to('admin/cpt')->with('success',$api_response_data->message);
	}
	/*** Cpt details delete Ends ***/
	
    /*** Cpt import page Starts ***/   
    public function getImport()
	{
		$table_name = "cpts";
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
		return view('admin/cpt/import/upload',['delimiters' => $delimiters, 'fields'=>$fields]);
	}
	/*** Cpt import page Ends ***/
	
	/*** Cpt Postimport page Starts ***/
	public function postImport(Request $request)
	{
		$tablename		= 'cpts';
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
				else{
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
					//appending double quotes for empty columns
					$fields = implode(',',$fields);
					foreach($data as $line){
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
							if(substr_count($match[0], '",') == 0)
							{
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
		else
		{
			return redirect()->back()->with('message', 'File not uploaded correctly! Try Again.')->withInput($request->all());
		}
	}
	/*** Cpt Postimport page Ends ***/
	
	/*** Cpt details search page Starts ***/
	public function SearchIndex()
	{
        return view('admin/cpt/search');
	}
	/*** Cpt details search page Ends ***/
}
