@extends("la.layouts.app")

@section("contentheader_title")
	<a href="{{ url(config('laraadmin.adminRoute') . '/case_screens') }}">Case Screen</a> :
@endsection
@section("contentheader_description", $case_screen->$view_col)
@section("section", "Case Screens")
@section("section_url", url(config('laraadmin.adminRoute') . '/case_screens'))
@section("sub_section", "Edit")

@section("htmlheader_title", "Case Screens Edit : ".$case_screen->$view_col)

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

<div class="box">
	<div class="box-header">
		
	</div>
	<div class="box-body">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				{!! Form::model($case_screen, ['route' => [config('laraadmin.adminRoute') . '.case_screens.update', $case_screen->id ], 'method'=>'PUT', 'id' => 'case_screen-edit-form']) !!}
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
                    <br>
					<div class="form-group">
						{!! Form::submit( 'Update', ['class'=>'btn btn-success']) !!} <button class="btn btn-default pull-right"><a href="{{ url(config('laraadmin.adminRoute') . '/case_screens') }}">Cancel</a></button>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script>
$(function () {
	$("#case_screen-edit-form").validate({
		
	});
});
</script>
@endpush
