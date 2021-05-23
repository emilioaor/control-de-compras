@extends('layouts.app')

@section('content')
    <product-form
        :edit-data = "{{ isset($product) ? json_encode($product) : 'null' }}"
    ></product-form>
@endsection
