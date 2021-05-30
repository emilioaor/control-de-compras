<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-edit"></i> {{ t('form.edit') }} {{ t('menu.purchaseRequests') }}
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

                            <div class="col-sm-6 col-lg-4 form-group" v-if="form.processed_at">
                                <label for="processed_at">{{ t('validation.attributes.processedAt') }}</label>
                                <input
                                    type="text"
                                    id="processed_at"
                                    name="processed_at"
                                    class="form-control"
                                    :value="form.processed_at | date(true)"
                                    readonly
                                >
                            </div>

                            <div class="col-sm-6 col-lg-4 form-group">
                                <label>{{ t('validation.attributes.status') }}</label>
                                <div>
                                    <span
                                        class="py-2 px-3 rounded"
                                        :class="{
                                            'bg-info' : form.status === 'pending',
                                            'bg-success text-white': form.status === 'processed'
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
                                            <th>{{ t('validation.attributes.upc') }}</th>
                                            <th class="text-center">{{ t('validation.attributes.ordered') }}</th>
                                            <th class="text-center">{{ t('validation.attributes.approved') }}</th>
                                            <th width="5%" class="text-center">{{ t('validation.attributes.balance') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="product in formC.products">
                                            <td>{{ product.upc }}</td>
                                            <td>{{ product.description }}</td>
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
                                                    {{ product.balance }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>{{ t('form.total') }}</th>
                                            <th></th>
                                            <th class="text-center">{{ formC.products.reduce((total, p) => total += p.ordered, 0) }}</th>
                                            <th class="text-center">{{ formC.products.reduce((total, p) => total += p.approved, 0) }}</th>
                                            <th class="text-center">
                                                <span
                                                    class="d-block rounded p-1 py-2"
                                                    :class="{
                                                        'bg-success text-white': formC.products.reduce((total, p) => total += p.balance, 0) >= 0,
                                                        'bg-danger text-white': formC.products.reduce((total, p) => total += p.balance, 0) < 0
                                                    }"
                                                >
                                                    {{ formC.products.reduce((total, p) => total += p.balance, 0) }}
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
            }
        },
        mounted() {
            if (this.editData) {
                this.form = {...this.editData};
            }
        },
        data() {
            return {
                form: {
                    number: null,
                    seller_id: null,
                    seller: null,
                    purchase_requests: [],
                    purchase_movements: [],
                    processed_at: null,
                    status: null
                },
            }
        },

        computed: {
            formC() {
                const ordered = this.form.purchase_requests.reduce((total, pr) => total += pr.qty, 0);
                const approved = this.form.purchase_movements.reduce((total, pm) => total += pm.qty * -1, 0)
                const products = this.form.purchase_requests.map(pr => {
                    return {
                        ...pr.product,
                        ordered: pr.qty,
                        approved: 0,
                        balance: pr.qty * -1
                    }
                });

                this.form.purchase_movements.forEach(pm => {
                    const product = products.find(p => p.id === pm.product_id)

                    if (product) {
                        product.approved = pm.qty * -1;
                        product.balance = product.approved - product.ordered
                    } else {
                        products.push({
                            ...pm.product,
                            ordered: 0,
                            approved: pm.qty * -1,
                            balance: pm.qty * -1
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
