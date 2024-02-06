<?php namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use App\Models\Medcubics\Users as User;
use Auth;
use Session;
use Redirect;
class UpdateUserLastAccessTime {

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if(Auth::check() && Auth::user()->deleted_at == null ) {
			if ($request->ajax()) {
				// 
			} else {					
				$id = Auth::user()->id;
				$filename = Session::get('last_updated');
				$last_updated_time = date('Y-m-d H:i:s', $filename);
				//$user = User::where('id',$id)->first();	
				$user = Auth::User();	// Instead of getting user details use auth;
				$user['last_access_date'] = $last_updated_time;
				$user->save();
				//\Log::info("Login updated ".$id." Time ".$last_updated_time); \Log::info($user);
			}
			return $next($request);
		} else {
			return Redirect::to('/auth/logout?msg=You are not a valid user');
		}			
	}

}