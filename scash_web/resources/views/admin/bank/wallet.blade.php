@extends('layout/main')

@section('title', 'Wallet')

@section('content')

<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Your Wallet</h1>
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
            <div class="col-md-4 text-right d-flex flex-row">
              <button class="btn btn-success" onclick="depositPopUp('{{$bank_id}}')"><b>Deposit To Wallet</b></button>

              <button id="plaid-link-btn" class="btn btn-primary ml-2" onClick="withdrawPopUp()" >Send Withdrawal Request</button>
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
        <h6 class="m-0 font-weight-bold text-primary">Wallet transactions</h6>
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
<!-- Modal -->
<div class="modal fade" id="successModel" tabindex="-1" aria-labelledby="successModelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered custom_moddal">
    <div class="modal-content">
        <div class="modal-body">
          <div class="inner_modal text-center">
            <img src="{{asset('/asset/img/payment-success.png')}}">
            <h3>Payment Successfull!</h3>
            <button type="button" class="btn btn-secondary" onclick="window.location.reload();">Done</button>
          </div>
        </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="withdrawModel" tabindex="-1" aria-labelledby="withdrawModelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered custom_moddal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title fs-5" id="withdrawModelLabel">Withdraw from Wallet</h2>
      </div>
      <form id="withdraw-wallet-form" method="POST" action="{{route('admin.bank.withdrawFromWallet')}}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="form-group">
          <label class="control-label my-2">Standard transfer timeline of 2-3 banking days.</label>
            <input type="number" name="amount" class="form-control amount" placeholder="Enter Amount">
            <strong class="text-danger is-invalid" id="_amount"></strong>

            <input type="number" name="pin" class="form-control pin mt-3" placeholder="Enter Pin" maxlength="4"
            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
            <strong class="text-danger is-invalid" id="_pin"></strong>
          </div>
          <strong class="text-danger is-invalid" id="_destination_id"></strong>
          <strong class="text-danger is-invalid" id="_all_errors"></strong>
          <div id="bank_account_list" class="row bank_account_list">

            <div class="col-md-12 text-center py-3">
              <div class="spinner-grow text-secondary" id="bank_list_loader" role="status" style="width: 3rem; height: 3rem;">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
              
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Withdraw</button>
          <button type="button" class="btn btn-secondary" onclick="$('#withdrawModel').modal('hide')">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="depositModel" tabindex="-1" aria-labelledby="depositModelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered custom_moddal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title fs-5" id="depositModelLabel">Sending Money to Wallet</h2>
      </div>
      <form id="deposit-wallet-form" method="POST" action="{{route('admin.bank.depositToWallet')}}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="account_id" id="form_account_id">
        <div class="modal-body">
          <div class="form-group">
          <label class="control-label my-2">Standard transfer timeline of 2-3 banking days by using Same Day transfer it will take 24 hours banking time.</label>
            <input type="number" name="amount" class="form-control amount" placeholder="Enter Amount">
            <strong class="text-danger is-invalid" id="amount"></strong>
            
            <input type="number" name="pin" class="form-control pin mt-3" placeholder="Enter Pin" maxlength="4"
            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
            <strong class="text-danger is-invalid" id="pin"></strong><br>
            
            <strong class="text-danger is-invalid" id="payment_type"></strong>
            <strong class="text-danger is-invalid" id="all_errors"></strong>

            <div class="row">
              <div class="col-md-12 mt-3">
                <label class="control-label">Choose Payment Type</label>
              </div>
              <div class="card-body col-md-12">
                  <div class="table-responsive">
                      <div class="card">
                          <div class="card-body">
                              <div class="row">
                                  <input class="col-md-1" type="radio" value="manual" name="payment_type" id="manual">
                                  <label class="card-text col-md-10" for="manual">Standard <span>(2-3 days to transfer) </span> </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="card-body col-md-12">
                  <div class="table-responsive">
                      <div class="card">
                          <div class="card-body">
                              <div class="row">
                                  <input class="col-md-1" type="radio" value="instant" name="payment_type" id="instant">
                                  <label class="card-text col-md-10" for="instant">Same Day <span>(24 hours) </span> </label>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Add</button>
          <button type="button" class="btn btn-secondary" onclick="$('#depositModel').modal('hide')">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="successModel" tabindex="-1" aria-labelledby="successModelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered custom_moddal">
    <div class="modal-content">
        <div class="modal-body">
          <div class="inner_modal text-center">
            <img src="{{asset('/asset/img/payment-success.png')}}">
            <h3>Payment Successfull!</h3>
            <button type="button" class="btn btn-secondary" onclick="window.location.reload();">Done</button>
          </div>
        </div>
    </div>
  </div>
</div>
@endsection

@push('style')
<link href="{{ asset('/asset/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@push('js')
<script>
	var transaction_datatable_url = "{{ route('admin.bank.walletTransaction') }}";
  var bankListUrl = "{{route('admin.bank.getBankList')}}"
</script>
<script src="{{ asset('/asset/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/asset/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets') }}/js/admin/wallet.js"></script>
  

@endpush
