<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> {{ t('menu.inventory') }}
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="form-group col-sm-5 d-flex align-items-center">
                        <i class="fa fa-filter d-inline-block mr-3"></i>
                        <input type="text" class="form-control" v-model="filter">
                    </div>
                </div>

                <table class="table">
                    <thead>
                    <tr>
                        <th>{{ t('validation.attributes.code') }}</th>
                        <th>{{ t('validation.attributes.product') }}</th>
                        <!--<th class="text-center">{{ t('validation.attributes.ordered') }}</th>-->
                        <th class="text-center">{{ t('validation.attributes.available') }}</th>
                        <!--<th width="5%" class="text-center">{{ t('validation.attributes.balance') }}</th>-->
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in itemsFiltered" :key="item.id">
                        <td>{{ item.upc }}</td>
                        <td>{{ item.description }}</td>
                        <!--<td class="text-center">{{ item.ordered }}</td>-->
                        <td class="text-center">{{ item.qty }}</td>
                        <!--<td class="text-center">
                            <span
                                class="d-block p-1 py-2 rounded"
                                :class="{
                                    'bg-warning': item.balance === 0,
                                    'bg-success text-white': item.balance > 0,
                                    'bg-danger text-white': item.balance < 0
                                }"
                            >
                                {{ item.balance }}
                            </span>
                        </td>-->
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
        name: 'inventory-list',
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
        },

        data() {
            return {
                filter: null
            }
        },

        computed: {
            itemsFiltered() {
                if (! this.filter) {
                    return this.items;
                }

                return this.items.filter(i => {
                    return i.description.toLowerCase().includes(this.filter.toLowerCase()) ||
                        i.upc.toLowerCase().includes(this.filter.toLowerCase())
                });
            }
        }
    }
</script>
