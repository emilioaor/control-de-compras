<template>
    <div class="container">
        <div class="techland-form">
            <div class="card">
                <div class="card-header">
                    <div v-if="editData">
                        <i class="fa fa-edit"></i> {{ t('form.edit') }} {{ t('menu.purchaseRequests') }}
                    </div>
                    <div v-else>
                        <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.purchaseRequests') }}
                    </div>
                </div>
                <div class="card-body">

                    <div class="card card-product mb-3" v-for="(pr, i) in form.purchaseRequests">
                        <div class="card-header bg-secondary d-flex pl-1 align-items-center">
                            <div class="col-auto">
                                <strong>{{ t('validation.attributes.model') }}:</strong>
                            </div>

                            <div class="flex-grow-1 col-sm-6 col-md-4">
                                <search-input
                                    route="/buy/product/models"
                                    :description-fields="['model']"
                                    @selectResult="changeModel($event, i)"
                                    :input-class="errors.has('model' + i) ? 'is-invalid' : ''"
                                    :value="pr.model ? pr.model : ''"
                                    :disabled="!! loadingModel"
                                ></search-input>
                                <input type="hidden" :name="'model' + i" v-validate data-vv-rules="required" v-if="! pr.model">
                            </div>

                            <div class="col-auto" v-if="i > 0">
                                <button class="btn btn-danger" @click="removeRequest(i)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>

                            <div class="flex-grow-1 text-right">
                                <i class="fa fa-caret-down d-inline-block pointer" v-if="!pr.show" @click="pr.show = !pr.show"></i>
                                <i class="fa fa-caret-up d-inline-block pointer" v-if="pr.show" @click="pr.show = !pr.show"></i>
                            </div>
                        </div>

                        <div class="card-body">
                            <i class="spinner-border spinner-border-sm" v-if="loadingModel"></i>

                            <template v-else>

                                <div class="row mb-3" v-if="user.role === 'administrator'">
                                    <div class="col-sm-6 col-md-4">
                                        <label>{{ t('validation.attributes.seller') }}</label>

                                        <select
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('seller' + i)}"
                                            :name="'seller' + i"
                                            v-model="pr.seller_id"
                                            v-validate
                                            data-vv-rules="required"
                                        >
                                            <option v-for="seller in sellers" :value="seller.id">{{ seller.name }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <strong>
                                        <i class="fa fa-mobile-phone"></i>
                                        {{ t('form.productForThisModel', {attribute: purchaseRequests[i].qty}) }}
                                        <template v-if="!pr.show">
                                            (<a class="pointer" @click="pr.show = !pr.show">{{ t('form.detail').toLowerCase() }}</a>)
                                        </template>
                                        <template v-if="pr.show">
                                            (<a class="pointer" @click="pr.show = !pr.show">{{ t('form.hide').toLowerCase() }}</a>)
                                        </template>
                                        <div
                                            class="text-danger d-inline-block"
                                            v-if="pr.products.some((p, ii) => errors.has('qty' + i + '-' + ii)) || errors.has('productsByModel' + i)"
                                        >
                                            {{ t('form.hasErrors') }}
                                            <i class="fa fa-exclamation"></i>
                                        </div>
                                    </strong>
                                </div>

                                <input type="hidden" :name="'productsByModel' + i" v-validate data-vv-rules="required" v-if="purchaseRequests[i].qty === 0">
                                <div v-if="errors.has('productsByModel' + i)">
                                    <span class="invalid-feedback d-block" role="alert" >
                                        <strong>
                                            {{ t('form.productsByModel') }}
                                        </strong>
                                    </span>
                                </div>

                                <div v-for="(product, ii) in pr.products" class="row mt-3" v-show="pr.show">
                                    <div class="col-12">
                                        <hr class="mt-0">
                                    </div>

                                    <div class="ml-2 d-flex align-items-center">
                                        {{ product.description }}
                                    </div>
                                    <div class="col-sm-4 col-md-2">
                                        <input
                                            type="number"
                                            :name="'qty' + i + '-' + ii"
                                            class="form-control"
                                            :class="{'is-invalid': errors.has('qty' + i + '-' + ii)}"
                                            v-model="product.qty"
                                            v-validate
                                            data-vv-rules="required|min_value:0"
                                        >
                                        <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('qty' + i + '-' + ii, 'required')">
                                            <strong>
                                                {{ t('validation.required', {attribute: 'qty'}) }}
                                            </strong>
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div>
                        <button class="btn btn-info text-white" @click="addMore()">
                            <i class="fa fa-plus"></i>
                            {{ t('form.addMore') }}
                        </button>
                    </div>
                </div>

                <div class="card-footer">
                    <i class="spinner-border spinner-border-sm" v-if="loading"></i>

                    <button class="btn btn-success" v-if="! loading" @click="validateForm()">
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
                required: true
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
                    purchaseRequests: []
                }
            }
        },

        methods: {

            validateForm() {
                return this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {

                const apiService = this.editData ?
                    ApiService.put('/seller/purchase-request/' + this.form.uuid, this.form) :
                    ApiService.post('/seller/purchase-request', this.form)
                ;

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
                    seller_id: null,
                    products: []
                })
            },

            removeRequest(index) {
                this.form.purchaseRequests.splice(index, 1);
            },

            getProductByModel(model, index) {
                this.loadingModel = model;

                ApiService.get('/buy/product/models/' + model).then(res => {

                    this.loadingModel = null;

                    if (res.data.success) {
                        this.form.purchaseRequests[index].products = res.data.data.map(p => {
                            return  {
                                ...p,
                                qty: 0
                            }
                        });

                        this.form.purchaseRequests[index].show = true;
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
</style>
