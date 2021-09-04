@extends('layouts.app')

@section('content')
    <purchase-request-group-form
        :edit-data = "{{ json_encode($purchaseGroup) }}"
        purchase-type = "purchase"
        :user = "{{ json_encode(Auth::user()) }}"
    ></purchase-request-group-form>
@endsection
