<template>
    <form>
        <div class="row">
            <div class="col-sm-5 form-group input-group">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    :placeholder="t('form.filter')"
                    maxlength="30"
                    v-model="search"
                >
                <span class="input-group-btn">
                    <button class="btn btn-secondary">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
            <div class="col-sm-7 form-group">
                <slot name="total"></slot>
            </div>

            <div class="col-12 mb-2" v-if="initialSearch">
                <strong>
                    <a :href="pathname" class="text-danger">X</a>
                    Filtered by:
                </strong>
                {{ initialSearch }}
            </div>
        </div>
    </form>
</template>

<script>
    export default {
        name: 'TableFilter',

        mounted() {
            const url = new URL(location.href);

            this.initialSearch = url.searchParams.get('search');
            this.search = url.searchParams.get('search');
            this.pathname = location.pathname;
        },

        data() {
            return {
                initialSearch: null,
                search: null,
                pathname: null
            }
        }
    }
</script>
