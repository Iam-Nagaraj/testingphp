@extends('layout/main')

@section('title', 'ACH Fee - Configuration')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0">ACH Fee / <span>Configuration</span></h1>
	</div>

	<!-- Content Row -->
	<div class="card video_tab shadow mb-4">
		<div class="card-header user_list py-3">
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
					<div class="card-body p-0">
						<form id="platformFee-form" method="POST" action="{{route('admin.configuration.achFee.save')}}" enctype="multipart/form-data">
							@csrf
							<div class="row">
								<div class="col-md-6">
									<div class="form-group input-group-outline mt-3" id="imageInput">
										<label for="platformFee" class="form-label">Standard ACH Fee Amount</label>
										<input class="form-control" name="manual_platform_fee" type="text" value="{{$manual_platform_fee ? $manual_platform_fee->config_value : ''}}">
										<strong class="text-danger is-invalid" id="manual_platform_fee"></strong>
									</div>	
								</div>
								<div class="col-md-6">
									<div class="form-group input-group-outline mt-3" id="imageInput">
										<label for="platformFee" class="form-label">Same Day ACH Fee Amount</label>
										<input class="form-control" name="instant_platform_fee" type="text" value="{{$instant_platform_fee ? $instant_platform_fee->config_value : ''}}">
										<strong class="text-danger is-invalid" id="instant_platform_fee"></strong>
									</div>	
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<button type="submit">Save Changes</button>
								</div>
							</div>
						</form>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
<!-- /.container-fluid -->
@endsection
@push('js')
<script src="{{ asset('assets') }}/js/admin/platformFee.js"></script>

@endpush