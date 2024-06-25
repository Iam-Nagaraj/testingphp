@extends('layout/main')

@section('title', 'Deposit Money')

@section('content')

<div class="container-fluid">
  <!-- Page Heading -->
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Deposit Money</h1>
  </div>

  <!-- Content Row -->
  <div class="card shadow mb-4">
    <div class="card-header user_list py-3">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row border-bottom mb-4 align-items-center">
            <div class="col-md-9">
              <h4 class="my-4 text-gray-600">Which Bank Account you want to connect with Scash!</h4>
            </div>
            <div class="col-md-3 text-right">
              <button id="plaid-link-btn" class="btn btn-primary">Connect New Bank Account</button>
            </div>
          </div>
          <div>
            <div id="account_list" class="row bank_account_list">
              @foreach($account_list as $singleAccount)
              @if($singleAccount->is_connected == false)
              <div class="card-body col-md-3" id="account_id">
                <div class="table-responsive">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Bank Account</h5>
                            <p class="card-text">{{$singleAccount->user_account_name ?? $singleAccount->name}}</p>
                            <h6 class="card-text">#### #### #### {{$singleAccount->mask}}</h6>
                        </div>
                        <button class="btn btn-primary" onclick='connectToScash("{{$singleAccount->account_id}}", "{{$singleAccount->access_token}}", "{{$singleAccount->user_account_name ?? $singleAccount->name}}")' >Connect</button>
                    </div>
                </div>
              </div>
              @endif
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header user_list py-3">
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
          <div class="row border-bottom mb-4 align-items-center">
            <div class="col-md-9">
              <h4 class="my-4 text-gray-600">Connected Bank Accounts!</h4>
            </div>
          </div>
          <div>
            <div id="bank_account_list" class="row bank_account_list">

              <div class="col-md-12 text-center py-3">
                <div class="spinner-grow text-secondary" id="bank_list_loader" role="status" style="width: 3rem; height: 3rem;">
                  <span class="sr-only">Loading...</span>
                </div>
              </div>
                
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
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
<!-- Modal -->
<div class="modal fade" id="depositModel" tabindex="-1" aria-labelledby="depositModelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered custom_moddal">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title fs-5" id="depositModelLabel">Sending Money to Wallet</h2>
      </div>
      <form id="deposit-wallet-form" method="POST" action="{{route('merchant.bank.depositToWallet')}}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="account_id" id="form_account_id">
        <div class="modal-body">
          <div class="form-group">
          <label class="control-label my-2">Standard transfer timeline of 2-3 banking days by using Same Day transfer it will take 24 hours banking time.</label>
            <input type="number" name="amount" class="form-control amount" placeholder="Enter Amount">
            <strong class="text-danger is-invalid" id="amount"></strong>
            
            <input type="number" name="pin" class="form-control pin mt-3" placeholder="Enter Pin" maxlength="4"
            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
            <strong class="text-danger is-invalid" id="pin"></strong>
            
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
<x-loader-ajax-component></x-loader-ajax-component>
@endsection

@push('style')


@endpush

@push('js')

<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
<script>
  var link_token = "{{$link_token['link_token']}}";
  var token_url = "{{route('merchant.bank.accessToken')}}";
  var plaid_dwolla = "{{route('merchant.bank.plaidDwollaToken')}}"
  var bankListUrl = "{{route('merchant.bank.getBankList')}}"

</script>
<script src="{{ asset('assets') }}/js/merchant/plaid.js"></script>


@endpush
