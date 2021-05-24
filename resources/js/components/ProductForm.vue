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
                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.upc') }}</label>
                            <input
                                type="text"
                                name="upc"
                                class="form-control"
                                :class="{'is-invalid': errors.has('upc') || exists}"
                                v-model="form.upc"
                                maxlength="20"
                                v-validate
                                data-vv-rules="required"
                            >
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('upc', 'required')">
                                <strong>
                                    {{ t('validation.required', {attribute: 'upc'}) }}
                                </strong>
                            </span>

                            <span class="invalid-feedback" role="alert" v-if="exists">
                                <strong>{{ t('validation.unique', {attribute: 'upc'}) }}</strong>
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
                exists: false,
                form: {
                    upc: null,
                    model: null,
                    description: null
                }
            }
        },

        methods: {

            validateForm() {
                return this.$validator.validateAll().then(res => res && this.checkIfProductExists());
            },

            checkIfProductExists() {
                this.loading = true;

                ApiService.post('/admin/product/exists', {upc: this.form.upc})
                    .then(res => {
                        if (!res.data.data || (this.editData && this.editData.uuid === res.data.data.uuid)) {
                            this.sendForm();
                        } else {
                            this.exists = true;
                            this.loading = false;
                        }
                    })
                    .catch(err => {
                        this.loading = false;
                    })
            },

            sendForm() {

                const apiService = this.editData ?
                    ApiService.put('/admin/product/' + this.form.uuid, this.form) :
                    ApiService.post('/admin/product', this.form)
                ;

                this.loading = true;

                apiService.then(res => {

                    location.href = res.data.redirect;

                }).catch(err => {
                    this.loading = false;
                })
            },
        },

        watch: {
            "form.upc"() {
                this.exists = false;
            }
        }
    }
</script>
