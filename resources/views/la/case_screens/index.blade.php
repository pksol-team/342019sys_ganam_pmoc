@extends("la.layouts.app")

@section("contentheader_title", "Case Screens")
@section("contentheader_description", "Case Screens listing")
@section("section", "Case Screens")
@section("sub_section", "Listing")
@section("htmlheader_title", "Case Screens Listing")

@section("headerElems")
@la_access("Case_Screens", "create")
	<button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#AddModal">Add Case Screen</button>
@endla_access
@endsection

@section("main-content")

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="box box-success">
	<!--<div class="box-header"></div>-->
	<div class="box-body">
		<table id="example1" class="table table-bordered">
		<thead>
		<tr class="success">
			@foreach( $listing_cols as $col )
			<th>{{ $module->fields[$col]['label'] or ucfirst($col) }}</th>
			@endforeach
			@if($show_actions)
			<th>Actions</th>
			@endif
		</tr>
		</thead>
		<tbody>
			
		</tbody>
		</table>
	</div>
</div>

@la_access("Case_Screens", "create")
<div class="modal fade" id="AddModal" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Add Case Screen</h4>
			</div>
			{!! Form::open(['action' => 'LA\Case_ScreensController@store', 'id' => 'case_screen-add-form']) !!}
			<div class="modal-body">
				<div class="box-body">
                    @la_form($module)
					
					{{--
					@la_input($module, 'customer_name')
					@la_input($module, 'record_type')
					@la_input($module, 'case_name')
					@la_input($module, 'task_name')
					@la_input($module, 'grant_total')
					@la_input($module, 'target_name')
					@la_input($module, 'content_preparation')
					@la_input($module, 'project_proposal_day')
					@la_input($module, 'expiration_date')
					@la_input($module, 'application_amount')
					@la_input($module, 'scheduled_date_1')
					@la_input($module, 'scheduled_date_2')
					@la_input($module, 'scheduled_date_3')
					@la_input($module, 'stop')
					@la_input($module, 'reserved')
					@la_input($module, 'case_close_check')
					@la_input($module, 'remarks')
					@la_input($module, 'final_update_date')
					--}}
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				{!! Form::submit( 'Submit', ['class'=>'btn btn-success']) !!}
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endla_access

@endsection

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('la-assets/plugins/datatables/datatables.min.css') }}"/>
@endpush

@push('scripts')
<script src="{{ asset('la-assets/plugins/datatables/datatables.min.js') }}"></script>
<script>
$(function () {
	$("#example1").DataTable({
		processing: true,
        serverSide: true,
        ajax: "{{ url(config('laraadmin.adminRoute') . '/case_screen_dt_ajax') }}",
		language: {
			lengthMenu: "_MENU_",
			search: "_INPUT_",
			searchPlaceholder: "Search"
		},
		@if($show_actions)
		columnDefs: [ { orderable: false, targets: [-1] }],
		@endif
	});
	$("#case_screen-add-form").validate({
		
	});
});
</script>
@endpush
