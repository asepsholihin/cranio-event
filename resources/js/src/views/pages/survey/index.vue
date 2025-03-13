<template>
  <div>
    <!-- Table Container Card -->
    <b-card no-body class="mb-0">
      <div class="m-2">

        <!-- Table Top -->
        <div class="row align-items-center">
            <div class="col-sm-12 mb-1">
              <div class="d-lg-flex align-items-center justify-content-end">
                <div class="mr-1 mb-lg-0 mb-2">
                  <div class="input-group">
                      <flat-pickr v-model="dateFilter" class="form-control" :config="{ mode: 'range', dateFormat: 'd/m/Y' }" placeholder="Pilih Tanggal" />
                      <div class="input-group-append">
                          <button class="btn btn-danger" type="button" title="Clear" @click="clearDate">
                              <feather-icon icon="Trash2Icon" />
                          </button>
                      </div>
                  </div>
                </div>
                <b-button class="mr-1" variant="primary" @click="selectRowAction">
                  <template v-if="!isRowChecked">
                    <span class="text-nowrap">
                      <feather-icon icon="CheckSquareIcon" size="14" /> Select All
                    </span>
                  </template>
                  <template v-else>
                    <span class="text-nowrap">
                      <feather-icon icon="SquareIcon" size="14" /> Deselect All
                    </span>
                  </template>
                </b-button>
                <b-button variant="success" @click="generateReport">
                  <span class="text-nowrap">Buat Laporan</span>
                </b-button>
              </div>
            </div>
        </div>

        <b-row>

          <!-- Per Page -->
          <b-col cols="12" md="4" class="d-flex align-items-center justify-content-start mb-1 mb-md-0">
            <label>Show</label>
            <v-select v-model="perPage" :options="perPageOptions" :clearable="false"
              class="per-page-selector d-inline-block mx-50" :reduce="(label) => label.id" label="name" />
            <label>entries</label>
          </b-col>

          <!-- Search -->
          <b-col cols="12" md="8">
            <div class="d-flex align-items-center justify-content-end">
              <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1"
                placeholder="Search..." />
              <b-button variant="primary" :to="{ name: 'add-survey' }">
                <span class="text-nowrap">Add Survey</span>
              </b-button>
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refListTable" class="position-relative" select-mode="multi" selected-variant="primary" selectable
        :items="fetchSurveys" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc" @row-selected="onRowSelected">

        <template #cell(select)="{ rowSelected }">
          <template v-if="rowSelected">
            <feather-icon icon="CheckSquareIcon" size="20" class="align-end" />
          </template>
          <template v-else>
            <feather-icon icon="SquareIcon" size="20" class="align-end" />
          </template>
        </template>

        <!-- Column: Title -->
        <template #cell(title)="data">
          <b-link :to="{ name: 'survey-detail', params: { id: data.item.id } }">{{ data.item.title }}</b-link>
        </template>

        <!-- Column: Created at -->
        <template #cell(created_at)="data">
          <span class="text-nowrap">{{ formatDateTime(data.item.created_at) }}</span>
        </template>

        <!-- Column: Respons -->
        <template #cell(respons)="data">
          <span class="text-nowrap">{{ data.item.respons }} | <b-link :to="{ name: 'survey-summary', params: { id: data.item.id } }">Summary</b-link></span>
        </template>

        <!-- Column: Updated at -->
        <template #cell(updated_at)="data">
          <span class="text-nowrap">{{ formatDateTime(data.item.updated_at) }}</span>
        </template>

        <!-- Column: Status -->
        <template #cell(status)="data">
          <b-badge pill :variant="`light-${resolveSurveyStatusVariant(data.item.status)}`" class="text-capitalize">
            {{ resolveSurveyStatusName(data.item.status) }}
          </b-badge>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">
            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item :to="{ name: 'survey-detail', params: { id: data.item.id } }">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Details</span>
            </b-dropdown-item>
            <b-dropdown-item variant="info" @click="copyLink(data.item)">
              <feather-icon icon="CopyIcon" />
              <span class="align-middle ml-50">Copy Link</span>
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteSurvey(data.item)">
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

            <b-pagination v-model="currentPage" :total-rows="totalSurveys" :per-page="perPage" first-number last-number
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
  BFormCheckbox,
  BDropdownItem,
  BPagination,
  BDropdownDivider,
} from 'bootstrap-vue'
import vSelect from 'vue-select'
import { formatDateTime, formatDate } from '@core/utils/filter'
import useDataList from './useDataList'
import _ from 'lodash'
import { deleteData, getTrips } from '@/network/survey'
import flatPickr from 'vue-flatpickr-component'
import { hasPermission } from '@/auth/utils'

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
    BFormCheckbox,
    BPagination,
    BDropdownDivider,

    vSelect,
    flatPickr
  },
  setup() {

    const {
      fetchSurveys,
      tableColumns,
      perPage,
      currentPage,
      totalSurveys,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refListTable,
      refetchData,

      // UI
      resolveSurveyStatusVariant,
      resolveSurveyStatusName,

      // Extra Filters
      roleFilter,
      planFilter,
      statusFilter,
      categoryFilter,
      showInPageFilter
    } = useDataList()

    return {
      fetchSurveys,
      tableColumns,
      perPage,
      currentPage,
      totalSurveys,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refListTable,
      refetchData,

      // Filter
      formatDateTime,
      formatDate,

      // UI
      resolveSurveyStatusVariant,
      resolveSurveyStatusName,

      // Extra Filters
      roleFilter,
      planFilter,
      statusFilter,

      hasPermission
    }
  },
  data() {
    return {
      isRowChecked: false,
      selectedIds: [],
      dateFilter: ""
    }
  },
  methods: {
    selectRowAction() {
      if (this.isRowChecked)
        this.$refs.refListTable.clearSelected()
      else
        this.$refs.refListTable.selectAllRows()
    },
    onRowSelected(items) {
      const selectedIds = []
      items.forEach(function (item) {
        selectedIds.push(item.id)
      })

      this.selectedIds = selectedIds

      if (items.length > 0) {
          this.isRowChecked = true
      } else {
          this.isRowChecked = false
      }
    },
    generateReport() {
      if(_.isEmpty(this.dateFilter) || _.isEmpty(this.selectedIds)) {
        this.$bvToast.toast('Tanggal dan Survey harus di pilih', {
            title: `Warning`,
            variant: 'danger',
            toaster: 'b-toaster-top-center',
            solid: true,
        })
        return
      }
      window.open("/spa/survey-report-comprehensive" + '?formIds='+this.selectedIds+'&date='+this.dateFilter, '_blank');
    },
    clearDate() {
      this.dateFilter = ""
    },
    copyLink(item) {
      const link = "https://www.jejakimani.com/survey/"+item.slug
      navigator.clipboard.writeText(link)
    },
    deleteSurvey(item) {
      this.$swal({
        title: `Delete Survey ${item.title}?`,
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
@import "~@resources/scss/vue/libs/vue-flatpicker.scss";
</style>
