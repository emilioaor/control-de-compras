<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> {{ t('menu.purchases') }}
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
                        <th>{{ t('validation.attributes.number') }}</th>
                        <th>{{ t('validation.attributes.product') }}</th>
                        <th>{{ t('validation.attributes.buyer') }}</th>
                        <th class="text-center">{{ t('validation.attributes.qty') }}</th>
                        <th class="text-center">{{ t('validation.attributes.status') }}</th>
                        <th width="5%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in items" :key="item.id">
                        <td>{{ item.created_at |date(true) }}</td>
                        <td>{{ item.number }}</td>
                        <td>{{ item.purchases.map(p => p.product.model).filter((m, i, a) => a.indexOf(m) === i).join(', ') }}</td>
                        <td>{{ item.buyer.name }}</td>
                        <td class="text-center">{{ item.purchases.reduce((total, p) => total + p.qty, 0) }}</td>
                        <td class="text-center">
                            <span
                                class="p-1 rounded d-inline-block"
                                :class="{
                                    'bg-info' : item.status === 'open',
                                    'bg-secondary text-white': item.status === 'closed'
                                }"
                            >
                                {{ t('status.' + item.status) }}
                            </span>
                        </td>
                        <td>
                            <a :href="'/buyer/purchase/' + item.uuid + '/edit'" class="btn btn-warning">
                                <i class="fa fa-eye"></i>
                            </a>
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
        name: 'purchase-list',
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
