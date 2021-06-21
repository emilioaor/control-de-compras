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

                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a
                                class="nav-link pointer"
                                :class="{active: form.type === 'file'}"
                                @click="form.type = 'file'">{{ t('form.loadFile') }}</a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link pointer"
                                :class="{active: form.type === 'by_product'}"
                                @click="form.type = 'by_product'">{{ t('form.pricesByProduct') }}</a>
                        </li>
                    </ul>

                    <div class="row px-3">
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
                form: {
                    supplier: null,
                    supplier_id: null,
                    type: 'file',
                    file: null
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

                    location.reload();

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
        }
    }
</script>
