@extends('layouts.app')

@section('content')
    <purchase-request-form
        :edit-data = "{{ isset($purchase) ? json_encode($purchase) : 'null' }}"
        :user = "{{ json_encode(Auth::user()) }}"
        :buyers = "{{ json_encode($buyers) }}"
        purchase-type = "purchase"
    ></purchase-request-form>
@endsection
