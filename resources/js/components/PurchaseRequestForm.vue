<template>
    <div class="container">
        <div class="techland-form">
            <div class="card">
                <div class="card-header">
                    <template v-if="purchaseType === 'purchase-request'">
                        <div v-if="editData">
                            <i class="fa fa-edit"></i> {{ t('form.edit') }} {{ t('menu.purchaseRequests') }}
                        </div>
                        <div v-else>
                            <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.purchaseRequests') }}
                        </div>
                    </template>
                    <template v-if="purchaseType === 'purchase'">
                        <div v-if="editData">
                            <i class="fa fa-edit"></i> {{ t('form.edit') }} {{ t('menu.purchases') }}
                        </div>
                        <div v-else>
                            <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.purchases') }}
                        </div>
                    </template>
                </div>
                <div class="card-body">

                    <div class="row mb-3" v-if="user.role === 'administrator' && purchaseType === 'purchase-request'">
                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.seller') }}</label>

                            <select
                                class="form-control"
                                :class="{'is-invalid': errors.has('seller')}"
                                name="seller"
                                v-model="form.seller_id"
                                v-validate
                                data-vv-rules="required"
                            >
                                <option v-for="seller in sellers" :value="seller.id">{{ seller.name }}</option>
                            </select>

                            <span class="invalid-feedback d-block" role="alert" v-if="errors.has('seller')">
                                <strong>{{ t('validation.required', {attribute: 'seller'}) }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3" v-if="user.role === 'administrator' && purchaseType === 'purchase'">
                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.buyer') }}</label>

                            <select
                                class="form-control"
                                :class="{'is-invalid': errors.has('buyer')}"
                                name="buyer"
                                v-model="form.buyer_id"
                                v-validate
                                data-vv-rules="required"
                            >
                                <option v-for="buyer in buyers" :value="buyer.id">{{ buyer.name }}</option>
                            </select>

                            <span class="invalid-feedback d-block" role="alert" v-if="errors.has('buyer')">
                                <strong>{{ t('validation.required', {attribute: 'buyer'}) }}</strong>
                            </span>
                        </div>
                    </div>

                    <table class="table table-bordered table-responsive-sm">
                        <thead>
                            <tr>
                                <template v-if="purchaseType === 'purchase-request'">
                                    <th width="15%" class="text-center">{{ t('validation.attributes.upc') }}</th>
                                    <th width="36%">{{ t('validation.attributes.description') }}</th>
                                    <th width="15%" class="text-center">{{ t('validation.attributes.ordered') }}</th>
                                    <th width="8%" class="text-center">{{ t('validation.attributes.important') }}</th>
                                    <th width="26%" class="text-center">{{ t('validation.attributes.note') }}</th>
                                </template>
                                <template v-else-if="purchaseType === 'purchase'">
                                    <th width="25%" class="text-center">{{ t('validation.attributes.upc') }}</th>
                                    <th width="60%">{{ t('validation.attributes.description') }}</th>
                                    <th width="15%" class="text-center">{{ t('validation.attributes.ordered') }}</th>
                                </template>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="(pr, i) in form.purchaseRequests">
                                <tr class="bg-secondary text-white">
                                    <td colspan="5">
                                        <div class="d-flex align-items-center">
                                            <div class="col-auto">
                                                <strong>{{ t('validation.attributes.model') }}:</strong>
                                            </div>

                                            <div class="flex-grow-1 col-sm-6 col-md-4">
                                                <search-input
                                                    :route="'/buy/product/models?notIn=' + form.purchaseRequests.map(map => map.model).join(',')"
                                                    :description-fields="['model']"
                                                    @selectResult="changeModel($event, i)"
                                                    :value="pr.model ? pr.model : ''"
                                                    :disabled="!! loadingModel"
                                                ></search-input>
                                            </div>

                                            <div class="col-auto" v-if="i > 0">
                                                <button class="btn btn-danger" @click="removeRequest(i)">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <input type="hidden" :name="'productsByModel' + i" v-validate data-vv-rules="required" v-if="purchaseRequests[i].model && purchaseRequests[i].qty === 0">
                                    </td>
                                </tr>
                                <tr v-if="loadingModel">
                                    <td colspan="5">
                                        <i class="spinner-border spinner-border-sm"></i>
                                    </td>
                                </tr>
                                <tr
                                    v-if="!loadingModel"
                                    v-for="(product, ii) in pr.products"
                                >
                                    <td class="text-center">{{ product.upc }}</td>
                                    <td>{{ product.description }}</td>
                                    <td class="text-center" :class="{'bg-danger': errors.has('productsByModel' + i)}">
                                        <input
                                            type="number"
                                            :name="'qty' + i + '-' + ii"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('qty' + i + '-' + ii)}"
                                            v-model="product.qty"
                                            v-validate
                                            data-vv-rules="numeric|min_value:0"
                                        >
                                        <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('qty' + i + '-' + ii, 'numeric')">
                                            <strong>
                                                {{ t('validation.required', {attribute: 'qty'}) }}
                                            </strong>
                                        </span>
                                    </td>
                                    <template v-if="purchaseType === 'purchase-request'">
                                        <td class="text-center">
                                            <div
                                                class="custom-checkbox text-white d-flex justify-content-start"
                                                @click="product.important = !product.important"
                                                :class="{
                                                    'justify-content-end': product.important
                                                }"
                                            >
                                                <div v-if="product.important" class="bg-success ">
                                                    {{ t('form.yes') }}
                                                </div>
                                                <div v-else class="bg-danger">
                                                    {{ t('form.no') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center" >
                                            <button
                                                class="btn btn-link"
                                                v-if="! product.showNote"
                                                @click="product.showNote = true"
                                            >
                                                <i class="fa fa-list-alt"></i>
                                                {{ t('form.addNote') }}
                                            </button>

                                            <textarea
                                                v-if="product.showNote"
                                                class="form-control"
                                                rows="2"
                                                :placeholder="t('validation.attributes.note')"
                                                v-model="product.note"
                                            ></textarea>
                                        </td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer">
                    <i class="spinner-border spinner-border-sm" v-if="loading"></i>

                    <button
                        class="btn btn-success"
                        v-if="! loading"
                        @click="validateForm()"
                        :disabled="purchaseRequests.length < 2"
                    >
                        <i class="fa fa-save"></i>
                        {{ t('form.save') }}
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import ApiService from "../services/ApiService";

    export default {
        name: 'purchase-request-form',
        props: {
            editData: {
                type: Object,
                required: false
            },
            user: {
                type: Object,
                required: true
            },
            sellers: {
                type: Array,
                required: false,
                default: () => []
            },
            buyers: {
                type: Array,
                required: false,
                default: () => []
            },
            purchaseType: {
                type: String,
                required: true,
                validator: value => {
                    return value === 'purchase' || value === 'purchase-request'
                }
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {
                    ...this.editData
                }
            } else {
                this.addMore();
            }
        },

        data() {
            return {
                loading: false,
                loadingModel: null,
                form: {
                    seller_id: null,
                    buyer_id: null,
                    purchaseRequests: []
                }
            }
        },

        methods: {

            validateForm() {
                return this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {

                let apiService;

                if (this.purchaseType === 'purchase-request') {

                    apiService = this.editData ?
                        ApiService.put('/seller/purchase-request/' + this.form.uuid, this.form) :
                        ApiService.post('/seller/purchase-request', this.form);

                } else if (this.purchaseType === 'purchase') {

                    apiService = this.editData ?
                        ApiService.put('/buyer/purchase/' + this.form.uuid, this.form) :
                        ApiService.post('/buyer/purchase', this.form);
                }

                this.loading = true;

                apiService.then(res => {

                    location.href = res.data.redirect;

                }).catch(err => {
                    this.loading = false;
                })
            },

            changeModel(event, index) {

                if (! this.form.purchaseRequests.some(pr => pr.model === event.model)) {
                    this.form.purchaseRequests[index].model = event.model;
                    this.getProductByModel(event.model, index);
                }

            },

            addMore() {
                this.form.purchaseRequests.push({
                    model: null,
                    show: true,
                    products: []
                })
            },

            removeRequest(index) {
                if (this.form.purchaseRequests[index].model) {
                    this.form.purchaseRequests.splice(index, 1);
                }
            },

            getProductByModel(model, index) {
                this.loadingModel = model;

                ApiService.get('/buy/product/models/' + model).then(res => {

                    this.loadingModel = null;

                    if (res.data.success) {
                        this.form.purchaseRequests[index].products = res.data.data.map(p => {
                            return  {
                                ...p,
                                qty: null,
                                important: false,
                                note: null,
                                showNote: false
                            }
                        });

                        this.form.purchaseRequests[index].show = true;
                        if (this.form.purchaseRequests[this.form.purchaseRequests.length - 1].model) {
                            this.addMore();
                        }
                    }

                }).catch(err => {
                    this.loadingModel = null;
                })
            }
        },

        computed: {
            purchaseRequests() {
                return this.form.purchaseRequests.map(pr => {
                    return {
                        ...pr,
                        qty: pr.products.reduce((total, p) => total + (!isNaN(parseInt(p.qty)) ? parseInt(p.qty) : 0), 0)
                    }
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    .card-product {
        border: solid 1px rgba(0,0,0, .3);
    }
    .custom-checkbox {
        border: solid 1px #ccc;
        border-radius: 20px;
        cursor: pointer;
        background-color: #cddd;

        div {
            padding: .3rem .4rem;
            border-radius: 50%;
        }
    }
</style>
