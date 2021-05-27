<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> {{ t('menu.purchaseRequests') }}
            </div>
            <div class="card-body">
                <table-filter>
                    <template v-slot:total v-if="total">
                        <div class="text-right">
                            <strong>{{ t('form.total') }}:</strong> {{ total }}
                        </div>
                    </template>
                </table-filter>

                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ t('validation.attributes.createdAt') }}</th>
                        <th>{{ t('validation.attributes.model') }}</th>
                        <th>{{ t('validation.attributes.seller') }}</th>
                        <th class="text-center">{{ t('validation.attributes.qty') }}</th>
                        <th class="text-center">{{ t('validation.attributes.approved') }}</th>
                        <th class="text-center">{{ t('validation.attributes.status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in items" :key="item.id">
                        <td>{{ item.created_at |date(true) }}</td>
                        <td>{{ item.purchase_requests.map(pr => pr.product.model).filter((m, i, a) => a.indexOf(m) === i).join(', ') }}</td>
                        <td>{{ item.seller.name }}</td>
                        <td class="text-center">{{ item.purchase_requests.reduce((total, pr) => total += pr.qty, 0) }}</td>
                        <td class="text-center">{{ item.purchase_requests.reduce((total, pr) => total += pr.approved, 0) }}</td>
                        <td class="text-center">
                            <span
                                class="p-1 rounded"
                                :class="{
                                    'bg-info' : item.status === 'pending',
                                    'bg-success text-white': item.status === 'processed'
                                }"
                            >
                                {{ t('status.' + item.status) }}
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <div>
                    <slot name="pagination"></slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'purchase-request-list',
        props: {
            items: {
                type: Array,
                required: true
            },

            total: {
                type: Number,
                required: false
            },
            user: {
                type: Object,
                required: true
            }
        }
    }
</script>
