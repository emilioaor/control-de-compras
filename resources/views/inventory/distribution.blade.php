@extends('layouts.app')

@section('content')
    <inventory-distribution
        :purchase-requests = "{{ json_encode($purchaseRequests) }}"
        :inventory = "{{ json_encode($inventory) }}"
    ></inventory-distribution>
@endsection
