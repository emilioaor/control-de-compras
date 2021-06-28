@extends('layouts.app')

@section('content')
    <inventory-distribution
        :edit-data = "{{ json_encode($purchaseRequests) }}"
        :inventories = "{{ json_encode($inventory) }}"
        :models-not-found = "{{ json_encode($modelsNotFound) }}"
    ></inventory-distribution>
@endsection
