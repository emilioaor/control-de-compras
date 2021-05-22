<template>
    <div class="container">
        <form class="techland-form" @submit.prevent="validateForm()">
            <div class="card">
                <div class="card-header">
                    <div v-if="editData">
                        <i class="fa fa-edit"></i> {{ t('form.edit') }} {{ t('menu.users') }}
                    </div>
                    <div v-else>
                        <i class="fa fa-plus"></i> {{ t('form.add') }} {{ t('menu.users') }}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-lg-4 form-group">
                            <label for="name">{{ t('validation.attributes.name') }}</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control"
                                :class="{'is-invalid': errors.has('name')}"
                                v-model="form.name"
                                v-validate
                                data-vv-rules="required"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('name', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'name'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group">
                            <label for="email">{{ t('validation.attributes.email') }}</label>
                            <input
                                type="text"
                                id="email"
                                name="email"
                                class="form-control"
                                :class="{'is-invalid': errors.has('email') || exists}"
                                v-model="form.email"
                                v-validate
                                data-vv-rules="required|email"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'email'}) }}</strong>
                            </span>

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('email', 'email')">
                                <strong>{{ t('validation.email', {attribute: 'email'}) }}</strong>
                            </span>

                            <span class="invalid-feedback" role="alert" v-if="exists">
                                <strong>{{ t('validation.unique', {attribute: 'email'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group">
                            <label for="role">{{ t('validation.attributes.role') }}</label>
                            <select
                                name="role"
                                id="role"
                                class="form-control"
                                v-model="form.role"
                            >
                                <option
                                    v-for="(role, value) in rolesAvailable"
                                    :value="value">
                                    {{ t('role.' + value) }}
                                </option>
                            </select>
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group">
                            <label for="password">{{ t('validation.attributes.password') }}</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                :class="{'is-invalid': errors.has('password')}"
                                v-model="form.password"
                                v-validate
                                :data-vv-rules="editData ? 'confirmed:password_confirmation' : 'required|confirmed:password_confirmation'"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('password', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'password'}) }}</strong>
                            </span>

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('password', 'confirmed')">
                                <strong>{{ t('validation.confirmed', {attribute: 'password'}) }}</strong>
                            </span>
                        </div>

                        <div class="col-sm-6 col-lg-4 form-group">
                            <label for="password_confirmation">{{ t('validation.attributes.password_confirmation') }}</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                :class="{'is-invalid': errors.has('password_confirmation')}"
                                v-model="form.password_confirmation"
                                v-validate
                                :data-vv-rules="editData ? 'confirmed:password' : 'required|confirmed:password'"
                            >

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('password_confirmation', 'required')">
                                <strong>{{ t('validation.required', {attribute: 'password_confirmation'}) }}</strong>
                            </span>

                            <span class="invalid-feedback" role="alert" v-if="errors.firstByRule('password_confirmation', 'confirmed')">
                                <strong>{{ t('validation.confirmed', {attribute: 'password'}) }}</strong>
                            </span>
                        </div>

                    </div>
                </div>
                <div class="card-footer">
                    <button v-if="!loading" class="btn btn-success">
                        <i class="fa fa-save"></i>
                        {{ t('form.save') }}
                    </button>

                    <i v-if="loading" class="spinner-border spinner-border-sm"></i>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import ApiService from "../services/ApiService";

    export default {
        props: {
            rolesAvailable: {
                type: Object,
                required: true
            },
            editData: {
                type: Object,
                required: false
            }
        },

        mounted() {
            if (this.editData) {
                this.form = {...this.editData};
            }
        },

        data() {
            return {
                loading: false,
                exists: false,
                form: {
                    name: null,
                    email: null,
                    role: 'administrator',
                    password: null,
                    password_confirmation: null,
                    customers: []
                }
            }
        },

        methods: {
            validateForm() {
                this.$validator.validateAll().then(res => res && this.checkIfUserExists());
            },

            checkIfUserExists() {
                this.loading = true;

                ApiService.post('/admin/user/exists', {email: this.form.email})
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
                    ApiService.put('/admin/user/' + this.editData.uuid, this.form) :
                    ApiService.post('/admin/user', this.form)
                ;

                apiService.then(res => {
                    if (res.data.success) {
                        location.href = res.data.redirect;
                    }
                }).catch(err => {
                    this.loading = false;
                })
            }
        },

        watch: {
            "form.email"(value, old) {
                this.exists = false;
            }
        }
    }
</script>
