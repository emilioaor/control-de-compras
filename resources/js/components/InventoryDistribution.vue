<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> {{ t('menu.inventoryDistribution') }}
            </div>
            <div class="card-body">

                <div v-if="form.purchaseRequestGroups.length === 0">
                    <h5>{{ t('form.nothingPendingToProcess') }}</h5>
                </div>

                <template v-for="inv in inventory">
                    <input type="hidden" :name="'inventory' + inv.product_id" v-validate data-vv-rules="required" v-if="getInventoryAvailable(inv.product_id) < 0">
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
                                        :class="{'is-invalid': errors.has('approved' + i + '-' + ii, 'prg' + i) || errors.has('inventory' + product.id)}"
                                        v-model="product.approved"
                                        v-validate
                                        data-vv-rules="required|numeric"
                                        :data-vv-scope="'prg' + i"
                                        :readonly="! product.hasInventory || prg.complete"
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

                        <div v-if="prg.complete">
                            <div class="alert alert-success">
                                {{ t('alert.processSuccessfully') }}
                            </div>
                        </div>
                        <div v-else v-show="prg.show">
                            <i class="spinner-border spinner-border-sm" v-if="loading === i"></i>

                            <button-confirmation
                                :label="t('form.assignToSeller')"
                                btn-class="btn btn-success"
                                icon-class="fa fa-mobile-phone"
                                v-if="loading !== i"
                                :disabled="loading !== null"
                                :confirmation="t('form.areYouSure')"
                                :buttons="[
                                        {
                                            label: t('form.yes'),
                                            btnClass: 'btn btn-success',
                                            code: 'yes'
                                        },
                                        {
                                            label: t('form.no'),
                                            btnClass: 'btn btn-danger',
                                            code: 'no'
                                        }
                                    ]"
                                @confirmed="handleAssignProducts($event, prg, i)"
                            ></button-confirmation>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import ApiService from '../services/ApiService';

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
                },
                loading: null
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
                            complete: false,
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
            },

            handleAssignProducts(code, productRequestGroup, index) {
                if (code === 'yes') {
                    this.$validator.validateAll('prg' + index).then(res => {

                        this.$validator.validateAll().then(inv =>  {

                            const hasError = productRequestGroup.products.some(p => this.errors.has('inventory' + p.id));

                            if (res && ! hasError) {
                                this.loading = index;

                                ApiService.post('/buyer/inventory/distribution/' + productRequestGroup.uuid, {
                                    ...productRequestGroup
                                }).then(res => {

                                    if (res.data.success) {
                                        productRequestGroup.complete = true;
                                        productRequestGroup.show = false;
                                        this.loading = null;
                                    }
                                }).catch(err => {
                                    this.loading = null;
                                })
                            }
                        })
                    })
                }

            },
        }
    }
</script>

<style lang="scss" scoped>
    .card-product {
        border: solid 1px rgba(0, 0, 0, 0.3);
    }
</style>
