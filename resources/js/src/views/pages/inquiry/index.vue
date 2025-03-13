<template>
  <div>
    <!-- Table Container Card -->
    <b-card no-body class="mb-0">

      <b-modal v-model="showInquiry" ok-title="Close" @hidden="resetModal" centered no-close-on-backdrop ok-only
        :title="`View Inquiry JIBB-${inquiryDetail.id}`">
        <table width="100%">
          <tr>
            <td width="40%" valign="top">Inquiry Number</td>
            <td width="5%" valign="top">:</td>
            <td width="55%" valign="top">JIBB-{{ inquiryDetail.id }}</td>
          </tr>
          <tr>
            <td valign="top">Date</td>
            <td valign="top">:</td>
            <td valign="top">{{ inquiryDetail.created_at }}</td>
          </tr>
          <tr>
            <td valign="top">Full Name</td>
            <td valign="top">:</td>
            <td valign="top">{{ inquiryDetail.full_name }}</td>
          </tr>
          <tr>
            <td valign="top">Whatsapp</td>
            <td valign="top">:</td>
            <td valign="top">{{ inquiryDetail.wa_number }}</td>
          </tr>
          <tr>
            <td valign="top">Email</td>
            <td valign="top">:</td>
            <td valign="top">{{ inquiryDetail.email }}</td>
          </tr>
          <tr>
            <td valign="top">Message</td>
            <td valign="top">:</td>
            <td valign="top">{{ inquiryDetail.message }}</td>
          </tr>
        </table>
      </b-modal>

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
              <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1"
                placeholder="Search..." />
              <b-button variant="primary" @click="exportInquiry()">
                <span class="text-nowrap">Export Data</span>
              </b-button>
            </div>
          </b-col>
        </b-row>

        <b-row class="mt-2">
          <b-col cols="12" md="4">
            <p>Filter by From Page</p>
            <v-select v-model="pageFilter" placeholder="Choose From Page" :options="fromPageOptions" :filterable="false"
              :reduce="name => name.from_page" label="from_page">
              <template slot="no-options"> Type to search trip.. </template>
              <template slot="option" slot-scope="option">
                <b-media vertical-align="center"> {{ option.from_page }}</b-media>
              </template>
              <template slot="selected-option" slot-scope="option">
                <div class="selected d-center">
                  <b-media vertical-align="center">{{ option.from_page }}</b-media>
                </div>
              </template>
            </v-select>
          </b-col>
          <b-col cols="12" md="4">
            <p>Filter by Date Range</p>
            <div class="d-flex align-items-center">
              <flat-pickr v-model="startDate" class="form-control mr-1" placeholder="Start Date"
                :config="{ altInput: true }" />
              <flat-pickr v-model="endDate" class="form-control mr-1" placeholder="End Date"
                :config="{ altInput: true }" />
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refInquiryListTable" class="position-relative" :items="fetchInquiries" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc">

        <!-- Column: Package -->
        <template #cell(package_type)="data">
          <b-link :to="{ name: 'participant-detail', params: { id: data.item.id } }"
            class="font-weight-bold d-block text-nowrap">
            {{ resolvePackageType(data.item.package_type) }}
          </b-link>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item @click="showPayment(data.item)">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">View Message</span>
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteInquiry(data.item)">
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

            <b-pagination v-model="currentPage" :total-rows="totalInquiries" :per-page="perPage" first-number last-number
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
import useDataList from './useDataList'
import { exportInquiry, deleteData, getEquipmentGroupBy } from '@/network/web-inquiry'
import { hasPermission } from '@/auth/utils'
import flatPickr from 'vue-flatpickr-component'

export default {
  components: {

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
    flatPickr,
  },
  setup() {
    const {
      fetchInquiries,
      tableColumns,
      perPage,
      currentPage,
      totalInquiries,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refInquiryListTable,
      startDate,
      endDate,
      refetchData,

      // Extra Filters
      roleFilter,
      planFilter,
      statusFilter,
      pageFilter
    } = useDataList()

    return {

      fetchInquiries,
      tableColumns,
      perPage,
      currentPage,
      totalInquiries,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refInquiryListTable,
      startDate,
      endDate,
      refetchData,

      // Extra Filters
      roleFilter,
      planFilter,
      statusFilter,
      pageFilter,

      hasPermission
    }
  },
  data() {
    const fromPageOptions = []
    getEquipmentGroupBy({ q: '' }).then(res => {
      this.fromPageOptions = res.data
    }).catch(error => {
      this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
    })
    return {
      showInquiry: false,
      inquiryDetail: {},
      fromPageOptions
    }
  },
  created() {

  },
  methods: {
    showPayment(data) {
      this.showInquiry = true
      this.inquiryDetail = data
    },
    exportInquiry() {
      exportInquiry().then(response => {
        window.location = response.data.downloadLink
      }).catch(error => {
        this.isSubmitModal = false
        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
      })
    },
    resetModal() {

    },
    deleteInquiry(item) {
      this.$swal({
        title: `Delete Inquiry ${item.full_name}?`,
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
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>