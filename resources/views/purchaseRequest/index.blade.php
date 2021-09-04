@extends('layouts.app')

@section('content')
    <purchase-request-list
        :items = "{{ json_encode($purchaseRequests->items()) }}"
        :total = "{{ $purchaseRequests->total() }}"
        :user = "{{ json_encode(Auth::user()) }}"
        :sellers = "{{ json_encode($sellers) }}"
    >
        <template v-slot:pagination>{{ $purchaseRequests->links() }}</template>
    </purchase-request-list>
@endsection
