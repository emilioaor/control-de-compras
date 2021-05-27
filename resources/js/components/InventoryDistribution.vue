<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> {{ t('menu.inventoryDistribution') }}
            </div>
            <div class="card-body">

                <div class="mb-4" v-for="pr in prs">
                    <h5>
                        <strong>{{ pr.seller.name }} <small>({{ pr.created_at | date(true) }})</small></strong>
                    </h5>

                    <table class="table">
                        <tr>
                            <th>{{ t('validation.attributes.product') }}</th>
                            <th>{{ t('validation.attributes.ordered') }}</th>
                            <th>{{ t('validation.attributes.available') }}</th>
                            <th>{{ t('validation.attributes.approved') }}</th>
                        </tr>
                        <tr v-for="product in pr.product.same_model">
                            <td>
                                {{ product.description }}
                            </td>
                            <td>
                                <input type="number" class="form-control" disabled :value="product.id === pr.product_id ? pr.qty : 0">
                            </td>
                            <td>
                                <input type="number" class="form-control" disabled :value="getInventoryAvailable(product.id)">
                            </td>
                            <td>
                                <input type="number" class="form-control" v-model="product.approved">
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'inventory-distribution',
        props: {
            purchaseRequests: {
                type: Array,
                required: true
            },
            inventory: {
                type: Array,
                required: true
            }
        },

        data() {
            return {
                prs: []
            }
        },

        mounted() {
            this.prs = this.purchaseRequests.map(pr => {
                return {
                    ...pr,
                    product: {
                        ...pr.product,
                        same_model: pr.product.same_model.map(sm => {
                            return {
                                ...sm,
                                approved: 0
                            }
                        })
                    }
                }
            })
        },

        methods: {
            getInventoryAvailable(productId) {
                const inventory = this.inventory.find(i => i.product_id === productId)
                const available = inventory ? inventory.m_qty : 0;
                const consumed = this.prs.reduce((total, current) => {

                    const product = current.product.same_model.find(p => p.id === productId);
                    const approved = product ? product.approved : 0;

                    return total + (!isNaN(parseInt(approved)) ? parseInt(approved) : 0)
                }, 0)

                return available - consumed;
            }
        }
    }
</script>
