<template>
    <div>
        <div class="input-group">
            <input
                    type="text"
                    :name="'search' + modalId"
                    :id="'search' + modalId"
                    class="form-control search"
                    :class="inputClass + (! readOnly ? ' pointer' : '')"
                    readonly
                    :value="value"
                    @click="openModal()"
            >
            <span class="input-group-btn">
                <button
                        type="button"
                        class="btn btn-danger"
                        v-if="nullable && value && ! readOnly"
                        @click="clearResult()"
                >
                    X
                </button>

                <button
                        type="button"
                        :id="'open' + modalId"
                        class="btn btn-primary"
                        data-toggle="modal"
                        :data-target="'#' + modalId"
                        @click="filter()"
                        :disabled="disabled"
                        v-if="! readOnly"
                >
                    <i class="fa fa-search"></i>
                </button>
            </span>
        </div>


        <!-- Modal -->
        <div class="modal fade" :id="modalId" tabindex="-1" role="dialog" :aria-labelledby="modalId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <input
                                        type="text"
                                        class="form-control"
                                        :id="'input-filter' + modalId"
                                        v-model="search"
                                        @keypress="filter()"
                                        @keydown="filter()"
                                        @keyup="filter()"
                                >
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-12">

                                <table class="table table-responsive table-striped">
                                    <tr v-for="result in results" :key="result.id">
                                        <td width="95%">{{ result.searchDescription }}</td>
                                        <td width="5%">
                                            <button
                                                    type="button"
                                                    class="btn btn-success"
                                                    @click="selectResult(result)"
                                            >
                                                <i class="fa fa-check"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" :id="'close' + modalId" class="btn btn-secondary" data-dismiss="modal">{{ t('form.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ApiService from '../services/ApiService';

    export default {
        name: 'SearchInput',
        props: {
          route: {
              type: String,
              required: true
          },
          descriptionFields: {
              type: Array,
              required: true
          },
          inputClass: {
            type: String,
            required: false
          },
          nullable: {
              type: Boolean,
              required: false,
              default: false
          },
          value: {
              type: String,
              required: true
          },
          disabled: {
              type: Boolean,
              required: false,
              default: false
          },
          readOnly: {
              type: Boolean,
              required: false,
              default: false
          },

        },

        data() {
            return {
                modalId: 'modal' + Math.round(Math.random() * 1000000),
                search: '',
                results: [],
            }
        },

        methods: {
            filter() {
                window.setTimeout(this.focusFilter, 500);
                this.results = [];

                ApiService.get(this.route + '?search=' + this.search).then(res => {

                    this.results = res.data.data.data.map(current => {
                        let description = '';

                        for (let i of this.descriptionFields) {
                            if (description !== '') {
                                description += ' / ';
                            }

                            description += current[i] ? current[i] : '-';
                        }

                        return {
                            ...current,
                            searchDescription: description
                        }
                    })
                })
            },

            selectResult(result) {
                this.$emit('selectResult', result);
                this.results = [];
                this.search = '';
                document.querySelector('#close' + this.modalId).click();
            },

            clearResult() {
                this.$emit('selectResult', null);
            },

            openModal() {
                const button = document.querySelector('#open' + this.modalId);

                return button && button.click();
            },

            focusFilter() {
                document.querySelector('#input-filter' + this.modalId).focus();
            }
        }
    }
</script>

<style scoped="scoped">
    .search.pointer {
        cursor: pointer;
    }

    .search[readonly] {
        background-color: #ffffff;
    }
</style>
