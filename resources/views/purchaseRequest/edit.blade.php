@extends('layouts.app')

@section('content')
    <purchase-request-group-form
        :edit-data = "{{ json_encode($purchaseRequestGroup) }}"
    ></purchase-request-group-form>
@endsection
