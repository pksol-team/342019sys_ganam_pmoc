@extends("la.layouts.app")

@section("contentheader_title", "案件")
@section("section", "案件")
@section("htmlheader_title", "ケース画面 リスティング")

@section("headerElems")
@la_access("Sample_case_Screens", "create")
	<button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#AddModal">案件追加画面</button>
@endla_access
@endsection

@section("main-content")
<?php if (Auth::user()->id != 1): ?>
<style>
	table tr td:nth-child(2), table tr td:nth-child(7)  {
	    text-align: right;
	}
</style>

<div class="message-show">
	<div class="message-show-message">
		<p><?= Auth::user()->name ?> 様</p>
		<p>メッセージ</p>
	</div>
	<?php  $CurrentUser = DB::table('employees')->WHERE('id', Auth::user()->id)->first();
	if ($CurrentUser->message_1 != '' || $CurrentUser->message_2 != '' || $CurrentUser->message_3 != ''): ?>
		<div class="message-show-inner">
			<?php 
				if ($CurrentUser->message_1 != '') {
					echo "<p class='message-show-inner-p'>".$CurrentUser->message_1."</p>";
				}
				if ($CurrentUser->message_2 != '') {
					echo "<p class='message-show-inner-p'>".$CurrentUser->message_2."</p>";
				}
				if ($CurrentUser->message_3 != '') {
					echo "<p class='message-show-inner-p'>".$CurrentUser->message_3."</p>";
				}
			?>
		</div>
	<?php endif ?>
</div>
<?php else: ?>
	<style>
		table tr td:nth-child(6), table tr td:nth-child(11)  {
		    text-align: right;
		}
	</style>

<?php endif ?>

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div id="exTab1">
   <ul class="nav nav-tabs">
      <li class="active"><a  href="#completed_tab" data-toggle="tab">進行中</a></li>
      <li><a href="#processing_tab" data-toggle="tab">クローズ</a></li>
   </ul>
   <div class="tab-content ">
      <div class="tab-pane active" id="completed_tab">
         <div class="box box-success">
         	<div class="box-body">
         		<table id="example1" class="table table-bordered">
         		<thead>
         		<tr class="success">
         			@foreach( $listing_cols as $col )
         				<?php if (Auth::user()->id == 1): ?>
				            <th>{{ $module->fields[$col]['label'] or ucfirst($col) }}</th>
						<?php else: ?>
							@if($col != 'id')
					            <th>{{ $module->fields[$col]['label'] or ucfirst($col) }}</th>
					        @endif
         				<?php endif ?>
         			@endforeach
         			@if($show_actions)
         				<?php if (Auth::user()->id == 1): ?>
         					<th>行動</th>
         				<?php endif ?>
         			@endif
         		</tr>
         		</thead>
         		<tbody>
         			
         		</tbody>
         		</table>
         	</div>
         </div>
      </div>
      <div class="tab-pane" id="processing_tab">
        <div class="box box-success">
         <div class="box-body">
     		<table id="example2" class="table table-bordered">
     		<thead>
     		<tr class="success">
     			@foreach( $listing_cols as $col )
     				<?php if (Auth::user()->id == 1): ?>
			            <th>{{ $module->fields[$col]['label'] or ucfirst($col) }}</th>
					<?php else: ?>
						@if($col != 'id')
				            <th>{{ $module->fields[$col]['label'] or ucfirst($col) }}</th>
				        @endif
     				<?php endif ?>
     			@endforeach
     			@if($show_actions)
     				<?php if (Auth::user()->id == 1): ?>
     					<th>行動</th>
     				<?php endif ?>
     			@endif
     		</tr>
     		</thead>
     		<tbody>
     			
     		</tbody>
     		</table>
     	 </div>
     	</div>
      </div>
   </div>
</div>

@la_access("Sample_case_Screens", "create")
<div class="modal fade" id="AddModal" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">ケース追加画面</h4>
			</div>
			{!! Form::open(['action' => 'LA\Sample_case_ScreensController@store', 'id' => 'sample_case_screen-add-form']) !!}
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
				<button type="button" class="btn btn-default" data-dismiss="modal">閉じる</button>
				{!! Form::submit( '提出する', ['class'=>'btn btn-success']) !!}
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
	var user = '<?= Auth::user()->id; ?>';
	if (user != 1) {
		var orderNo = 5;
	} else {
		var orderNo = 9;
	}
	$("#example1").DataTable({
		processing: true,
        serverSide: true,
        ajax: "{{ url(config('laraadmin.adminRoute') . '/sample_case_screen_dt_ajax') }}",
        order: [[ orderNo, "desc" ]],
		language: {
			lengthMenu: "_MENU_",
			search: "_INPUT_",
			searchPlaceholder: "サーチ",
            url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/Japanese.json"
		},
		@if($show_actions)
		columnDefs: [ { orderable: false, targets: [-1] }],
		@endif
	});

	$("#example2").DataTable({
		processing: true,
        serverSide: true,
        ajax: "{{ url(config('laraadmin.adminRoute') . '/sample_case_screen_dt_ajax2') }}",
        order: [[ orderNo, "desc" ]],
		language: {
			lengthMenu: "_MENU_",
			search: "_INPUT_",
			searchPlaceholder: "サーチ",
            url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/Japanese.json"
		},
		@if($show_actions)
		columnDefs: [ { orderable: false, targets: [-1] }],
		@endif
	});
	var user = '<?= Auth::user()->id; ?>';
	if (user != 1) {
		$("#example1, #example2").attr('style', 'width:2068px;');
	} else {
		$("#example1, #example2").attr('style', 'width:4600px;');
	}
	
	$("#sample_case_screen-add-form").validate({});

	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		if (user != 1) {
			$("#example1, #example2").attr('style', 'width:2068px;');
		} else {
			$("#example1, #example2").attr('style', 'width:4600px;');
		}
    });
});
</script>
@endpush
