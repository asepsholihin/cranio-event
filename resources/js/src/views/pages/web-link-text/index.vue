<template>
  <div>
    <add-sidebar :is-add-sidebar-active.sync="isAddSidebarActive" :page-name-options="pageNameOptions"
      @refetch-data="refetchData" v-if="hasPermission('web-link-text-add-or-edit')" />

    <!-- Table Container Card -->
    <b-card no-body class="mb-0">

      <div class="m-2">

        <!-- Table Top -->
        <b-row>

          <!-- Per Page -->
          <b-col cols="12" md="6" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
            <label>Show</label>
            <v-select v-model="perPage" :options="perPageOptions" :clearable="false"
              class="per-page-selector d-inline-block mx-50" />
            <label>entries</label>
          </b-col>

          <!-- Search -->
          <b-col cols="12" md="6">
            <div class="d-flex align-items-center justify-content-end">
              <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1" placeholder="Search..." />
              <b-button variant="primary" @click="isAddSidebarActive = true" v-if="hasPermission('web-link-text-add-or-edit')">
                <span class="text-nowrap">Add Link Text</span>
              </b-button>
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refUserListTable" class="position-relative" :items="fetchSales" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc">

        <!-- Column: Status -->
        <template #cell(status)="data">
          <b-badge pill :variant="`light-${resolveSaleStatusVariant(data.item.status)}`" class="text-capitalize">
            {{ resolveSaleStatusName(data.item.status) }}
          </b-badge>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item :to="{ name: 'web-link-text-detail', params: { id: data.item.id, name: data.item.sales_name } }">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Details</span>
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteSale(data.item)">
              <feather-icon icon="Trash2Icon" />
              <span class="align-middle ml-50">Delete</span>
            </b-dropdown-item>
          </b-dropdown>
        </template>

      </b-table>
      <div class="mx-2 mb-2">
        <b-row>

          <b-col cols="12" sm="6" class="d-flex align-items-center justify-content-center justify-content-sm-start">
            <span class="text-muted">Showing {{ dataMeta.from }} to {{ dataMeta.to }} of {{ dataMeta.of }}
              entries</span>
          </b-col>
          <!-- Pagination -->
          <b-col cols="12" sm="6" class="d-flex align-items-center justify-content-center justify-content-sm-end">

            <b-pagination v-model="currentPage" :total-rows="totalSales" :per-page="perPage" first-number last-number
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
  BButton,
  BTable,
  BMedia,
  BAvatar,
  BLink,
  BBadge,
  BDropdown,
  BDropdownItem,
  BPagination,
} from 'bootstrap-vue'
import vSelect from 'vue-select'
import { ref } from '@vue/composition-api'
import useDataList from './useDataList'
import { deleteData } from '@/network/web-link-text'
import addSidebar from './addSidebar.vue'
import { hasPermission } from '@/auth/utils'

export default {
  components: {
    addSidebar,

    BCard,
    BRow,
    BCol,
    BFormInput,
    BButton,
    BTable,
    BMedia,
    BAvatar,
    BLink,
    BBadge,
    BDropdown,
    BDropdownItem,
    BPagination,

    vSelect,
  },
  setup() {
    const isAddSidebarActive = ref(false)

    const pageNameOptions = [
      { label: 'Homepage', value: 1 },
      { label: 'Contact Us Page', value: 2 },
      { label: 'Umrah Parent Page', value: 3 },
      { label: 'Umrah Bersama Ust. Salim', value: 9 },
      { label: 'Umrah Lebih Hemat', value: 10 },
      { label: 'Umrah Lebih Nyaman', value: 11 },
      { label: 'Tabungan Umrah', value: 12 },
      { label: 'Product Detail Page', value: 4 },
      { label: 'Haji Parent Page', value: 13 },
      { label: 'Haji Khusus', value: 5 },
      { label: 'Haji Furoda', value: 6 },
      { label: 'Wisata Halal', value: 7 },
      { label: 'Tombol Daftar', value: 8 },
      { label: 'Badal Parent', value: 14 },
      { label: 'Badal Haji', value: 15 },
      { label: 'Badal Umroh', value: 16 },
      // new page
      { label: 'Umrah Bersama Ust. Salim Yaqin', value: 17 },
      { label: 'Umrah Bersama Ust. Salim Onyx', value: 18 },
      { label: 'Umrah Bersama Ust. Salim Ruby', value: 19 },
      { label: 'Umrah Bersama Ust. Salim Sapphire', value: 20 },
      { label: 'Umrah Bersama Ust. Salim Sapphire Plus', value: 21 },
      { label: 'Umrah Lebih Hemat Yaqin', value: 22 },
      { label: 'Umrah Lebih Hemat Konsorsium', value: 23 },
      { label: 'Umrah Lebih Hemat Onyx', value: 24 },
      { label: 'Umrah Lebih Hemat Ruby', value: 25 },
      { label: 'Umrah Lebih Nyaman Umrah Plus', value: 26 },
      { label: 'Umrah Lebih Nyaman Sapphire', value: 27 },
      { label: 'Umrah Lebih Nyaman Sapphire Plus', value: 28 },

      // page name
      { label: 'Google Demand Gen', value: 29 },
      { label: 'Google GDN Brand', value: 30 },
      { label: 'Google GDN Umroh', value: 31 },
      { label: 'Google GDN Haji', value: 32 },
      { label: 'Google SEM Haji', value: 33 },
      { label: 'Google SEM Umroh', value: 34 },
      { label: 'Google Youtube Bumper', value: 35 },
      { label: 'Google Youtube CPV', value: 36 },
      { label: 'Google Youtube CPM', value: 37 },
      { label: 'Meta Awareness Umroh', value: 38 },
      { label: 'Meta Awareness Haji', value: 39 },
      { label: 'Meta Traffic Umroh', value: 40 },
      { label: 'Meta Traffic Haji', value: 41 },
      { label: 'Meta Engage Umroh', value: 42 },
      { label: 'Meta Engage Haji', value: 43 },

      { label: 'Promo Ramadhan', value: 44 },
      { label: 'Pop Up Deals', value: 45 },
    ]

    const {
      fetchSales,
      tableColumns,
      perPage,
      currentPage,
      totalSales,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,

      // UI
      resolveSaleStatusVariant,
      resolveSaleStatusName,

      // Extra Filters
      roleFilter,
      planFilter,
      statusFilter,
    } = useDataList()

    return {
      // Sidebar
      isAddSidebarActive,

      fetchSales,
      tableColumns,
      perPage,
      currentPage,
      totalSales,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,
      pageNameOptions,

      // UI
      resolveSaleStatusVariant,
      resolveSaleStatusName,

      // Extra Filters
      roleFilter,
      planFilter,
      statusFilter,

      hasPermission
    }
  },
  created() {

  },
  methods : {
    deleteSale(item){
        this.$swal({
        title: `Delete ${item.page_name}?`,
        text: "It cannot be reverted",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
          confirmButton: 'btn btn-danger',
          cancelButton: 'btn btn-outline-primary ml-1',
        },
        buttonsStyling: false,
      }).then(result => {
            if (result.value) {
                deleteData(item.id).then(response => {
                    this.refetchData()
                })
            }
      })
    }
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
