@extends('layout/main')

@section('title', 'Transaction Limit - Configuration')

@section('content')
<!-- Begin Page Content -->
<div class="container-fluid">
	<!-- Page Heading -->
	<div class="d-sm-flex align-items-center justify-content-between mb-4">
		<h1 class="h3 mb-0">Transaction Limit / <span>Configuration</span></h1>
	</div>

	<!-- Content Row -->
	<div class="card video_tab shadow mb-4">
		<div class="card-header user_list py-3">
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
					<div class="card-body p-0">
						<form id="transactionLimit-form" method="POST" action="{{route('admin.configuration.transactionLimit.save')}}" enctype="multipart/form-data">
							@csrf
							<div class="row">
								<div class="col-md-6">
									<div class="form-group input-group-outline mt-3" id="imageInput">
										<label for="transactionLimit" class="form-label">Transaction Limit (In the single transition)</label>
										<input class="form-control" name="transaction_limit" type="number" value="{{$transaction_limit ? $transaction_limit->config_value : ''}}">
										<strong class="text-danger is-invalid" id="transaction_limit"></strong>
									</div>	
								</div>
								<div class="col-md-6">
									<div class="form-group input-group-outline mt-3" id="imageInput">
										<label for="transactionLimit" class="form-label">Full Day Transaction Limit</label>
										<input class="form-control" name="full_day_transaction_limit" type="number" value="{{$full_day_transaction_limit ? $full_day_transaction_limit->config_value : ''}}">
										<strong class="text-danger is-invalid" id="full_day_transaction_limit"></strong>
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
<script src="{{ asset('assets') }}/js/admin/transactionLimit.js"></script>

@endpush