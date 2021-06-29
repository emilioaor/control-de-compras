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

                                <table class="table table-responsive-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ t('validation.attributes.model') }}</th>
                                            <th></th>
                                            <th class="text-center">{{ t('validation.attributes.currentPrice') }}</th>
                                            <th class="text-center">{{ t('validation.attributes.priceDate') }}</th>
                                            <th width="15%" class="text-center">{{ t('validation.attributes.price') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(model, i) in form.models">
                                            <td>
                                                <template v-if="model.isNew">
                                                    <search-input
                                                        :route="'/buy/product/models?notIn=' + modelSelected"
                                                        :description-fields="['model']"
                                                        @selectResult="changeModel($event, i)"
                                                        :value="model.model"
                                                    ></search-input>
                                                </template>

                                                <template v-else>
                                                    {{ model.model }}
                                                </template>
                                            </td>
                                            <td>
                                                <template v-if="model.isNew">
                                                    <button class="btn btn-danger" @click="removeModel(i)">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </template>
                                            </td>
                                            <td class="text-center">
                                                <template v-if="model.currentPrice">
                                                    {{ model.currentPrice }}
                                                </template>
                                                <template v-else>
                                                    -
                                                </template>
                                            </td>
                                            <td class="text-center">
                                                <template v-if="model.updated_at">
                                                    {{ model.updated_at | date }}
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
                                                    v-model="model.price"
                                                    v-validate
                                                    :data-vv-rules="model.isNew ? 'required' : ''"
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>
                                                <search-input
                                                    :route="'/buy/product/models?notIn=' + modelSelected"
                                                    :description-fields="['model']"
                                                    @selectResult="addModel($event)"
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
                    models: []
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

                    this.form.models = [];
                    this.form.type = type;

                    if (type === 'by_product') {
                        this.getModels();
                    }
                }
            },

            getModels() {
                this.loadingProducts = true;

                ApiService.get('/buyer/product-price/' + this.form.supplier.uuid).then(res => {

                    const models = [];

                    res.data.data.forEach(productPrice => {
                        if (! models.some(m => m.model === productPrice.product.model)) {
                            models.push({
                                model: productPrice.product.model,
                                currentPrice: productPrice.price,
                                price: null,
                                isNew: false,
                                supplier_id: this.form.supplier.id,
                                updated_at: productPrice.updated_at
                            })
                        }
                    });

                    this.form.models = models;
                    this.loadingProducts = false;

                }).catch(err => {
                    this.loadingProducts = false;
                })
            },

            addModel(model) {
                this.form.models.push({
                    model: model.model,
                    isNew: true,
                    currentPrice: null,
                    price: null,
                    supplier_id: this.form.supplier.id,
                })
            },

            changeModel(product, index) {
                this.form.models[index].model = product.model;
            },

            removeModel(index) {
                this.form.models.splice(index, 1);
            }
        },

        computed: {
            modelSelected() {
                return this.form.models.map(m => m.model).join(',');
            }
        }
    }
</script>
