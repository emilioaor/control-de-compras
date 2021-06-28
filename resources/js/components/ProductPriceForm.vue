<template>
    <div class="container">
        <div class="techland-form">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.purchasePrice') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.supplier') }}</label>
                            <search-input
                                :route="'/buyer/supplier'"
                                :description-fields="['name']"
                                @selectResult="changeSupplier($event)"
                                :value="form.supplier ? form.supplier.name : ''"
                                :input-class="errors.has('supplier') ? 'is-invalid' : ''"
                            ></search-input>
                            <input type="hidden" name="supplier" v-validate data-vv-rules="required" v-if="! form.supplier">

                            <span class="invalid-feedback d-block" role="alert" v-if="errors.has('supplier')">
                                <strong>{{ t('validation.required', {attribute: 'supplier'}) }}</strong>
                            </span>
                        </div>
                    </div>

                    <ul class="nav nav-tabs mb-3" v-if="form.supplier">
                        <li class="nav-item">
                            <a
                                class="nav-link pointer"
                                :class="{active: form.type === 'by_product'}"
                                @click="changeType('by_product')">{{ t('form.pricesByProduct') }}</a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link pointer"
                                :class="{active: form.type === 'file'}"
                                @click="changeType('file')">{{ t('form.loadFile') }}</a>
                        </li>
                    </ul>

                    <div class="row px-3" v-if="form.supplier">
                        <template v-if="form.type === 'file'">

                            <div class="col-12 form-group">
                                <a href="/buyer/report/product" target="_blank">
                                    <i class="fa fa-download"></i>
                                    {{ t('form.downloadFile') }}
                                </a>
                            </div>

                            <div class="col-sm-6 col-md-4 form-group">
                                <label for="file">{{ t('validation.attributes.file') }}</label>
                                <input
                                    type="file"
                                    id="file"
                                    name="file"
                                    class="form-control"
                                    :class="{'is-invalid': errors.has('file')}"
                                    v-validate
                                    data-vv-rules="required|mimes:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                    @change="changeFile()"
                                >

                                <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('file', 'required')">
                                    <strong>{{ t('validation.required', {attribute: 'file'}) }}</strong>
                                </span>

                                <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('file', 'mimes')">
                                    <strong>{{ t('validation.mimes', {attribute: 'file', values: 'Excel'}) }}</strong>
                                </span>
                            </div>
                        </template>

                        <template v-if="form.type === 'by_product'">
                            <div class="col-12" v-if="loadingProducts">
                                <i class="spinner-border spinner-border-sm"></i>
                            </div>

                            <div class="col-12" v-else>

                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th>{{ t('validation.attributes.product') }}</th>
                                            <th></th>
                                            <th class="text-center">{{ t('validation.attributes.currentPrice') }}</th>
                                            <th class="text-center">{{ t('validation.attributes.priceDate') }}</th>
                                            <th width="15%" class="text-center">{{ t('validation.attributes.price') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(productPrice, i) in form.products">
                                            <td>
                                                <template v-if="productPrice.isNew">
                                                    <search-input
                                                        :route="'/buy/product?notIn=' + productSelectedIds"
                                                        :description-fields="['upc', 'description']"
                                                        @selectResult="changeProduct($event, i)"
                                                        :value="productPrice.product ? productPrice.product.upc + ' / ' + productPrice.product.description : ''"
                                                    ></search-input>
                                                </template>

                                                <template v-else>
                                                    {{ productPrice.product.upc }} / {{ productPrice.product.description }}
                                                </template>
                                            </td>
                                            <td>
                                                <template v-if="productPrice.isNew">
                                                    <button class="btn btn-danger" @click="removeProduct(i)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </template>
                                            </td>
                                            <td class="text-center">
                                                <template v-if="productPrice.currentPrice">
                                                    {{ productPrice.currentPrice }}
                                                </template>
                                                <template v-else>
                                                    -
                                                </template>
                                            </td>
                                            <td class="text-center">
                                                <template v-if="productPrice.updated_at">
                                                    {{ productPrice.updated_at | date }}
                                                </template>
                                                <template v-else>
                                                    -
                                                </template>
                                            </td>
                                            <td class="text-center">
                                                <input
                                                    type="number"
                                                    :name="'price' + i"
                                                    class="form-control"
                                                    :class="{'is-invalid': errors.has('price' + i)}"
                                                    v-model="productPrice.price"
                                                    v-validate
                                                    :data-vv-rules="productPrice.isNew ? 'required' : ''"
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>
                                                <search-input
                                                    :route="'/buy/product?notIn=' + productSelectedIds"
                                                    :description-fields="['upc', 'description']"
                                                    @selectResult="addProduct($event)"
                                                    :value="''"
                                                ></search-input>
                                            </td>
                                            <td></td>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </template>
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

        <!-- Modal -->
        <button
            type="button"
            class="d-none"
            id="openModalErrors"
            data-toggle="modal"
            data-target="#modalErrors"
        ></button>

        <div class="modal fade" id="modalErrors" tabindex="-1" role="dialog" aria-labelledby="modalErrors" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">

                        <h5 class="bg-danger text-white p-2 rounded">
                            {{ t('validation.regex', {'attribute': 'file'}) }}
                            <strong>{{ t('form.line') }}:</strong> {{ modal.line }}
                        </h5>

                        <div class="pt-2">
                            <div>
                                <strong>{{ t('form.errors') }}:</strong>
                            </div>
                            <ul>
                                <li v-for="error in modal.errors">{{ error }}</li>
                            </ul>
                        </div>
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
    import ApiService from "../services/ApiService";

    export default {
        name: 'product-price-form',

        data() {
            return {
                loading: false,
                loadingProducts: false,
                form: {
                    supplier: null,
                    supplier_id: null,
                    type: 'by_product',
                    file: null,
                    products: []
                },
                modal: {
                    errors: [],
                    line: []
                },
            }
        },

        methods: {

            validateForm() {
                return this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {
                this.loading = true;

                ApiService.post('/buyer/product-price', this.form).then(res => {

                    if (res.data.success) {
                        location.reload();
                    }

                }).catch(err => {
                    this.loading = false;

                    if (this.form.type === 'file') {
                        this.modal = {...err.response.data};
                        document.querySelector('#openModalErrors').click();
                    }
                })
            },

            changeSupplier(supplier) {
                this.form.supplier = supplier;
                this.form.supplier_id = supplier.id;
                this.form.type = null;
                this.changeType('by_product');
            },

            changeFile() {
                const file = $('#file')[0].files[0];

                if (!file) {
                    return false;
                }

                const reader = new FileReader();

                reader.addEventListener('load', () => {
                    if (reader.result) {
                        this.form.file = reader.result;
                    }
                });

                reader.readAsDataURL(file);
            },

            changeType(type) {
                if (this.form.type !== type) {

                    this.form.products = [];
                    this.form.type = type;

                    if (type === 'by_product') {
                        this.getProducts();
                    }
                }
            },

            getProducts() {
                this.loadingProducts = true;

                ApiService.get('/buyer/product-price/' + this.form.supplier.uuid).then(res => {
                    this.form.products = [
                        ...res.data.data.map(productPrice => {
                            return {
                                ...productPrice,
                                currentPrice: productPrice.price,
                                price: null,
                                isNew: false
                            }
                        })
                    ];
                    this.loadingProducts = false;
                }).catch(err => {
                    this.loadingProducts = false;
                })
            },

            addProduct(product) {
                this.form.products.push({
                    isNew: true,
                    currentPrice: null,
                    price: null,
                    product_id: product.id,
                    supplier_id: this.form.supplier.id,
                    product: product
                })
            },

            changeProduct(product, index) {
                this.form.products[index].product = product;
                this.form.products[index].product_id = product.id;
            },

            removeProduct(index) {
                this.form.products.splice(index, 1);
            }
        },

        computed: {
            productSelectedIds() {
                return this.form.products.map(map => map.product.id).join(',');
            }
        }
    }
</script>
