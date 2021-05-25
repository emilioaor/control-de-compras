@extends('layouts.app')

@section('content')
    <inventory-list
        :items = "{{ json_encode($inventory) }}"
        :total = "{{ count($inventory) }}"
        :user = "{{ json_encode(Auth::user()) }}"
    >
    </inventory-list>
@endsection
