@extends('layouts.app')

@section('content')
    <supplier-form
        :edit-data = "{{ isset($supplier) ? json_encode($supplier) : 'null' }}"
    ></supplier-form>
@endsection
