@extends('layouts.app')

@section('content')
    <brand-list
        :items = "{{ json_encode($brands->items()) }}"
        :total = "{{ $brands->total() }}"
    >
        <template v-slot:pagination>{{ $brands->links() }}</template>
    </brand-list>
@endsection
