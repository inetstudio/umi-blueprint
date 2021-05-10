<template>
    <div class="select-wrap filter-select" :class="wrapClass">
        <select v-select2 class="js-example-basic-single"
                v-model="filterVal"
                v-on:change="changeVal($event)"
                :name="name"
                :class="selectClass"
                :multiple="single !== true"
                :data-placeholder="placeholder">

            <option value="all" v-model="filterName" v-if="showAll">
                Все
            </option>

            <option v-for="(prop, val) in options" v-model="filterName" :value="prop.id" :disabled="prop.disabled">
                {{ prop.name }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    name: 'Select2Filter',
    data() {
        return {
            filterVal: [],
            filterName: '',
            options: JSON.parse(this.values) || {},
        }
    },
    watch: {
        values: function (newValue, oldValue) {
            if (oldValue !== newValue) {
                this.options = newValue;
            }
        }
    },
    props: {
        values: [String, Object, Array],
        name: {
            type: String,
            default: '_filter'
        },
        placeholder: {
            type: String,
            default: 'Все'
        },
        selectClass: String,
        wrapClass: String,
        "data-filter-for": String,
        single: Boolean,
        showAll: {
            type: Boolean,
            default: true
        }
    },
    methods: {
        getTitle: function (prop) {
            return _.isObject(prop) ? prop.title || prop.name : prop;
        },
        changeVal: function (event) {
            let component = this,
                optionName = '';

            let optionsObj = _.keyBy(component.options, 'id');

            if (component.filterVal in optionsObj) {
                optionName = optionsObj[component.filterVal][component.dataFilterFor];
            }

            this.$store.commit('addSelectFilter', {
                name: component.name,
                value: component.filterVal
            });
        },
    },
    mounted: function () {

        let component = this;

        $('.js-example-basic-single').each(function () {
            var sel2 = $(this),
                placeholder = $(this).data('placeholder');

            sel2.select2({
                width: 184,
                dropdownParent: $('html'),
                minimumResultsForSearch: Infinity,
                tags: true,
                change: function (e) {
                    // console.log(e);
                },
                placeholder: placeholder
            }).on('change', function (e) {
                var select = $(this);

                if (select.find('option[value=all]').is(':selected')) {
                    select.find('option').prop('selected', false);
                    select.trigger("change.select2");
                }
            });

        });
    }
}
</script>
