@extends('layouts.app')

@section('content')
    <inventory-distribution
        :edit-data = "{{ json_encode($purchaseRequests) }}"
        :inventory = "{{ json_encode($inventory) }}"
    ></inventory-distribution>
@endsection
