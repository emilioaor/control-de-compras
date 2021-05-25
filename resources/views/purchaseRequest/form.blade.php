@extends('layouts.app')

@section('content')
    <purchase-request-form
        :edit-data = "{{ isset($purchaseRequest) ? json_encode($purchaseRequest) : 'null' }}"
        :user = "{{ json_encode(Auth::user()) }}"
        :sellers = "{{ json_encode($sellers) }}"
        purchase-type="purchase-request"
    ></purchase-request-form>
@endsection
