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
use Illuminate\Support\Facades\Schema;
use Session;
use Excel;
use File;
use Importer;

 


use App\Models\Sample_case_Screen;

class Sample_case_ScreensController extends Controller
{
	public $show_action = true;
	public $view_col = 'customer_name';
	public $listing_cols = ['id', 'customer_name', 'record_type', 'case_name', 'task_name', 'grant_total', 'target_name', 'content_preparation', 'project_proposal_day', 'expiration_date', 'application_amount', 'scheduled_date_1', 'scheduled_date_2', 'scheduled_date_3', 'stop', 'reserved', 'case_close_check', 'remarks', 'final_update_date'];
	
	public function __construct() {
		// Field Access of Listing Columns
		if(\Dwij\Laraadmin\Helpers\LAHelper::laravel_ver() == 5.3) {
			$this->middleware(function ($request, $next) {
				$this->listing_cols = ModuleFields::listingColumnAccessScan('Sample_case_Screens', $this->listing_cols);
				return $next($request);
			});
		} else {
			$this->listing_cols = ModuleFields::listingColumnAccessScan('Sample_case_Screens', $this->listing_cols);
		}
	}
	
	/**
	 * Display a listing of the Sample_case_Screens.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$module = Module::get('Sample_case_Screens');
		
		if(Module::hasAccess($module->id)) {
			return View('la.sample_case_screens.index', [
				'show_actions' => $this->show_action,
				'listing_cols' => $this->listing_cols,
				'module' => $module
			]);
		} else {
            return redirect(config('laraadmin.adminRoute')."/");
        }
	}

	public function import()
	{
		$module = Module::get('Sample_case_Screens');
			return View('la.sample_case_screens.import', [
				'module' => $module
			]);
	}

	public function imported(Request $request){
        //validate the xls file
        $this->validate($request, array(
            'file'      => 'required'
        ));
 
        if($request->hasFile('file')){
            $extension = File::extension($request->file->getClientOriginalName());
            if ($extension == "xlsx" || $extension == "xls" || $extension == "csv") {
 
                $path = $request->file->getRealPath();
                $data = Excel::load($path, function($reader) {
                })->get();
                if(!empty($data) && $data->count()){

 
                    foreach ($data as $key => $value) {
                    	
                        $insert[] = [
                        'customer_name' => $value->customer_name,
                        'record_type' => $value->record_type
                        ];
                    }
                	var_dump($data);
                	exit();
                    if(!empty($insert)){
 
                        $insertData = DB::table('sample_case_screens')->insert($insert);
                        if ($insertData) {
                            Session::flash('success', 'Your Data has successfully imported');
                        }else {                        
                            Session::flash('error', 'Error inserting the data..');
                            return back();
                        }
                    }
                }
 
                return back();
 
            }else {
                Session::flash('error', 'File is a '.$extension.' file.!! Please upload a valid xls/csv file..!!');
                return back();
            }
        }
    }

	/**
	 * Show the form for creating a new sample_case_screen.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created sample_case_screen in database.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		if(Module::hasAccess("Sample_case_Screens", "create")) {
		
			$rules = Module::validateRules("Sample_case_Screens", $request);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}
			
			$insert_id = Module::insert("Sample_case_Screens", $request);
			
			return redirect()->route(config('laraadmin.adminRoute') . '.sample_case_screens.index');
			
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Display the specified sample_case_screen.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		if(Module::hasAccess("Sample_case_Screens", "view")) {
			
			$sample_case_screen = Sample_case_Screen::find($id);
			if(isset($sample_case_screen->id)) {
				$module = Module::get('Sample_case_Screens');
				$module->row = $sample_case_screen;
				
				return view('la.sample_case_screens.show', [
					'module' => $module,
					'view_col' => $this->view_col,
					'no_header' => true,
					'no_padding' => "no-padding"
				])->with('sample_case_screen', $sample_case_screen);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("sample_case_screen"),
				]);
			}
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Show the form for editing the specified sample_case_screen.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if(Module::hasAccess("Sample_case_Screens", "edit")) {			
			$sample_case_screen = Sample_case_Screen::find($id);
			if(isset($sample_case_screen->id)) {	
				$module = Module::get('Sample_case_Screens');
				
				$module->row = $sample_case_screen;
				
				return view('la.sample_case_screens.edit', [
					'module' => $module,
					'view_col' => $this->view_col,
				])->with('sample_case_screen', $sample_case_screen);
			} else {
				return view('errors.404', [
					'record_id' => $id,
					'record_name' => ucfirst("sample_case_screen"),
				]);
			}
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Update the specified sample_case_screen in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		if(Module::hasAccess("Sample_case_Screens", "edit")) {
			
			$rules = Module::validateRules("Sample_case_Screens", $request, true);
			
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();;
			}
			
			$insert_id = Module::updateRow("Sample_case_Screens", $request, $id);
			
			return redirect()->route(config('laraadmin.adminRoute') . '.sample_case_screens.index');
			
		} else {
			return redirect(config('laraadmin.adminRoute')."/");
		}
	}

	/**
	 * Remove the specified sample_case_screen from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		if(Module::hasAccess("Sample_case_Screens", "delete")) {
			Sample_case_Screen::find($id)->delete();
			
			// Redirecting to index() method
			return redirect()->route(config('laraadmin.adminRoute') . '.sample_case_screens.index');
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
		$user_Id = Auth::user()->id;
		$userName = Auth::user()->name;
        $Role_User = DB::table('role_user')->WHERE('user_id', $user_Id)->first();
		if ($Role_User->role_id != 1) {
			$values = DB::table('sample_case_screens')->select($this->listing_cols)->whereNull('deleted_at')->where('customer_name', $userName)->orderBy('id','DESC');
		} else {
			$values = DB::table('sample_case_screens')->select($this->listing_cols)->whereNull('deleted_at')->orderBy('id','DESC');
		}
		$out = Datatables::of($values)->make();
		$data = $out->getData();

		$fields_popup = ModuleFields::getModuleFields('Sample_case_Screens');
		
		for($i=0; $i < count($data->data); $i++) {
			for ($j=0; $j < count($this->listing_cols); $j++) { 
				$col = $this->listing_cols[$j];
				if($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
					$data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
				}
				if($col == $this->view_col) {
					$data->data[$i][$j] = '<a href="'.url(config('laraadmin.adminRoute') . '/sample_case_screens/'.$data->data[$i][0]).'">'.$data->data[$i][$j].'</a>';
				}
				// else if($col == "author") {
				//    $data->data[$i][$j];
				// }
			}
			
			if($this->show_action) {
				$output = '';
				if(Module::hasAccess("Sample_case_Screens", "edit")) {
					$output .= '<a href="'.url(config('laraadmin.adminRoute') . '/sample_case_screens/'.$data->data[$i][0].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
				}
				
				if(Module::hasAccess("Sample_case_Screens", "delete")) {
					$output .= Form::open(['route' => [config('laraadmin.adminRoute') . '.sample_case_screens.destroy', $data->data[$i][0]], 'method' => 'delete', 'onsubmit'=> 'return confirm("消去してもよろしいですか?")',  'style'=>'display:inline']);
					$output .= ' <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-times"></i></button>';
					$output .= Form::close();
				}
				$data->data[$i][] = (string)$output;
			}
		}
		$out->setData($data);
		return $out;
	}
}
