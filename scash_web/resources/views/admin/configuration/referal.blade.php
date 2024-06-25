@extends('layout/main')

@section('title', 'Referral Fees - Configuration')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0">Referral / <span>Configuration</span></h1>
	</div>

	<!-- Content Row -->
	<div class="card video_tab shadow mb-4">
		<div class="card-header user_list py-3">
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
					<div class="card-body p-0">
						<form id="referral-form" method="POST" action="{{route('admin.configuration.referral.save')}}" enctype="multipart/form-data">
							@csrf
							<div class="row">
								<div class="col-md-6">
									<div class="form-group input-group-outline mt-3" id="imageInput">
										<label for="referral" class="form-label">Referral Amount</label>
										<input class="form-control" name="referral" type="number" value="{{$detail ? $detail->config_value : ''}}">
										<strong class="text-danger is-invalid" id="referral"></strong>
									</div>	
								</div>
								<div class="col-md-6">
									<div class="form-group input-group-outline mt-3" id="imageInput">
										<label for="platformFee" class="form-label">Transaction Min Amount</label>
										<input class="form-control" name="referral_min_amount" type="text" value="{{$referral_min_amount ? $referral_min_amount->config_value : ''}}">
										<strong class="text-danger is-invalid" id="referral_min_amount"></strong>
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
<script src="{{ asset('assets') }}/js/admin/referral.js"></script>

@endpush