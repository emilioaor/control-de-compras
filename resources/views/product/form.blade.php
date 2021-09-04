@extends('layouts.app')

@section('content')
    <product-form
        :edit-data = "{{ isset($product) ? json_encode($product) : 'null' }}"
        :brands = "{{ json_encode($brands) }}"
    ></product-form>
@endsection
