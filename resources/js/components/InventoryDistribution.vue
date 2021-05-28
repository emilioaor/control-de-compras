<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> {{ t('menu.inventoryDistribution') }}
            </div>
            <div class="card-body">

                <template v-for="inv in inventory">
                    <input type="hidden" :name="'inventory' + inv.product_id" v-validate data-vv-rules="min_value:0" :value="getInventoryAvailable(inv.product_id)">
                </template>

                <div class="card mb-4 card-product" v-for="(prg, i) in form.purchaseRequestGroups">
                    <div class="card-header bg-secondary">
                        <strong>{{ prg.seller.name }} <small>({{ prg.created_at | date(true) }})</small></strong>
                    </div>

                    <div class="card-body">

                        <div class="mb-2">
                            <strong>
                                <i class="fa fa-mobile-phone"></i>
                                {{ t('form.approvedProducts', {
                                    attribute: prg.products.reduce((total, p) => total + (!isNaN(parseInt(p.approved)) ? parseInt(p.approved) : 0), 0)
                                }) }}
                                <template v-if="!prg.show">
                                    (<a class="pointer" @click="prg.show = !prg.show">{{ t('form.detail').toLowerCase() }}</a>)
                                </template>
                                <template v-if="prg.show">
                                    (<a class="pointer" @click="prg.show = !prg.show">{{ t('form.hide').toLowerCase() }}</a>)
                                </template>
                            </strong>
                        </div>

                        <table class="table">
                            <thead>
                            <tr>
                                <th>{{ t('validation.attributes.product') }}</th>
                                <th>{{ t('validation.attributes.ordered') }}</th>
                                <th>{{ t('validation.attributes.available') }}</th>
                                <th>{{ t('validation.attributes.approved') }}</th>
                            </tr>
                            </thead>
                            <tbody v-show="prg.show">
                                <tr v-for="(product, ii) in prg.products" v-if="product.qty || product.hasInventory">
                                <td>
                                    {{ product.description }}
                                </td>
                                <td>
                                    <input type="number" class="form-control" readonly :value="product.qty">
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        class="form-control"
                                        :class="{'is-invalid': errors.has('inventory' + product.id)}"
                                        readonly
                                        :value="getInventoryAvailable(product.id)"
                                    >
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        :name="'approved' + i + '-' + ii"
                                        class="form-control"
                                        :class="{'is-invalid': errors.has('approved' + i + '-' + ii) || errors.has('inventory' + product.id)}"
                                        v-model="product.approved"
                                        v-validate
                                        data-vv-rules="required|numeric"
                                        :readonly="! product.hasInventory"
                                        @keypress="$validator.validate('inventory' + product.id)"
                                        @keydown="$validator.validate('inventory' + product.id)"
                                        @keyup="$validator.validate('inventory' + product.id)"
                                        @change="$validator.validate('inventory' + product.id)"
                                    >
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>{{ t('form.total') }}</th>
                                <th>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :value="prg.products.reduce((total, p) => total + (!isNaN(parseInt(p.qty)) ? parseInt(p.qty) : 0), 0)"
                                        readonly
                                    >
                                </th>
                                <th></th>
                                <th>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :value="prg.products.reduce((total, p) => total + (!isNaN(parseInt(p.approved)) ? parseInt(p.approved) : 0), 0)"
                                        readonly
                                    >
                                </th>
                            </tr>
                            </tfoot>
                        </table>

                        <div v-show="prg.show">
                            <button class="btn btn-success">
                                <i class="fa fa-mobile-phone"></i> &nbsp;
                                <i class="fa fa-arrow-alt-circle-right"></i> &nbsp;
                                <i class="fa fa-store-alt"></i> &nbsp;
                                {{ t('form.assignToSeller') }}
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'inventory-distribution',
        props: {
            editData: {
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
                form: {
                    purchaseRequestGroups: []
                }
            }
        },

        mounted() {
            this.form = {
                purchaseRequestGroups: [
                    ...this.editData.map(prg => {

                        let products = [];

                        prg.purchase_requests.forEach(pr => {

                            products = products.concat(
                                pr.product.same_model.filter(sm => ! products.find(p => p.id === sm.id))
                            );
                        });

                        return {
                            ...prg,
                            show: true,
                            products: products.map(p => {
                                return {
                                    ...p,
                                    qty: prg.purchase_requests.find(pr => pr.product_id === p.id)?.qty ?? 0,
                                    approved: 0,
                                    hasInventory: !! this.getInventoryAvailable(p.id)
                                }
                            })
                        }
                    })
                ]
            };
        },

        methods: {
            getInventoryAvailable(productId) {
                const inventory = this.inventory.find(i => i.product_id === productId)
                const available = inventory ? inventory.qty : 0;
                const consumed = this.form.purchaseRequestGroups.reduce((total, prg) => {

                    let approved = parseInt(prg.products.find(p => p.id === productId)?.approved ?? 0);
                    approved = approved > 0 ? approved : 0;

                    return total + approved
                }, 0)

                return available - consumed;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .card-product {
        border: solid 1px rgba(0, 0, 0, 0.3);
    }
</style>
