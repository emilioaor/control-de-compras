@extends('layouts.app')

@section('content')
    <purchase-list
        :items = "{{ json_encode($purchases->items()) }}"
        :total = "{{ $purchases->total() }}"
        :user = "{{ json_encode(Auth::user()) }}"
    >
        <template v-slot:pagination>{{ $purchases->links() }}</template>
    </purchase-list>
@endsection
