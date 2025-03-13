<template>
    <div>
        <!-- Table Container Card -->
        <b-card no-body class="mb-0">
            <div class="mx-2 mt-2" v-if="filterFormView">
                <b-row>
                    <b-col cols="12" md="3" class="mb-md-1 mb-2">
                        <label>Gender</label>
                        <v-select v-model="genderFilter" :options="genderOptions" class="w-100" :reduce="val => val.value" />
                    </b-col>
                </b-row>
            </div>
            <b-button variant="outline-primary" size="sm" class="mx-2 mt-1" @click="filterFormView = !filterFormView">Filter <feather-icon :icon="(filterFormView) ? `ArrowUpIcon` : `ArrowDownIcon`" /></b-button>

            <div class="m-2">
                <!-- Table Top -->
                <b-row>
                    <!-- Per Page -->
                    <b-col cols="12" md="4" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
                        <label>Show</label>
                        <v-select v-model="perPage" :options="perPageOptions" :clearable="false" class="per-page-selector d-inline-block mx-50" />
                        <label>entries</label>
                    </b-col>
                    <b-col cols="12" md="8">
                        <div class="d-lg-flex align-items-center justify-content-end">
                            <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1 mb-lg-0 mb-2" placeholder="Search..." />
                            <div>
                                <b-form-checkbox v-model="duplicateFilter" value="1" unchecked-value="" inline>
                                    <span class="text-nowrap">Duplicate Data</span>
                                </b-form-checkbox>
                            </div>
                        </div>
                    </b-col>
                </b-row>
            </div>

            <b-table sticky-header="500px" thead-class="sticky-top" ref="refUserListTable" class="position-relative" :items="fetchUsers" responsive hover bordered small
                :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
                :sort-desc.sync="isSortDirDesc">
            </b-table>

            <div class="mx-2 mb-2">
                <b-row>

                <b-col cols="12" sm="6" class="d-flex align-items-center justify-content-center justify-content-sm-start">
                    <span class="text-muted">Showing {{ dataMeta.from }} to {{ dataMeta.to }} of {{ dataMeta.of }}
                    entries</span>
                </b-col>
                <!-- Pagination -->
                <b-col cols="12" sm="6" class="d-flex align-items-center justify-content-center justify-content-sm-end">

                    <b-pagination v-model="currentPage" :total-rows="totalUsers" :per-page="perPage" first-number last-number
                    class="mb-0 mt-1 mt-sm-0" prev-class="prev-item" next-class="next-item">
                    <template #prev-text>
                        <feather-icon icon="ChevronLeftIcon" size="18" />
                    </template>
                    <template #next-text>
                        <feather-icon icon="ChevronRightIcon" size="18" />
                    </template>
                    </b-pagination>

                </b-col>

                </b-row>
            </div>
        </b-card>
    </div>
</template>

<script>
import {
    BCard,
    BRow,
    BCol,
    BFormInput,
    BFormCheckbox,
    BButton,
    BTable,
    BMedia,
    BAvatar,
    BLink,
    BBadge,
    BDropdown,
    BDropdownItem,
    BPagination,
    BForm,
    BFormGroup,
    BSpinner
} from 'bootstrap-vue'
import vSelect from 'vue-select'
import { avatarText, formatDateShort } from '@core/utils/filter'
import useDataRawList from './useDataRawList'
import { deleteData, getJobSearch } from '@/network/participant'
import addSidebar from './addSidebar.vue'
import importSidebar from './importSidebar.vue'
import { hasPermission } from '@/auth/utils'
import _ from 'lodash'
import { getTripSearch, getPackages } from '@/network/booking-order'
import { getBookingSearch } from '@/network/umroh-booking-seat'

export default {
    components: {
        addSidebar,
        importSidebar,

        BCard,
        BRow,
        BCol,
        BForm,
        BFormInput,
        BFormCheckbox,
        BButton,
        BTable,
        BMedia,
        BAvatar,
        BLink,
        BBadge,
        BDropdown,
        BDropdownItem,
        BPagination,
        BFormGroup,
        BSpinner,

        vSelect,
    },
    setup() {
        const genderOptions = [{ label: 'Man', value: 1 }, { label: 'Woman', value: 2 }]
       
        const {
        fetchUsers,
        tableColumns,
        perPage,
        currentPage,
        totalUsers,
        dataMeta,
        perPageOptions,
        searchQuery,
        sortBy,
        isSortDirDesc,
        refUserListTable,
        refetchData,

        // Extra Filters
        genderFilter,
        duplicateFilter,
        } = useDataRawList()

        return {
        fetchUsers,
        tableColumns,
        perPage,
        currentPage,
        totalUsers,
        dataMeta,
        perPageOptions,
        searchQuery,
        sortBy,
        isSortDirDesc,
        refUserListTable,
        refetchData,
        genderOptions,

        // Filter
        avatarText,
        formatDateShort,

        // Extra Filters
        genderFilter,
        duplicateFilter,

        hasPermission
        }
    },
    created() {

    },
    data() {
        return {
            filter: {},
            isLoading: false,
            filterFormView: false,
        }
    },
    methods : {
    },
}
</script>

<style lang="scss" scoped>
    .per-page-selector {
        width: 90px;
    }
</style>

<style lang="scss">
    @import '~@resources/scss/vue/libs/vue-select.scss';
    @import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
