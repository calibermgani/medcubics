<?php

namespace App\Http\Controllers;

use DB;
use Config;
use App\Dyndb;
use App\Hospital;
use App\Transaction;
use App\Practice_info;
use App\Customer as Customer;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Connectors\ConnectionFactory;
use Validator;
class DatabaseController extends Controller {
	public $config;
	public $tables;
	public $connection;
	public $message;
	public function __construct() {
		$this->config = array (
				'driver' => 'mysql',
				'host' => env ( 'DB_HOST_001', 'localhost' ),
				'username' => env ( 'DB_USERNAME_001', 'root' ),
				'password' => env ( 'DB_PASSWORD_001', '' ),
				'charset' => 'utf8',
				'collation' => 'utf8_unicode_ci',
				'prefix' => '',
				'strict' => false,
				'name' => 'newmysqlconnection' 
		);
		$dynamicdatabase = new Dyndb ();
		$this->tables = $dynamicdatabase;
	}
	
	public function index($id) {
		$practices = DB::table('practice_info')->where('t_customer_id', $id)->get();
                $customers = Customer::all();
		return view ( 'Admin\Database\List', compact('practices','customers') );
	}
	public function create() {
		$tableslist = $this->tables->get ()->all ();
		return view ( 'Admin\Database\Create', [ 
				'tablelist' => $tableslist 
		] );
	}
	
	public function store(Request $req) {
		$param = $req->except ( '_token' );
		if($param ['t_prac_name'] != ''){
		$this->name = $param ['t_prac_name'] = "slave_" . $param ['t_prac_name'];
		$tables = [ ];
		if (isset ( $param ['tables'] )) {
			$tables = array_pop ( $param );
		}
		

		
		$manager = new DatabaseManager ( app (), new ConnectionFactory ( app () ) );
		$manager->connection ( 'mysql' );
		$mysqlconnectionobject = $manager->getConnections ();
		$this->connection = $mysqlconnectionobject ['mysql'];
		try {
			if ($this->connection->statement ( 'CREATE DATABASE ' . $this->name )) {
				$pdoinstance = new \PDO ( 'mysql:host=localhost;dbname=' . $this->name, 'root', '', [ ] );
				$this->connection = new MySqlConnection ( $pdoinstance, $this->name, '', $this->config );
				$this->message .= "Database " . $this->name . " created.";
				
				$param = array_slice ( $param, 0, count ( $param ) - 1, true );
				$practiceinfo = new Practice_info ( $param );
				$practiceinfo->save ();
			}
		} catch ( QueryException $qe ) {
			$this->message = $qe->errorInfo [2];
			return redirect ()->back ()->with ( 'message', $this->message );
		}
		
		$successfulltables = [ ];
		$dynamicdatabase = new Dyndb ();
		foreach ( $tables as $id ) {
			$tabledetails = $dynamicdatabase->find ( $id );
			$prefixstatements = "SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';";
			$postfixstatements = "SET SQL_MODE=@OLD_SQL_MODE;SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;";
			$query = $tabledetails->SQLQUERY;
			//$query = str_replace ( "master", "laravel_medcubics_start", $query );
			if ($this->connection->statement ( $prefixstatements . ($query) . $postfixstatements )) {
				//$this->message .= "<br/>" . $tabledetails->Label . " Table created";
				$successfulltables [] = $id;
			} else {
				$this->message .= "<br/>" . $tabledetails->Label . " Table creation ________________(FAILED)";
			}
		}
		
		if (count ( $successfulltables ) > 0) {
			foreach ( $successfulltables as $table_id ) {
				$transaction = new Transaction ();
				$transaction->DynDBID = $table_id;
				$transaction->DatabaseName = $this->name;
				$transaction->save ();
			}
		}
		
		$this->config ['database'] = $this->name;
		Config::set ( 'database.connections.newmysqlconnection', $this->config );
		DB::setDefaultConnection ( 'newmysqlconnection' );

		
		$practice = new Hospital ( $param );
		$practice->save ();
		
		return redirect ()->back ()->with ( 'message', $this->message );
		}
		else{
			return redirect ()->back ()->with ( 'message', "Enter a practice name to start with.");
		}
	}
	
	public function show($id,$databasename) {
		$executedQueries = DB::table ( 'transactions' )->where ( 'DatabaseName', '=', $databasename )->pluck ( 'DynDBID' )->all();
		$executedQueryList = DB::table ( 'dyndb' )->whereIn ( 'id', $executedQueries )->get ();
		$practice_info = DB::table ( 'practice_info' )->where ( 't_customer_id', '=', $id)->get ();
		return view ( 'Admin\Database\View', ['executedQueryList' => $executedQueryList,'practicename' => $databasename,'practice_info' => $practice_info [0]] );
	}
	
