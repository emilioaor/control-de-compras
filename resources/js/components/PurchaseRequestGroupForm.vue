<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <template v-if="purchaseType === 'purchase-request'">
                            <i class="fa fa-eye"></i> {{ t('menu.purchaseRequests') }}
                        </template>
                        <template v-if="purchaseType === 'purchase'">
                            <i class="fa fa-eye"></i> {{ t('menu.purchases') }}
                        </template>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 col-lg-4 form-group">
                                <label for="number">{{ t('validation.attributes.number') }}</label>
                                <input
                                    type="text"
                                    id="number"
                                    name="number"
                                    class="form-control"
                                    :value="formC.number"
                                    readonly
                                >
                            </div>

                            <template v-if="purchaseType === 'purchase-request'">
                                <div class="col-sm-6 col-lg-4 form-group">
                                    <label for="seller">{{ t('validation.attributes.seller') }}</label>
                                    <input
                                        type="text"
                                        id="seller"
                                        name="seller"
                                        class="form-control"
                                        :value="formC.seller ? formC.seller.name : ''"
                                        readonly
                                    >
                                </div>
                            </template>
                            <template v-else-if="purchaseType === 'purchase'">
                                <div class="col-sm-6 col-lg-4 form-group">
                                    <label for="buyer">{{ t('validation.attributes.buyer') }}</label>
                                    <input
                                        type="text"
                                        id="buyer"
                                        name="buyer"
                                        class="form-control"
                                        :value="formC.buyer ? formC.buyer.name : ''"
                                        readonly
                                    >
                                </div>
                            </template>

                            <div class="col-sm-6 col-lg-4 form-group">
                                <label>{{ t('validation.attributes.status') }}</label>
                                <div>
                                    <span
                                        class="py-2 px-3 rounded d-inline-block"
                                        :class="{
                                            'bg-info' : form.status === 'open',
                                            'bg-secondary text-white': form.status === 'closed'
                                        }"
                                    >
                                        {{ t('status.' + form.status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="col-12 form-group mt-2">
                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th>{{ t('validation.attributes.upc') }}</th>
                                            <th>{{ t('validation.attributes.description') }}</th>
                                            <template v-if="purchaseType === 'purchase-request'">
                                                <th class="text-center">{{ t('validation.attributes.important') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.note') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.ordered') }}</th>
                                                <th class="text-center">{{ t('validation.attributes.approved') }}</th>
                                                <th width="5%" class="text-center">{{ t('validation.attributes.balance') }}</th>
                                            </template>
                                            <template v-else-if="purchaseType === 'purchase'">
                                                <th width="5%" class="text-center">{{ t('validation.attributes.qty') }}</th>
                                            </template>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="product in formC.products">
                                            <td>{{ product.upc }}</td>
                                            <td>
                                                {{ product.description }}

                                                <span
                                                    class="text-danger"
                                                    v-if="purchaseType === 'purchase-request' && product.markAsNotFound"
                                                >
                                                    <strong>({{ t('form.notFound') }})</strong>
                                                </span>
                                            </td>
                                            <template v-if="purchaseType === 'purchase-request'">
                                                <td class="text-center">{{ product.important ? t('form.yes') : t('form.no') }}</td>
                                                <td class="text-center">{{ product.note }}</td>
                                                <td class="text-center">{{ product.ordered }}</td>
                                                <td class="text-center">{{ product.approved }}</td>
                                                <td class="text-center">
                                                <span
                                                    class="d-block rounded p-1 py-2"
                                                    :class="{
                                                        'bg-success text-white': product.balance >= 0,
                                                        'bg-danger text-white': product.balance < 0
                                                    }"
                                                >
                                                    <span v-if="product.balance > 0">+</span>
                                                    {{ product.balance }}
                                                </span>
                                                </td>
                                            </template>
                                            <template v-else-if="purchaseType === 'purchase'">
                                                <td class="text-center">{{ product.approved }}</td>
                                            </template>
                                        </tr>
                                    </tbody>
                                    <tfoot v-if="purchaseType === 'purchase-request'">
                                        <tr>
                                            <th>{{ t('form.total') }}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th class="text-center">{{ formC.products.reduce((total, p) => total + p.ordered, 0) }}</th>
                                            <th class="text-center">{{ formC.products.reduce((total, p) => total + p.approved, 0) }}</th>
                                            <th class="text-center">
                                                <span
                                                    class="d-block rounded p-1 py-2"
                                                    :class="{
                                                        'bg-success text-white': formC.products.reduce((total, p) => total + p.balance, 0) >= 0,
                                                        'bg-danger text-white': formC.products.reduce((total, p) => total + p.balance, 0) < 0
                                                    }"
                                                >
                                                    <span v-if="formC.products.reduce((total, p) => total + p.balance, 0) > 0">+</span>
                                                    {{ formC.products.reduce((total, p) => total + p.balance, 0) }}
                                                </span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'purchase-request-group-form',
        props: {
            editData: {
                type: Object,
                required: true
            },
            purchaseType: {
                type: String,
                required: true,
                validator: value => {
                    return value === 'purchase' || value === 'purchase-request'
                }
            },
            modelsNotFound: {
                type: Array,
                required: false,
                default: () => []
            }
        },
        mounted() {
            if (this.editData) {
                this.form = {
                    ...this.form,
                    ...this.editData
                };
            }
        },
        data() {
            return {
                form: {
                    number: null,
                    seller_id: null,
                    seller: null,
                    buyer: null,
                    purchases: [],
                    purchase_requests: [],
                    purchase_movements: [],
                    processed_at: null,
                    status: null
                },
            }
        },

        computed: {
            formC() {
                const ordered = this.form.purchase_requests.reduce((total, pr) => total + pr.qty, 0);
                const approved = this.form.purchase_movements.reduce((total, pm) => total + pm.qty * -1, 0)
                const products = this.form.purchase_requests.map(pr => {
                    return {
                        ...pr.product,
                        ordered: pr.qty,
                        approved: 0,
                        balance: pr.qty * -1,
                        markAsNotFound: this.modelsNotFound.some(mnf => mnf.model === pr.product.model),
                        important: pr.important,
                        note: pr.note
                    }
                });

                this.form.purchase_movements.forEach(pm => {
                    const product = products.find(p => p.id === pm.product_id)

                    if (product) {
                        product.approved += (pm.qty < 0 ? pm.qty * -1 : pm.qty);
                        product.balance = product.approved - product.ordered
                    } else {
                        products.push({
                            ...pm.product,
                            ordered: 0,
                            approved: pm.qty < 0 ? pm.qty * -1 : pm.qty,
                            balance: pm.qty < 0 ? pm.qty * -1 : pm.qty,
                            markAsNotFound: this.modelsNotFound.some(mnf => mnf.model === pm.product.model),
                            important: false,
                            note: null
                        })
                    }
                })

                return {
                    ...this.form,
                    ordered,
                    approved,
                    balance: approved - ordered,
                    products
                }
            }
        }
    }
</script>
