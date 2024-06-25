@extends('layout/main')

@section('title', 'Store Transaction')

@section('content')

<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Store Transaction</h1>
  </div>

  <!-- Content Row -->
  <div class="card shadow mb-4">
    <div class="card-header user_list py-3">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row align-items-center">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-10">
                        <h4 class="my-4 text-gray-600">Balance ${{$storeModel->wallet_balance}}</h4>
                    </div>
                    <div class="col-md-2 text-right">
                        <div class="btn_flex">
                            <div class="form_card">
                                <div id="qrcode"></div>
                            </div>
                          </div>
                      </div>
                </div>
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
        <h6 class="m-0 font-weight-bold text-primary">Store transactions</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered dataTable-table" id="store-table" width="100%" cellspacing="0">
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


@endsection

@push('style')
<link href="{{ asset('/asset/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endpush

@push('js')
<script src="{{ asset('/asset/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/asset/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Page level custom scripts -->
<script src="{{ asset('/asset/js/demo/datatables-demo.js') }}"></script>

<script src="{{ asset('assets') }}/js/admin/store-transaction.js"></script>
<script>
	var store_transaction_datatable_url = "{{ route('merchant.store.transactionTable', ['id' => $id]) }}";
    var transfer_data = "{{ $id }}";

</script>

<script src= "https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script> var qrcode = new QRCode("qrcode", "{{$userModel->uuid}}"); </script>
<script src="{{ asset('assets') }}/js/merchant/profile.js"></script>


@endpush