@extends('layouts.app')

@section('content')
    <brand-form
        :edit-data = "{{ isset($brand) ? json_encode($brand) : 'null' }}"
    ></brand-form>
@endsection
