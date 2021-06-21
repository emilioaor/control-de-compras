<template>
    <div class="container">
        <div class="techland-form">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-list-alt"></i> {{ t('menu.productReport') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.upc') }}</label>
                            <input
                                type="text"
                                name="upc"
                                class="form-control"
                                :class="{'is-invalid': errors.has('upc')}"
                                v-model="form.upc"
                                v-validate
                                data-vv-rules=""
                            >
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('upc', 'required')">
                                <strong>
                                    {{ t('validation.required', {attribute: 'upc'}) }}
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
                                v-validate
                                data-vv-rules=""
                            >
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('model', 'required')">
                                <strong>
                                    {{ t('validation.required', {attribute: 'model'}) }}
                                </strong>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <i class="spinner-border spinner-border-sm" v-if="loading"></i>

                    <button class="btn btn-success" v-if="! loading" @click="validateForm()">
                        <i class="fa fa-list-alt"></i>
                        {{ t('form.report') }}
                    </button>

                    <a
                        class="btn btn-info text-white"
                        :href="downloadRoute"
                        v-if="data.length"
                    >
                        <i class="fa fa-download"></i>
                        {{ t('form.download') }}
                    </a>
                </div>

                <div class="card-body" v-if="data.length">
                    <table class="table table-responsive-sm">
                        <thead>
                            <tr>
                                <th>{{ t('validation.attributes.upc') }}</th>
                                <th>{{ t('validation.attributes.model') }}</th>
                                <th>{{ t('validation.attributes.description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, i) in data">
                                <td>{{ item.upc }}</td>
                                <td>{{ item.model }}</td>
                                <td>{{ item.description }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import ApiService from "../services/ApiService";

    export default {
        name: 'product-report',

        data() {
            return {
                loading: false,
                form: {
                    upc: null,
                    model: null
                },
                data: []
            }
        },

        methods: {

            validateForm() {
                return this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {
                this.loading = true;
                this.data = [];

                ApiService.post('/buyer/report/product', this.form).then(res => {
                    this.loading = false;
                    this.data = res.data.data;
                }).catch(err => {
                    this.loading = false;
                })
            },
        },

        computed: {
            downloadRoute() {
                let route = '/buyer/download/product';

                route += (this.form.model ? '&model=' + this.form.model : '')
                route += (this.form.upc ? '&upc=' + this.form.upc : '')
                route = route.replace('&', '?');

                return route;
            }
        }
    }
</script>
