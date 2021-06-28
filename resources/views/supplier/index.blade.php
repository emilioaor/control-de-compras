@extends('layouts.app')

@section('content')
    <supplier-list
        :items = "{{ json_encode($suppliers->items()) }}"
        :total = "{{ $suppliers->total() }}"
    >
        <template v-slot:pagination>{{ $suppliers->links() }}</template>
    </supplier-list>
@endsection