	public function edit($id,$databasename) {
		// check if database exists
		$availabletables = DB::table ( 'dyndb' )->pluck ( 'ID' )->all();
		$executedQueries = DB::table ( 'transactions' )->where ( 'DatabaseName', '=', $databasename )->pluck ( 'DynDBID' )->all();
		
		$unexecutedQueries = array_diff ( $availabletables, $executedQueries );
		
		$unexecutedQueryList = DB::table ( 'dyndb' )->whereIn ( 'id', $unexecutedQueries )->get ();
		$executedQueryList = DB::table ( 'dyndb' )->whereIn ( 'id', $executedQueries )->get ();
		$practice_info = DB::table ( 'practice_info' )->where ( 't_prac_name', '=', $databasename )->get ();
		return view ( 'Admin\Database\Edit', ['executedQueryList' => $executedQueryList,'unexecutedQueryList' => $unexecutedQueryList,'practicename' => $databasename,'practice_info' => $practice_info [0]] );
	}
	
	public function update($id, Request $req) {
		$param = $req->except ( [ 
				'_token',
				'_method' 
		] );
		$this->name = $param ['t_prac_name'] = $param ['t_prac_name'];
		$this->config ['database'] = $this->name;
		
		$tables = [ ];
		if (isset ( $param ['tables'] )) {
			$tables = array_pop ( $param );
		}

		$param = array_slice ( $param, 0, count ( $param ) - 1, true );
		$practice_info = Practice_info::find ( $id );
		$practice_info->t_prac_name = $param ['t_prac_name'];
		$practice_info->t_prac_description = $param ['t_prac_description'];
		$practice_info->t_prac_fax = $param ['t_prac_fax'];
		$practice_info->t_prac_email = $param ['t_prac_email'];
		$practice_info->t_prac_fb = $param ['t_prac_fb'];
		$practice_info->save();
		
		
		try {
			$pdoinstance = new \PDO ( 'mysql:host=localhost;dbname=' . $this->name, 'root', '', [ ] );
			$this->connection = new MySqlConnection ( $pdoinstance, $this->name, '', $this->config );
			$this->message .= "Database " . $this->name . " connected.";
		} catch ( QueryException $qe ) {
			$this->message = $qe->errorInfo [2];
			return redirect ()->back ()->with ( 'message', $this->message );
		}

		$successfulltables = [ ];
		$dynamicdatabase = new Dyndb ();
		foreach ( $tables as $id ) {
			$tabledetails = $dynamicdatabase->find ( $id );
			$prefixstatements = "SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';";
			$postfixstatements = "SET SQL_MODE=@OLD_SQL_MODE;SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;";
			$query = $tabledetails->SQLQUERY;
			//$query = str_replace ( "`master`", "`laravel_medcubics_start`", $query );
			if ($this->connection->statement ( $prefixstatements . ($query) . $postfixstatements )) {
				$this->message .= "<br/>" . $tabledetails->Label . " Query Executed";
				$successfulltables [] = $id;
			} else {
				$this->message .= "<br/>" . $tabledetails->Label . " Query Execution ________________(FAILED)";
			}
		}
		
		
		if (count ( $successfulltables ) > 0) {
			foreach ( $successfulltables as $table_id ) {
				$transaction = new Transaction ();
				$transaction->DynDBID = $table_id;
				$transaction->DatabaseName = $this->name;
				$transaction->save ();
			}
		}
		return redirect ()->back ()->with ( 'message', $this->message );
	}
	
	
	public function destroy(Request $request) {
		$this->name = $request->get ( 't_prac_name' );
		$manager = new DatabaseManager ( app (), new ConnectionFactory ( app () ) );
		$manager->connection ( 'mysql' );
		$mysqlconnectionobject = $manager->getConnections ();
		$this->connection = $mysqlconnectionobject ['mysql'];
		try {
			if ($this->connection->statement ( 'DROP DATABASE ' . $this->name )) {
				$this->message .= "Database " . $this->name . " destroyed successfully.";
				DB::table ( 'transactions' )->where ( 'DatabaseName', $this->name )->delete ();
			}
		} catch ( QueryException $qe ) {
			$this->message = $qe->errorInfo [2];
		}
		return redirect ( 'database' )->with ( 'message', $this->message );
	}
}
