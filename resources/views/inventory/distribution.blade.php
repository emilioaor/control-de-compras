@extends('layouts.app')

@section('content')
    <inventory-distribution
        :edit-data = "{{ json_encode($purchaseRequests) }}"
        :inventories = "{{ json_encode($inventory) }}"
    ></inventory-distribution>
@endsection
