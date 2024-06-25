@extends('layout/blank')

@section('title', 'Merchant Login')

@section('content')
<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">

      <!-- Login -->
      <div class="card p-2">

        <div class="card-body mt-2">
          <h4 class="mb-2">Welcome to {{config('variables.templateName')}}! 👋</h4>

          <button id="plaid-link-btn" class="btn btn-primary">Connect Bank Account</button>


          
        </div>
      </div>
      <!-- /Login -->
    </div>
  </div>
</div>
@endsection

@push('style')
<style>

</style>
@endpush

@push('js')
<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>

<script>
  // Public Token: public-sandbox-44f688e9-aaad-4dba-8c12-86291950fd8f
var handler = Plaid.create({
    token: 'link-sandbox-2c6973ae-8a88-4aa4-8155-061a06b0fbde',
    onSuccess: function (publicToken, metadata) {
        // Handle the success event, e.g., send the publicToken to your server
        console.log('Public Token:', publicToken);
    },
    onExit: function (err, metadata) {
        // Handle when the user exits Link, e.g., cancel or encounter an error
        console.log('Exit Event:', err);
    },
});

// Trigger Plaid Link when the button is clicked
document.getElementById('plaid-link-btn').onclick = function () {
    handler.open();
};
</script>

@endpush
