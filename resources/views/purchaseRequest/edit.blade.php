@extends('layouts.app')

@section('content')
    <purchase-request-group-form
        :edit-data = "{{ json_encode($purchaseRequestGroup) }}"
        purchase-type = "purchase-request"
    ></purchase-request-group-form>
@endsection
