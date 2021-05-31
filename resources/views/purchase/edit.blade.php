@extends('layouts.app')

@section('content')
    <purchase-request-group-form
        :edit-data = "{{ json_encode($purchaseGroup) }}"
        purchase-type = "purchase"
    ></purchase-request-group-form>
@endsection
