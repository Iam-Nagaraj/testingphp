@extends('layout/main')

@section('title', 'Wallet')

@section('content')

<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Transactions</h1>
  </div>

  <!-- Content Row -->
  <div class="card shadow mb-4">
    <div class="card-header user_list py-3">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h4 class="my-4 text-gray-600">Balance ${{$walletModel->balance}}</h4>
            </div>
          </div>
          <div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Content Row -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Bank transactions</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered dataTable-table" id="transaction-table" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>Transaction</th>
              <th>Date</th>
              <th>Amount</th>
              <th>Status</th>
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

</div>

<x-loader-ajax-component></x-loader-ajax-component>

@endsection

@push('style')
<link href="{{ asset('/asset/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@push('js')
<script>
	var transaction_datatable_url = "{{ route('admin.bank.transactions') }}";
	var transfer_data = "{{ Auth::user()->id }}";
</script>
<script src="{{ asset('/asset/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/asset/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets') }}/js/admin/transactions.js"></script>
  

@endpush
