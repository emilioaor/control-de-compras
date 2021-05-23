@extends('layouts.app')

@section('content')
    <product-list
        :items = "{{ json_encode($products->items()) }}"
        :total = "{{ $products->total() }}"
    >
        <template v-slot:pagination>{{ $products->links() }}</template>
    </product-list>
@endsection
