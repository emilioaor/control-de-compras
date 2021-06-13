<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> {{ t('menu.inventoryDistribution') }}
            </div>
            <div class="card-body">

                <div v-if="form.productsByModel.length === 0">
                    <h5>{{ t('form.nothingPendingToProcess') }}</h5>
                </div>

                <template v-for="inv in inventory">
                    <input type="hidden" :name="'inventory' + inv.product_id" v-validate data-vv-rules="required" v-if="getInventoryAvailable(inv.product_id) < 0">
                </template>


                <div v-for="(model, i) in form.productsByModel">
                    <table class="table table-bordered table-responsive-sm">
                        <thead>
                        <tr class="bg-header text-white">
                            <th colspan="7">
                                <i class="fa fa-mobile-phone"></i>
                                {{ model.model }}
                                <template v-if="!model.show">
                                    (<a class="pointer text-white" @click="model.show = !model.show">{{ t('form.detail').toLowerCase() }}</a>)
                                </template>
                                <template v-if="model.show">
                                    (<a class="pointer text-white" @click="model.show = !model.show">{{ t('form.hide').toLowerCase() }}</a>)
                                </template>
                            </th>
                        </tr>
                        <tr>
                            <th class="py-1">{{ t('validation.attributes.seller') }}</th>
                            <th width="10%" class="text-center py-1 bg-ordered">{{ t('validation.attributes.ordered') }}</th>
                            <th width="5%" class="text-center py-1 bg-ordered">%</th>
                            <th width="10%" class="text-center py-1 bg-available">{{ t('validation.attributes.available') }}</th>
                            <th width="10%" class="text-center py-1 bg-approved">{{ t('validation.attributes.approved') }}</th>
                            <th width="10%" class="text-center py-1 bg-approved">{{ t('validation.attributes.assign') }}</th>
                            <th width="5%" class="text-center py-1 bg-approved">%</th>
                        </tr>
                        </thead>
                        <tbody>
                        <template
                            v-for="(product, ii) in model.products"
                            v-if="product.hasInventory || product.qty > 0 || getApprovedThisWeekByProduct(product) > 0"
                        >
                            <tr class="tr-title-product">
                                <td class="py-0">{{ product.description }}</td>
                                <td class="text-center">
                                    <template v-if="! model.show">
                                        {{ product.qty }}
                                    </template>
                                </td>
                                <td class="text-center">
                                    <template v-if="! model.show">
                                        {{ getOrderedPercentageByProduct(product, model.qty) }}
                                    </template>
                                </td>
                                <td class="text-center">
                                    <template v-if="! model.show">
                                        {{ getInventoryAvailable(product.id) }}
                                    </template>
                                </td>
                                <td class="text-center">
                                    <template v-if="! model.show">
                                        {{ getApprovedThisWeekByProduct(product) }}
                                    </template>
                                </td>
                                <td class="text-center">
                                    <template v-if="! model.show">
                                        {{ getApprovedByProduct(product) }}
                                    </template>
                                </td>
                                <td class="text-center">
                                    <template v-if="! model.show">
                                        {{ getApprovedPercentageByProduct(product, model.qty) }}
                                    </template>
                                </td>
                            </tr>
                            <tr v-show="model.show" v-for="(seller, iii) in product.sellers">
                                <td>{{ seller.name }}</td>
                                <td class="text-center bg-ordered">
                                    {{ seller.qty }}
                                </td>
                                <td class="text-center bg-ordered">
                                    {{ getOrderedPercentageBySeller(seller, model.qty) }}
                                </td>
                                <td class="text-center bg-available">
                                    <span
                                        class="d-inline-block p-2 w-100"
                                        :class="{
                                            'bg-danger text-white': errors.has('inventory' + product.id)
                                        }"
                                    >
                                        {{ getInventoryAvailable(product.id) }}
                                    </span>
                                </td>
                                <td class="text-center bg-approved">
                                    {{ seller.approvedThisWeek }}
                                </td>
                                <td class="text-center bg-approved">
                                    <input
                                        type="number"
                                        :name="'approved' + ii + '-' + iii"
                                        class="form-control"
                                        :class="{'is-invalid': errors.has('approved' + ii + '-' + iii, 'model' + i) || errors.has('inventory' + product.id)}"
                                        v-model="seller.approved"
                                        v-validate
                                        data-vv-rules="numeric"
                                        :data-vv-scope="'model' + i"
                                        v-if="product.hasInventory"
                                        :disabled="! product.hasInventory"
                                    >
                                </td>
                                <td class="text-center bg-approved">
                                    {{ getApprovedPercentage(seller, product) }}
                                </td>
                            </tr>
                        </template>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th class="py-1">{{ t('form.total') }}</th>
                            <th class="text-center py-1 bg-ordered">
                                {{ model.qty }}
                            </th>
                            <th class="text-center py-1 bg-ordered"></th>
                            <th class="text-center py-1 bg-available">
                                {{ getInventoryAvailableByProducts(model.products) }}
                            </th>
                            <th class="text-center py-1 bg-approved">
                                {{ getApprovedThisWeekByProducts(model.products) }}
                            </th>
                            <th class="text-center py-1 bg-approved">
                                {{ getApprovedByProducts(model.products) }}
                            </th>
                            <th class="text-center py-1 bg-approved"></th>
                        </tr>
                        </tfoot>
                    </table>

                    <div v-show="model.show">
                        <i class="spinner-border spinner-border-sm" v-if="loading === i"></i>

                        <button-confirmation
                            :label="t('form.assignProducts')"
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
                            @confirmed="handleAssignProducts($event, model, i)"
                        ></button-confirmation>
                    </div>

                    <hr>
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
            inventories: {
                type: Array,
                required: true
            }
        },

        data() {
            return {
                inventory: this.inventories.map(inv => {
                    return {...inv}
                }),
                form: {
                    productsByModel: []
                },
                loading: null
            }
        },

        mounted() {
            const productsByModel = [];

            this.editData.forEach((prg) => {
                prg.purchase_requests.forEach(pr => {
                    if (!productsByModel.some(p => p.model === pr.product.model)) {

                        const products = pr.product.same_model.map(p => {

                            const sellers = this.editData
                                .filter(prg2 => prg2.purchase_requests.some(pr2 => pr2.product.model === pr.product.model))
                                .map(prg2 => {
                                    return {
                                        ...prg2.seller,
                                        qty: prg2.purchase_requests.find(pr => pr.product_id === p.id)?.qty ?? 0,
                                        approved: null,
                                        approvedThisWeek: (
                                            prg2.purchase_movements
                                                .filter(pm => pm.product_id === p.id)
                                                .reduce((tot, pm) => tot + (pm.qty * -1), 0)
                                        )
                                    }
                                })

                            return {
                                ...p,
                                sellers: [
                                    ...sellers
                                ],
                                hasInventory: this.getInventoryAvailable(p.id),
                                qty: sellers.reduce((t, s) => t + s.qty, 0)
                            }
                        });

                        productsByModel.push({
                            model: pr.product.model,
                            show: false,
                            qty: products.reduce((total, p) => total + p.qty, 0),
                            products: [
                                ...products
                            ],
                        });
                    }
                })
            })


            this.form.productsByModel = [
                ...productsByModel
            ]
        },

        methods: {
            getInventoryAvailable(productId) {
                const inventory = this.inventory.find(i => i.product_id === productId)
                const available = inventory ? inventory.qty : 0;
                const consumed = this.form.productsByModel.reduce((total, model) => {

                    const product = model.products.find(p => p.id === productId);

                    if (! product) {
                        return total;
                    }

                    const approved = product.sellers.reduce((tot, s) => {
                        return tot + this.notNaN(parseInt(s.approved));
                    }, 0);

                    return total + approved;
                }, 0)

                return available - consumed;
            },

            getOrderedPercentageByProduct(product, total) {
                return this.round(this.notNaN((product.qty * 100) / total));
            },

            getOrderedPercentageBySeller(seller, total) {
                return this.round(this.notNaN((seller.qty * 100) / total));
            },

            getInventoryAvailableByProducts(products) {
                return products.reduce((total, p) => {
                    return total + this.getInventoryAvailable(p.id)
                }, 0)
            },

            getApprovedByProduct(product) {
                return product.sellers.reduce((tot, s) => tot + this.notNaN(parseInt(s.approved)), 0)
            },

            getApprovedByProducts(products) {
                return products.reduce((total, p) => total + this.getApprovedByProduct(p), 0)
            },

            getApprovedPercentage(seller, product) {
                const approved = this.notNaN(parseInt(seller.approved)) + seller.approvedThisWeek;
                const count = product.hasInventory + product.sellers.reduce((tot, s) => tot + s.approvedThisWeek, 0);

                return this.round(this.notNaN((approved * 100) / count));
            },

            getApprovedPercentageByProduct(product, total) {
                const approved = this.getApprovedByProduct(product) + this.getApprovedThisWeekByProduct(product);

                return this.round((approved * 100) / total);
            },

            getApprovedThisWeekByProduct(product) {
                return product.sellers.reduce((tot, s) => tot + s.approvedThisWeek, 0);
            },

            getApprovedThisWeekByProducts(products) {
                return products.reduce((total, p) => {
                    return total + this.getApprovedThisWeekByProduct(p);
                }, 0)
            },

            round(number) {
                return Math.round(number * 100) / 100;
            },

            notNaN(number) {
                return !isNaN(number) ? number : 0;
            },

            handleAssignProducts(code, model, index) {
                if (code === 'yes') {
                    this.$validator.validateAll('model' + index).then(res => {

                        this.$validator.validateAll().then(inv =>  {

                            const hasError = model.products.some(p => this.errors.has('inventory' + p.id));

                            if (res && ! hasError) {
                                this.loading = index;

                                ApiService.post('/buyer/inventory/distribution', {
                                    ...model
                                }).then(res => {

                                    if (res.data.success) {
                                        model.products.forEach(p => {
                                            p.sellers.forEach(s => {

                                                s.approvedThisWeek += this.notNaN(parseInt(s.approved));
                                                p.hasInventory = p.hasInventory - this.notNaN(parseInt(s.approved));
                                                const inventory = this.inventory.find(i => i.product_id === p.id);

                                                if (inventory) {
                                                    inventory.qty = inventory.qty - this.notNaN(parseInt(s.approved));
                                                }

                                                s.approved = null;
                                            })
                                        })
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
    .bg-header {
        background-color: #758496;
    }
    .tr-title-product {
        font-size: 12px;
        background-color: #758496;
        color: #fff;
    }
    .table-bordered {
        tr {
            td {
                padding: 0;
                vertical-align: middle;
                &:first-of-type {
                    padding: 0 .5rem;
                }
            }
        }
    }
    .bg-ordered {
        background-color: #d4ffd1;
    }
    .bg-available {
        background-color: #f2ffbf;
    }
    .bg-approved {
        background-color: #badffc;

        .form-control {
            display: inline-block;
            width: 80%;
            height: 30px;
            padding: .4rem;
            font-size: 13px;
        }
    }
</style>
