<!-- Main Header -->
<header class="main-header">
	<?php $UserId = Auth::user()->id;

	$RoleTable = DB::table('role_user')->WHERE('user_id', $UserId)->first();
	if ($RoleTable->role_id != 1) { ?>
		@push('scripts')
		<script>
			$('ul.sidebar-menu li:nth-child(3)').css('display', 'none');
		</script>
		@endpush
	<?php } ?>

	@if(LAConfigs::getByKey('layout') != 'layout-top-nav')
	<!-- Logo -->
	<a href="{{ url(config('laraadmin.adminRoute')) }}" class="logo" style="text-align: left">
		<!-- mini logo for sidebar mini 50x50 pixels -->
		<!-- <span class="logo-mini"><b>{{ LAConfigs::getByKey('sitename_short') }}</b></span> -->
		<!-- logo for regular state and mobile devices -->
		<!-- <span class="logo-lg"><b>{{ LAConfigs::getByKey('sitename_part1') }}</b> -->
		 <!-- {{ LAConfigs::getByKey('sitename_part2') }}</span> -->
		 <img src="/la-assets/img/logo-small.png" width="175" class="img" height="45" alt="Marhaba Logistics" />

	</a>
	@endif

	<!-- Header Navbar -->
	<nav class="navbar navbar-static-top" role="navigation">
	@if(LAConfigs::getByKey('layout') == 'layout-top-nav')
		<div class="container">
			<div class="navbar-header">
				<a href="{{ url(config('laraadmin.adminRoute')) }}" class="navbar-brand"><b>{{ LAConfigs::getByKey('sitename_part1') }}</b>{{ LAConfigs::getByKey('sitename_part2') }}</a>
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
					<i class="fa fa-bars"></i>
				</button>
			</div>
			@include('la.layouts.partials.top_nav_menu')
			@include('la.layouts.partials.notifs')
		</div><!-- /.container-fluid -->
	@else
		<!-- Sidebar toggle button-->
		<a href="#" class="sidebar-toggle b-l" data-toggle="offcanvas" role="button">
			<span class="sr-only">Toggle navigation</span>
		</a>
		@include('la.layouts.partials.notifs')
	@endif
	
	</nav>
</header>
