<?php
/**
 * Controller genrated using LaraAdmin
 * Help: http://laraadmin.com
 */

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Dwij\Laraadmin\Models\Module;
use Dwij\Laraadmin\Models\ModuleFields;

use Dwij\Laraadmin\Helpers\LAHelper;

use App\User;
use App\Models\Employee;
use App\Role;
use Mail;
use Log;
use Excel;
use File;
use Importer;
use Session;

class EmployeesController extends Controller
{
	public $show_action = true;
	public $view_col = 'name';
	public $listing_cols = ['id', 'name', 'email', 'message_1', 'message_2', 'message_3'];
	
	public function __construct() {
		
		// Field Access of Listing Columns
		if(\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
			$this->middleware(function ($request, $next) {
				$this->listing_cols = ModuleFields::listingColumnAccessScan('Employees', $this->listing_cols);
				return $next($request);
			});
		} else {
			$this->listing_cols = ModuleFields::listingColumnAccessScan('Employees', $this->listing_cols);
		}
	}
	
	/**
	 * Display a listing of the Employees.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module = Module::get('Employees');
		if (Auth::user()->id == 1) {
			if(Module::hasAccess($module->id)) {
				return View('la.employees.index', [
					'show_actions' => $this->show_action,
					'listing_cols' => $this->listing_cols,
					'module' => $module
				]);
			} else {
	            return redirect(config('laraadmin.adminRoute')."/");
	        }
		
		} else {
            return redirect(config('laraadmin.adminRoute')."/");
		}
		
	}

	/**
	 * Show the form for creating a new employee.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	public function import()
	{
		if (Auth::user()->id == 1) {
			$module = Module::get('Employees');
				return View('la.employees.import', [
					'module' => $module
			]);
		} else {
            return redirect(config('laraadmin.adminRoute')."/");
		}

	}

	public function imported(Request $request){
        //validate the xls file
		if (Auth::user()->id == 1) {
	        $this->validate($request, array(
	            'file'      => 'required'
	        ));
	 
	        if($request->hasFile('file')){
	            $extension = File::extension($request->file->getClientOriginalName());
	            if ($extension == "xlsx") {

	                $filepath = $request->file->getRealPath();
	            	$excel = Importer::make('Excel');
	            	$excel->load($filepath);
	            	$collections = $excel->getCollection();
	            	if (count($collections) != 0) {
						$DeleteEmp = DB::table('employees')->WHERE('id', '!=', '1')->delete();
						$DeleteUser = DB::table('users')->WHERE('id', '!=', '1')->delete();
						$DeleteRole = DB::table('role_user')->WHERE('user_id', '!=', '1')->delete();
						$error = '';
		            	foreach ($collections as $key => $collection) {

		            		if ($key == 0)
		            		{

		            		} else {
			            		$rowEmployee = [
					              'name' => $collection[0],
					              'email' => $collection[1],
					              'message_1' => $collection[3],
					              'message_2' => $collection[4],
					              'message_3' => $collection[5]
			                    ];

            	                if(!empty($rowEmployee)){

        							if ($collection[0] == '' || $collection[1] == '' || $collection[2] == '') {
        								$error .= 'データ挿入エラー 行番号をチェック'.strval($key+1).'（エラーを修正して再度アップロードする）.. <br>';
										Session::flash('error', $error);
        	                            continue;
        							} elseif (strlen($collection[0]) < 3 || filter_var($collection[1], FILTER_VALIDATE_EMAIL) == FALSE || strlen($collection[2]) < 6) {

        								$error .= 'データ挿入エラー 行番号をチェック'.strval($key+1).'（エラーを修正して再度アップロードする）.. <br>';
										Session::flash('error', $error);
        	                            continue;

        							} else {

        								$insertData = DB::table('employees')->insert($rowEmployee);
        								$InsertID = DB::getPdo()->lastInsertId();
        								if ($insertData) {
        									$user = User::create([
        										'name' => $collection[0],
        										'email' => $collection[1],
        										'password' => bcrypt($collection[2]),
        										'context_id' => $InsertID,
        										'type' => "Employee",
        									]);

        									DB::table('role_user')->insert([
        										'user_id' => $InsertID,
        										'role_id' => 2
        									]);
        								    Session::flash('success', 'あなたのデータは正常にインポートされました');
        								}else {                        
											Session::flash('error', $error);
        								    return back();
        								}
        							}
            	                } else {
					                Session::flash('error', 'あなたのファイルは空です..!!');
								    return back();
            	                }
		            			
		            		}
		                }
		                return back();
	            	} else {
		                Session::flash('error', 'あなたのファイルは空です..!!');
	            	}
	 
	            }else {
	                Session::flash('error', 'ファイルは '.$extension.' ファイルです.!! 有効なxlxsファイルをアップロードしてください..!!');
	                return back();
	            }
	        }
        } else {
            return redirect(config('laraadmin.adminRoute')."/");
		}
    }

	/**
	 * Store a newly created employee in database.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		if(Module::hasAccess("Employees", "create")) {
		
			$rules = Module::validateRules("Employees", $request);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}
			
			// generate password
			$password = LAHelper::gen_password();
			
			// Create Employee
			$employee_id = Module::insert("Employees", $request);
			// Create User
			$user = User::create([
				'name' => $request->name,
				'email' => $request->email,
				'password' => bcrypt($password),
				'context_id' => $employee_id,
				'type' => "Employee",
			]);

			DB::table('role_user')->insert([
				'user_id' => $employee_id,
				'role_id' => 2
			]);
	
			if(env('MAIL_USERNAME') != null && env('MAIL_USERNAME') != "null" && env('MAIL_USERNAME') != "") {
				// Send mail to User his Password
				Mail::send('emails.send_login_cred', ['user' => $user, 'password' => $password], function ($m) use ($user) {
					$m->from('info@marhabalogistics.com', 'Marhaba');
					$m->to($user->email, $user->name)->subject('Marhaba - Your Login Credentials');
				});
			} else {
				Log::info("User created: username: ".$user->email." Password: ".$password);
			}
			
			return redirect()->route(config('laraadmin.adminRoute') . '.employees.index');
			
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Display the specified employee.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if(Module::hasAccess("Employees", "view")) {
			
			$employee = Employee::find($id);
			if(isset($employee->id)) {
				$module = Module::get('Employees');
				$module->row = $employee;
				
				// Get User Table Information
				$user = User::where('context_id', '=', $id)->firstOrFail();
				
				return view('la.employees.show', [
					'user' => $user,
					'module' => $module,
					'view_col' => $this->view_col,
					'no_header' => true,
					'no_padding' => "no-padding"
				])->with('employee', $employee);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("employee"),
				]);
			}
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Show the form for editing the specified employee.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if(Module::hasAccess("Employees", "edit")) {
			
			$employee = Employee::find($id);
			if(isset($employee->id)) {
				$module = Module::get('Employees');
				
				$module->row = $employee;
				
				// Get User Table Information
				$user = User::where('context_id', '=', $id)->firstOrFail();
				
				return view('la.employees.edit', [
					'module' => $module,
					'view_col' => $this->view_col,
					'user' => $user,
				])->with('employee', $employee);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("employee"),
				]);
			}
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Update the specified employee in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		if(Module::hasAccess("Employees", "edit")) {
			
			$rules = Module::validateRules("Employees", $request, true);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();;
			}
			
			$employee_id = Module::updateRow("Employees", $request, $id);
        	
			// Update User
			$user = User::where('context_id', $employee_id)->first();
			$user->name = $request->name;
			$user->email = $request->email;
			$user->save();
			
			// update user role
			$user->detachRoles();
			$role = Role::find($request->role);
			$user->attachRole($role);
			
			return redirect()->route(config('laraadmin.adminRoute') . '.employees.index');
			
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Remove the specified employee from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		if(Module::hasAccess("Employees", "delete")) {
			Employee::find($id)->delete();
			
			// Redirecting to index() method
			return redirect()->route(config('laraadmin.adminRoute') . '.employees.index');
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}
	
	/**
	 * Datatable Ajax fetch
	 *
	 * @return
	 */
	public function dtajax()
	{
		$values = DB::table('employees')->select($this->listing_cols)->where('id', '!=', '1')->whereNull('deleted_at');
		$out = Datatables::of($values)->make();
		$data = $out->getData();

		$fields_popup = ModuleFields::getModuleFields('Employees');
		
		for($i=0; $i < count($data->data); $i++) {
			for ($j=0; $j < count($this->listing_cols); $j++) { 
				$col = $this->listing_cols[$j];
				if($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
					$data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
				}
				if($col == $this->view_col) {
					$data->data[$i][$j] = '<a href="'.url(config('laraadmin.adminRoute') . '/employees/'.$data->data[$i][0]).'">'.$data->data[$i][$j].'</a>';
				}
				// else if($col == "author") {
				//    $data->data[$i][$j];
				// }
			}
			
			if($this->show_action) {
				$output = '';
				if(Module::hasAccess("Employees", "edit")) {
					$output .= '<a href="'.url(config('laraadmin.adminRoute') . '/employees/'.$data->data[$i][0].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
				}
				
				if(Module::hasAccess("Employees", "delete")) {
					$output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.employees.destroy', $data->data[$i][0]], 'method' => 'delete', 'onsubmit'=> 'return confirm("消去してもよろしいですか?")', 'style'=>'display:inline']);
					$output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
					$output .= Form::close();
				}
				$data->data[$i][] = (string)$output;
			}
		}
		$out->setData($data);
		return $out;
	}
	
	/**
     * Change Employee Password
     *
     * @return
     */
	public function change_password($id, Request $request) {
		
		$validator = Validator::make($request->all(), [
            'password' => 'required|min:6',
			'password_confirmation' => 'required|min:6|same:password'
        ]);
		
		if ($validator->fails()) {
			return \Redirect::to(config('laraadmin.adminRoute') . '/employees/'.$id)->withErrors($validator);
		}
		
		$employee = Employee::find($id);
		$user = User::where("context_id", $employee->id)->where('type', 'Employee')->first();
		$user->password = bcrypt($request->password);
		$user->save();
		
		\Session::flash('success_message', 'パスワードは正常に変更されました');
		
		// Send mail to User his new Password
		if(env('MAIL_USERNAME') != null && env('MAIL_USERNAME') != "null" && env('MAIL_USERNAME') != "") {
			// Send mail to User his new Password
			Mail::send('emails.send_login_cred_change', ['user' => $user, 'password' => $request->password], function ($m) use ($user) {
				$m->from(LAConfigs::getByKey('default_email'), LAConfigs::getByKey('sitename'));
				$m->to($user->email, $user->name)->subject('LaraAdmin - Login Credentials changed');
			});
		} else {
			Log::info("User change_password: username: ".$user->email." Password: ".$request->password);
		}
		
		return redirect(config('laraadmin.adminRoute') . '/employees/'.$id.'#tab-account-settings');
	}
}
