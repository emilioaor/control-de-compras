<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> {{ t('menu.products') }}
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
                        <th>{{ t('validation.attributes.upc') }}</th>
                        <th>{{ t('validation.attributes.model') }}</th>
                        <th>{{ t('validation.attributes.description') }}</th>
                        <th>{{ t('validation.attributes.createdAt') }}</th>
                        <th width="5%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in items" :key="item.id">
                        <td>{{ item.upc }}</td>
                        <td>{{ item.model }}</td>
                        <td>{{ item.description }}</td>
                        <td>{{ item.created_at |date(true) }}</td>
                        <td>
                            <a :href="'/buyer/product/' + item.uuid + '/edit'" class="btn btn-warning">
                                <i class="fa fa-edit"></i>
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
        name: 'product-list',
        props: {
            items: {
                type: Array,
                required: true
            },

            total: {
                type: Number,
                required: false
            }
        }
    }
</script>
