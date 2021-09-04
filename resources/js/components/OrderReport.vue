<template>
    <div class="container">
        <div class="techland-form">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-list-alt"></i> {{ t('menu.orderReport') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-4 form-group">
                            <label>{{ t('validation.attributes.code') }}</label>
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
                            <label>{{ t('validation.attributes.brand') }}</label>
                            <input
                                type="text"
                                name="brand"
                                class="form-control"
                                :class="{'is-invalid': errors.has('brand')}"
                                v-model="form.brand"
                                v-validate
                                data-vv-rules=""
                            >
                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('brand', 'required')">
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

                    <button class="btn btn-info text-white" v-if="data.length" @click="downloadExcel()">
                        <i class="fa fa-download"></i>
                        {{ t('form.download') }}
                    </button>
                </div>

                <div class="card-body" v-if="data.length">
                    <table class="table table-responsive-sm">
                        <thead>
                            <tr>
                                <th>{{ t('validation.attributes.order') }}</th>
                                <th>{{ t('validation.attributes.createdAt') }}</th>
                                <th>{{ t('validation.attributes.brand') }}</th>
                                <th>{{ t('validation.attributes.model') }}</th>
                                <th>{{ t('validation.attributes.upc') }}</th>
                                <th>{{ t('validation.attributes.product') }}</th>
                                <th>{{ t('validation.attributes.seller') }}</th>
                                <th>{{ t('validation.attributes.customer') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(item, i) in data">
                                <td>{{ item.number }}</td>
                                <td>{{ item.created_at | date(true) }}</td>
                                <td>{{ item.brand }}</td>
                                <td>{{ item.upc }}</td>
                                <td>{{ item.model }}</td>
                                <td>{{ item.description }}</td>
                                <td>{{ item.seller }}</td>
                                <td>{{ item.customer_name }}</td>
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
    import XLSX from "xlsx";

    export default {
        name: 'order-report',

        data() {
            return {
                loading: false,
                form: {
                    upc: null,
                    model: null,
                    brand: null
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

                ApiService.post('/buyer/report/order', this.form).then(res => {
                    this.loading = false;
                    this.data = res.data.data;
                }).catch(err => {
                    this.loading = false;
                })
            },

            downloadExcel() {
                const dataToExport = [...this.data];

                let data = XLSX.utils.json_to_sheet(dataToExport);
                const workbook = XLSX.utils.book_new();
                const filename = 'order-report';
                XLSX.utils.book_append_sheet(workbook, data, filename);
                XLSX.writeFile(workbook, `${filename}.xlsx`);

            },
        }
    }
</script>
