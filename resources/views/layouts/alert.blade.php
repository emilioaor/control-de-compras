@if(Session::has('alert-type') && Session::has('alert-message'))
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 alert alert-{{ Session::get('alert-type') }} alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <strong>{{ __('alert.attention') }}</strong> {{ Session::get('alert-message') }}
            </div>
        </div>
    </div>
@endif