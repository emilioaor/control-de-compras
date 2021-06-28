<template>
    <div class="container">
        <div class="techland-form">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-list-alt"></i> {{ t('menu.comparativeReport') }}
                </div>
                <div class="card-body">
                    <template v-if="loading">
                        <i class="spinner-border spinner-border-sm"></i>
                    </template>

                    <template v-else>
                        <div class="my-2 d-flex align-items-center zoom">
                            <!--<i class="fa fa-minus-circle pointer" @click="setZoom(-10)"></i>

                            <div class="d-inline-block mx-2" v-show="">
                                {{ zoom }}%
                            </div>

                            <i class="fa fa-plus-circle pointer" @click="setZoom(10)"></i>-->

                            <div class="bg-warning rounded p-1 ml-2">
                                {{ t('form.yellow') }}:
                                {{ t('form.priceNotUpgraded') }}
                            </div>
                        </div>


                        <table class="table" :style="'font-size: ' + fs + 'px'">
                            <thead>
                            <tr>
                                <th>{{ t('validation.attributes.product') }}</th>
                                <th class="text-center" v-for="supplier in suppliers">
                                    {{ supplier.name }}
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(product, i) in productsC">
                                    <td>{{ product.upc }} / {{ product.description }}</td>
                                    <td
                                        v-for="supplier in product.suppliers"
                                        class="text-center"
                                        :class="{'bg-warning': supplier.price && ! supplier.isOpenWeek}"
                                    >
                                        <template v-if="supplier.price">
                                            {{ supplier.price }}
                                            <small>({{ supplier.updated_at | date }})</small>
                                        </template>
                                        <template v-else>
                                            -
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </template>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import ApiService from "../services/ApiService";

    export default {
        name: 'comparative-report',

        data() {
            return {
                loading: false,
                form: {
                },
                products: [],
                suppliers: [],
                productPrices: [],
                zoom: 100,
                fs: 14
            }
        },

        created() {
            this.sendForm();
        },

        methods: {

            validateForm() {
                return this.$validator.validateAll().then(res => res && this.sendForm());
            },

            sendForm() {
                this.loading = true;
                this.data = [];

                ApiService.post('/buyer/report/comparative', this.form).then(res => {
                    this.loading = false;
                    this.products = res.data.products;
                    this.suppliers = res.data.suppliers;
                    this.productPrices = res.data.productPrices;
                }).catch(err => {
                    this.loading = false;
                })
            },

            setZoom(percentage) {
                if (this.zoom > 10) {
                    this.zoom += percentage;
                    this.fs = ((this.zoom * 14) / 100).toFixed(2)
                }
            }
        },

        computed: {
            productsC() {
                return this.products.map(p => {
                    return {
                        upc: p.upc,
                        description: p.description,
                        suppliers: this.suppliers.map(s => {

                            const pp = this.productPrices.find(pp => pp.product_id === p.id && pp.supplier_id === s.id);

                            return {
                                ...s,
                                price: pp?.price ?? null,
                                updated_at: pp?.updated_at ?? null,
                                isOpenWeek: pp?.isOpenWeek ?? null
                            }
                        })
                    }
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    .zoom {
        font-size: 18px;
    }
</style>
