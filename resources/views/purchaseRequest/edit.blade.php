@extends('layouts.app')

@section('content')
    <purchase-request-group-form
        :edit-data = "{{ json_encode($purchaseRequestGroup) }}"
        :models-not-found = "{{ json_encode($modelsNotFound) }}"
        :inventory = "{{ json_encode($inventory) }}"
        purchase-type = "purchase-request"
    ></purchase-request-group-form>
@endsection
