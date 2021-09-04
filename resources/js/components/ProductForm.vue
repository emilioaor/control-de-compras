<template>
    <div class="container">
        <div class="techland-form">
            <div class="card">
                <div class="card-header">
                    <div v-if="editData">
                        <i class="fa fa-edit"></i> {{ t('form.edit') }} {{ t('menu.products') }}
                    </div>
                    <div v-else>
                        <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.products') }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-4 form-group" v-if="editData">
                            <label>{{ t('validation.attributes.code') }}</label>
                            <input
                                type="text"
                                name="upc"
                                class="form-control"
                                v-model="form.upc"
                                disabled
                            >
                        </div>

                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.brand') }}</label>
                            <select
                                name="brand_id"
                                id="brand_id"
                                class="form-control"
                                :class="{'is-invalid': errors.has('brand_id')}"
                                v-model="form.brand_id"
                                v-validate
                                data-vv-rules="required"
                            >
                                <option
                                    v-for="brand in brands"
                                    :key="brand.id"
                                    :value="brand.id"
                                >{{ brand.name }}</option>
                            </select>

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('brand_id', 'required')">
                                <strong>
                                    {{ t('validation.required', {attribute: 'brand'}) }}
                                </strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.model') }}</label>
                            <input
                                    type="text"
                                    name="model"
                                    class="form-control"
                                    :class="{'is-invalid': errors.has('model')}"
                                    v-model="form.model"
                                    maxlength="30"
                                    v-validate
                                    data-vv-rules="required"
                            >
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('model', 'required')">
                                <strong>
                                    {{ t('validation.required', {attribute: 'model'}) }}
                                </strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.description') }}</label>
                            <input
                                type="text"
                                name="description"
                                class="form-control"
                                :class="{'is-invalid': errors.has('description')}"
                                v-model="form.description"
                                v-validate
                                data-vv-rules="required"
                            >
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('description', 'required')">
                                <strong>
                                    {{ t('validation.required', {attribute: 'description'}) }}
                                </strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-md-4 form-group" v-if="editData">
                            <label>{{ t('validation.attributes.createdAt') }}</label>
                            <input type="text" class="form-control" :value="form.created_at | date(true)" disabled>
                        </div>
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
        name: 'product-form',
        props: {
            editData: {
                type: Object,
                required: false
            },
            brands: {
                type: Array,
                required: false
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {
                    ...this.editData
                }
            }
        },

        data() {
            return {
                loading: false,
                form: {
                    upc: null,
                    model: null,
                    description: null,
                    brand_id: null
                }
            }
        },

        methods: {

            validateForm() {
                return this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {
                const apiService = this.editData ?
                    ApiService.put('/buyer/product/' + this.form.uuid, this.form) :
                    ApiService.post('/buyer/product', this.form)
                ;

                this.loading = true;

                apiService.then(res => {

                    location.href = res.data.redirect;

                }).catch(err => {
                    this.loading = false;
                })
            },
        }
    }
</script>
