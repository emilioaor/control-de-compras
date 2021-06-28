<template>
    <div class="container">
        <div class="techland-form">
            <div class="card">
                <div class="card-header">
                    <div v-if="editData">
                        <i class="fa fa-edit"></i> {{ t('form.edit') }} {{ t('menu.suppliers') }}
                    </div>
                    <div v-else>
                        <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.suppliers') }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.name') }}</label>
                            <input
                                type="text"
                                name="name"
                                class="form-control"
                                :class="{'is-invalid': errors.has('name') || exists}"
                                v-model="form.name"
                                maxlength="255"
                                v-validate
                                data-vv-rules="required"
                            >
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('name', 'required')">
                                <strong>
                                    {{ t('validation.required', {attribute: 'name'}) }}
                                </strong>
                            </span>

                            <span class="invalid-feedback" role="alert" v-if="exists">
                                <strong>{{ t('validation.unique', {attribute: 'name'}) }}</strong>
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
        name: 'supplier-form',
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
                    name: null,
                }
            }
        },

        methods: {

            validateForm() {
                return this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {
                this.loading = true;

                const apiService = this.editData ?
                    ApiService.put('/buyer/supplier/' + this.form.uuid, this.form) :
                    ApiService.post('/buyer/supplier', this.form)
                ;

                apiService.then(res => {

                    location.href = res.data.redirect;

                }).catch(err => {
                    this.loading = false;

                    if (err.response.status === 422) {
                        if (err.response.data.errors.name) {
                            this.exists = true;
                        }
                    }
                })
            },
        },

        watch: {
            "form.name"() {
                this.exists = false;
            }
        }
    }
</script>
