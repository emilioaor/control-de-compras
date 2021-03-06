<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> {{ t('menu.purchaseRequests') }}
            </div>
            <div class="card-body">
                <table-filter>
                    <template v-slot:filter>
                        <div class="w-100 pl-2">
                            <select
                                name="seller"
                                id="seller"
                                class="form-control"
                                v-model="seller"
                            >
                                <option :value="null">{{ t('form.allSellers') }}</option>
                                <option
                                    v-for="seller in sellers"
                                    :key="seller.id"
                                    :value="seller.id"
                                >{{ seller.name }}</option>
                            </select>
                        </div>
                    </template>

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
                        <th>{{ t('validation.attributes.model') }}</th>
                        <th>{{ t('validation.attributes.seller') }}</th>
                        <th>{{ t('validation.attributes.customer') }}</th>
                        <th class="text-center">{{ t('validation.attributes.ordered') }}</th>
                        <th class="text-center">{{ t('validation.attributes.approved') }}</th>
                        <th class="text-center">{{ t('validation.attributes.excel') }}</th>
                        <th width="5%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in itemsC" :key="item.id">
                        <td>{{ item.created_at |date(true) }}</td>
                        <td>{{ item.number }}</td>
                        <td>{{ item.purchase_requests.map(pr => pr.product.model).filter((m, i, a) => a.indexOf(m) === i).join(', ') }}</td>
                        <td>{{ item.seller.name }}</td>
                        <td>{{ item.customer_name }}</td>
                        <td class="text-center">{{ item.purchase_requests.reduce((total, pr) => total += pr.qty, 0) }}</td>
                        <td class="text-center">{{ item.purchase_movements.reduce((total, pm) => total += pm.qty * -1, 0) }}</td>
                        <td class="text-center">{{ item.excel_downloaded ? t('form.yes') : t('form.no') }}</td>
                        <td>
                            <a v-if="user.role === 'buyer'" :href="'/buyer/purchase-request/' + item.uuid + '/edit'" class="btn btn-warning">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a v-else :href="'/seller/purchase-request/' + item.uuid + '/edit'" class="btn btn-warning">
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
            },
            sellers: {
                type: Array,
                required: true
            },
        },

        data() {
            return {
                seller: null
            }
        },

        computed: {
            itemsC() {
                return this.items.filter(item => ! this.seller || this.seller === item.seller.id);
            }
        }
    }
</script>
