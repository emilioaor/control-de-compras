<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fa fa-list"></i> {{ t('menu.inventoryDistribution') }}
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-sm-6 form-group">
                        <div class="input-group">
                            <input
                                type="text"
                                name="filter"
                                class="form-control"
                                :placeholder="t('form.filter')"
                                maxlength="30"
                                v-model="filter"
                            >
                            <span class="input-group-btn">
                                <button class="btn btn-secondary">
                                    <i class="fa fa-filter"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>

                <div v-if="productsByModelC.length === 0">
                    <h5>{{ t('form.nothingPendingToProcess') }}</h5>
                </div>

                <template v-for="inv in inventory">
                    <input type="hidden" :name="'inventory' + inv.product_id" v-validate data-vv-rules="required" v-if="getInventoryAvailable(inv.product_id) < 0">
                </template>

                <div v-for="(model, i) in productsByModelC">
                    <table class="table table-bordered table-responsive-sm">
                        <thead>
                        <tr class="bg-header text-white">
                            <th colspan="9">
                                <i class="fa fa-eye" v-if="model.show"></i>
                                <i class="fa fa-eye-slash" v-else></i>

                                <a class="pointer text-white call-to-click pl-1" @click="model.show = !model.show">
                                    {{ model.brand }} {{ model.model }} -

                                    {{ t('form.clickToAssign') }}
                                </a>
                            </th>
                        </tr>
                        <tr>
                            <th class="py-1">{{ t('validation.attributes.seller') }}</th>
                            <th width="5%" class="text-center py-1 bg-important">{{ t('validation.attributes.note') }}</th>
                            <th width="5%" class="text-center py-1 bg-important">{{ t('validation.attributes.important') }}</th>
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
                                <td class="py-0 d-flex align-items-center">
                                    <div class="w-100">
                                        <template v-if="product.markAsNotFound">
                                            <del>
                                                {{ product.brand.name }}
                                                {{ product.description }}
                                            </del>
                                        </template>
                                        <template v-else>
                                            {{ product.brand.name }}
                                            {{ product.description }}
                                        </template>
                                    </div>

                                    <div class="w-100 text-right p-1">
                                        <button-confirmation
                                            v-if="product.qty > 0"
                                            :label="product.markAsNotFound ? t('form.markAsFound') : t('form.markAsNotFound')"
                                            :btn-class="product.markAsNotFound ? 'btn btn-warning fs-12' : 'btn btn-danger fs-12'"
                                            :icon-class="product.markAsNotFound ? 'fa fa-check' : 'fa fa-remove'"
                                            :disabled="loading !== null"
                                            :confirmation="t('form.notFoundExplanation')"
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
                                            @confirmed="handleNotFound($event, model, product, i)"
                                        ></button-confirmation>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
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
                                <td class="text-center bg-important">
                                    <template v-if="seller.note">
                                        <i
                                            @click="noteSelected = seller.note"
                                            class="fa fa-list-alt pointer"
                                            data-toggle="modal"
                                            data-target="#noteModal"
                                        ></i>
                                    </template>
                                    <template v-else>
                                        -
                                    </template>
                                </td>
                                <td class="text-center bg-important">
                                    {{ seller.important ? t('form.yes') : t('form.no') }}
                                </td>
                                <td class="text-center bg-ordered">
                                    {{ seller.qty }}

                                    <i
                                        @click="historySelected = seller.histories"
                                        class="fa fa-calendar pointer"
                                        data-toggle="modal"
                                        data-target="#orderedModal"
                                    ></i>
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
                            <th class="bg-important"></th>
                            <th class="bg-important"></th>
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

                    <div>
                        <button
                            class="btn btn-info"
                            v-if="loading !== i & ! model.show"
                            @click="model.show = true"
                        >
                            <i class="fa fa-eye"></i>
                            {{ t('form.clickToAssign') }}
                        </button>

                        <i class="spinner-border spinner-border-sm" v-if="loading === i"></i>

                        <button-confirmation
                            :label="t('form.assignProducts')"
                            btn-class="btn btn-success"
                            icon-class="fa fa-save"
                            v-if="loading !== i & model.show"
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

        <!-- Modal -->
        <div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <textarea cols="30" rows="5" class="form-control" v-model="noteSelected"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ t('form.close') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="orderedModal" tabindex="-1" role="dialog" aria-labelledby="orderedModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <table class="table table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>{{ t('validation.attributes.product') }}</th>
                                    <th>{{ t('validation.attributes.date') }}</th>
                                    <th class="text-center">{{ t('validation.attributes.qty') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="history in historySelected">
                                    <td>{{ history.product.upc }} / {{ history.product.description }}</td>
                                    <td>{{ history.created_at | date }}</td>
                                    <td class="text-center">{{ history.qty }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ t('form.close') }}</button>
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
            inventories: {
                type: Array,
                required: true
            },
            modelsNotFound: {
                type: Array,
                required: true
            }
        },

        data() {
            return {
                inventory: this.inventories.map(inv => {
                    return {...inv}
                }),
                modelsNF: this.modelsNotFound.map(mnf => {
                   return {...mnf}
                }),
                form: {
                    productsByModel: []
                },
                loading: null,
                noteSelected: null,
                historySelected: null,
                filter: ''
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
                                        important: prg2.purchase_requests.find(pr => pr.product_id === p.id)?.important ?? false,
                                        note: prg2.purchase_requests.find(pr => pr.product_id === p.id)?.note,
                                        approvedThisWeek: (
                                            prg2.purchase_movements
                                                .filter(pm => pm.product_id === p.id)
                                                .reduce((tot, pm) => tot + (pm.qty * -1), 0)
                                        ),
                                        histories: prg2.purchase_requests.find(pr => pr.product_id === p.id)?.purchase_request_histories.map(prh => {
                                            return {
                                                ...prh,
                                                product: {...p}
                                            }
                                        }) ?? []
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
                            brand: pr.product.brand.name,
                            show: false,
                            qty: products.reduce((total, p) => total + p.qty, 0),
                            products: [
                                ...products.map(p => {
                                    return {
                                        ...p,
                                        markAsNotFound: this.modelsNF.some(mnf => mnf.product_id === p.id)
                                    }
                                })
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

            handleNotFound(code, model, product, index) {
                if (code === 'yes') {
                    this.loading = index;
                    const productId = product.id;

                    ApiService.post('/buyer/inventory/not-found', {
                        product_id: productId
                    }).then(res => {

                        if (res.data.success) {

                            const pbm = this.form.productsByModel.find(pbm =>  pbm.model === model.model);
                            const product = pbm.products.find(p => p.id === productId);

                            product.markAsNotFound = ! product.markAsNotFound;
                            this.loading = null;
                        }
                    }).catch(err => {
                        this.loading = null;
                        console.error(err);
                    })
                }
            },
        },

        computed: {
            productsByModelC() {
                return this.form.productsByModel.filter(pbm => {
                    return (
                        pbm.model.toLowerCase().indexOf(this.filter.toLowerCase()) >= 0 ||
                        pbm.brand.toLowerCase().indexOf(this.filter.toLowerCase()) >= 0
                    )
                });
            }
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
    .bg-important {
        background-color: #ffe9ad;
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
    .call-to-click {
        display: inline-block;
        //animation: callToClick;
        animation-duration: .4s;
        animation-iteration-count: 2;
    }

    @keyframes callToClick {
        0% {
            transform: translateY(0) scaleX(1);
        }
        50% {
            transform: translateY(-4px) scaleX(1.03);
        }
        100% {
            transform: translateY(0) scaleX(1);
        }
    }
</style>
