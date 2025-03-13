<template>
  <div>
    <!-- Table Container Card -->
    <b-card no-body class="mb-0">
      <div class="m-2">

        <!-- Table Top -->
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
              <b-button variant="primary" :to="{ name: 'add-form-section' }">
                <span class="text-nowrap">Add Form Section</span>
              </b-button>
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refUserListTable" class="position-relative" :items="fetchFormSections" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc">

        <!-- Column: Title -->
        <template #cell(name)="data">
          <b-link :to="{ name: 'form-section-detail', params: { id: data.item.id } }">{{ data.item.name }}</b-link>
        </template>
        
        <!-- Column: Created at -->
        <template #cell(created_at)="data">
          <span class="text-nowrap">{{ formatDateTime(data.item.created_at) }}</span>
        </template>

        <!-- Column: Updated at -->
        <template #cell(updated_at)="data">
          <span class="text-nowrap">{{ formatDateTime(data.item.updated_at) }}</span>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">
            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item :to="{ name: 'form-section-detail', params: { id: data.item.id } }">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Details</span>
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteFormSection(data.item)">
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

            <b-pagination v-model="currentPage" :total-rows="totalFormSections" :per-page="perPage" first-number last-number
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
import { deleteData } from '@/network/form-section'
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
  },
  setup() {

    const {
      fetchFormSections,
      tableColumns,
      perPage,
      currentPage,
      totalFormSections,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,

      // Extra Filters
    } = useDataList()

    return {
      fetchFormSections,
      tableColumns,
      perPage,
      currentPage,
      totalFormSections,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,

      // Filter
      formatDateTime,
      formatDate,

      // Extra Filters

      hasPermission
    }
  },
  methods: {
    deleteFormSection(item) {
      this.$swal({
        title: `Delete Form Section ${item.title}?`,
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
