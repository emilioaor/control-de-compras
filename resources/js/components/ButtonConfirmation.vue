<template>
    <div class="confirmation-container">
        <button
                type="button"
                :class="btnClass"
                :id="'open' + modalId"
                data-toggle="modal"
                :data-target="'#' + modalId"
                :disabled="disabled"
        >
            <i v-if="iconClass" :class="iconClass"></i>
            {{ label }}
        </button>

        <div class="modal fade" :id="modalId" tabindex="-1" role="dialog" :aria-labelledby="modalId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body d-flex justify-content-center">

                        <div class="bg-white p-3 rounded">
                            <h4 class="text-center confirmation">{{ confirmation }}</h4>

                            <div class="d-flex justify-content-center mt-3">

                                <div v-for="(button, i) in buttons" :key="i" class="px-1">
                                    <button type="button" :class="button.btnClass" data-dismiss="modal" @click="confirm(button.code)">
                                        {{ button.label }}
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer d-none">
                        <button type="button" :id="'close' + modalId" class="btn btn-secondary" data-dismiss="modal">{{ t('form.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'ButtonConfirmation',
        props: {
            btnClass: {
                type: String,
                required: false
            },
            iconClass: {
                type: String,
                required: false
            },
            label: {
                type: String,
                required: true
            },
            confirmation: {
                type: String,
                required: true
            },
            buttons: {
                type: Array,
                required: true
            },
            disabled: {
                type: Boolean,
                required: false,
                default: false
            }
        },

        data() {
            return {
                modalId: 'modal' + Math.round(Math.random() * 1000000)
            }
        },

        methods: {
            confirm(code) {
                this.$emit('confirmed', code);
            }
        }
    }
</script>

<style scoped>
    .confirmation-container {
        display: inline-block;
    }
    .modal-content {
        background-color: transparent;
        border: none;
    }
    .modal-content .confirmation {
        font-size: 25px;
    }
    .modal-content button {
        width: 5rem;
        font-size: 20px;
    }
</style>