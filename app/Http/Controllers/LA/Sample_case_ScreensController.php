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
	public $listing_cols = ['id', 'customer_name', 'record_type', 'case_name', 'grant_total', 'target_name', 'content_preparation', 'project_proposal_day', 'expiration_date', 'application_amount', 'scheduled_date_1', 'scheduled_date_2', 'scheduled_date_3', 'stop', 'reserved', 'case_close_check', 'remarks', 'final_update_date'];
	
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
		if (Auth::user()->id == 1) {
			$module = Module::get('Sample_case_Screens');
				return View('la.sample_case_screens.import', [
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
		            	foreach ($collections as $key => $collection) {

		            		if ($key == 0)
		        			continue;
		        			$newStop = ($collection[13] == true) ? 'True': 'False';
		        			$newReserved = ($collection[14] == true) ? 'True': 'False';
		        			$newCase_close_check = ($collection[15] == true) ? 'True': 'False';

		            		$row = [
				              'customer_name' => $collection[0],
				              'record_type' => $collection[1],
				              'case_name' => $collection[2],
				              'task_name' => $collection[3],
				              'grant_total' => $collection[4],
				              'target_name' => $collection[5],
				              'content_preparation' => $collection[6],
				              'application_amount' => $collection[9],
				              'stop' => $newStop,
				              'reserved' => $newReserved,
				              'case_close_check' => $newCase_close_check,
				              'remarks' => $collection[16]
		                    ];


		            		if (is_object($collection[7]) && get_class($collection[7]) == 'DateTime') {
			            		$row['project_proposal_day'] = $collection[7]->format('Y-m-d');
		                	} else {
		                		$row['project_proposal_day'] = '';
		                	}
		                	if (is_object($collection[8]) && get_class($collection[8]) == 'DateTime') {
			            		$row['expiration_date'] = $collection[8]->format('Y-m-d');
		                	} else {
		                		$row['expiration_date'] = '';
		                	}
		                	if (is_object($collection[10]) && get_class($collection[10]) == 'DateTime') {
			            		$row['scheduled_date_1'] = $collection[10]->format('Y-m-d');
		                	} else {
		                		$row['scheduled_date_1'] = '';
		                	}
		                	if (is_object($collection[11]) && get_class($collection[11]) == 'DateTime') {
			            		$row['scheduled_date_2'] = $collection[11]->format('Y-m-d');
		                	} else {
		                		$row['scheduled_date_2'] = '';
		                	}
		                	if (is_object($collection[12]) && get_class($collection[12]) == 'DateTime') {
			            		$row['scheduled_date_3'] = $collection[12]->format('Y-m-d');
		                	} else {
		                		$row['scheduled_date_3'] = '';
		                	}
		                	if (is_object($collection[17]) && get_class($collection[17]) == 'DateTime') {
			            		$row['final_update_date'] = $collection[17]->format('Y-m-d');
		                	} else {
		                		$row['final_update_date'] = '';
		                	}

		            		$insert[] = $row;

		                }
		                if(!empty($insert)){

	                        $insertData = DB::table('sample_case_screens')->insert($insert);
	                        if ($insertData) {
	                            Session::flash('success', 'あなたのデータは正常にインポートされました');
	                        }else {                        
	                            Session::flash('error', 'データ挿入エラー..');
	                            return back();
	                        }
		                }

	            	} else {
		                Session::flash('error', 'あなたのファイルは空です..!!');
	            	}
	                return back();
	 
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
			$values = DB::table('sample_case_screens')->select($this->listing_cols)->whereNull('deleted_at')->where([['customer_name', $userName], ['item_close', 'null']])->orderBy('id','DESC');
		} else {
			$values = DB::table('sample_case_screens')->select($this->listing_cols)->whereNull('deleted_at')->where('item_close', 'null')->orderBy('id','DESC');
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

	public function dtajax2()
	{
		$user_Id = Auth::user()->id;
		$userName = Auth::user()->name;
        $Role_User = DB::table('role_user')->WHERE('user_id', $user_Id)->first();
		if ($Role_User->role_id != 1) {
			$values = DB::table('sample_case_screens')->select($this->listing_cols)->whereNull('deleted_at')->where([['customer_name', $userName], ['item_close', 'クローズ']])->orderBy('id','DESC');
		} else {
			$values = DB::table('sample_case_screens')->select($this->listing_cols)->whereNull('deleted_at')->where('item_close', 'クローズ')->orderBy('id','DESC');
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
