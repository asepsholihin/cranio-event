<template>
  <div>
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
            <div class="d-lg-flex align-items-center justify-content-end">
              <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1 mb-lg-0 mb-2" placeholder="Search..." />
              <div class="input-group">
                <flat-pickr v-model="dateFilter" class="form-control" :config="{ altInput: true }" placeholder="Select date" />
                <div class="input-group-append">
                  <button class="btn btn-danger" type="button" title="Clear" @click="clearDate">
                    <feather-icon icon="Trash2Icon" />
                  </button>
                </div>
              </div>
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refUserListTable" class="position-relative" :items="fetchMediaMarketing" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc">

        <!-- Column: Title -->
        <template #cell(title)="data">
          <b-link :to="{ name: 'mitra-media-detail', params: { id: data.item.id } }"
            class="font-weight-bold d-block text-nowrap">
            {{ data.item.title }}
          </b-link>
        </template>

        <!-- Column: Category -->
        <template #cell(category)="data">
          {{ data.item.category }}
        </template>

        <!-- Column: Created At -->
        <template #cell(created_at)="data">
          {{ formatDate(data.item.created_at) }}
        </template>

        <!-- Column: Updated At -->
        <template #cell(updated_at)="data">
          {{ formatDate(data.item.updated_at) }}
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-spinner small v-show="isDownloading" />
          <b-dropdown :disabled="isDownloading" variant="link" no-caret :right="$store.state.appConfig.isRTL">
            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item v-if="data.item.type == 'flyer'" @click="viewFlyer(data.item)">
              <span class="align-middle ml-50">Preview</span>
            </b-dropdown-item>
            <b-dropdown-item @click="downloadFlyer(data.item)">
              <span class="align-middle ml-50">Download</span>
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

            <b-pagination v-model="currentPage" :total-rows="totalMediaMarketings" :per-page="perPage" first-number last-number
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

    <b-modal v-model="previewFlyerModal" size="lg" no-close-on-backdrop ok-only>
        <template #modal-title>
            <h4>Preview Flyer</h4>
        </template>

        <object :data="previewFlyerUrl" width="100%" height="650">
          Loading...
        </object>

        <template #modal-footer>

        </template>
    </b-modal>
  </div>
</template>

<script>
import {
  BSpinner,
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
import { ref } from '@vue/composition-api'
import { avatarText } from '@core/utils/filter'
import useMediaList from './useMediaList'
import { hasPermission } from '@/auth/utils'
import vSelect from 'vue-select'
import flatPickr from 'vue-flatpickr-component'
import { getPreviewFlyerPDF, getDownloadFlyerPDF } from '@/network/media-marketing'
import { formatDate } from '@core/utils/filter'

export default {
  components: {
    BSpinner,
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
    flatPickr
  },
  setup() {

    const {
      fetchMediaMarketing,
      tableColumns,
      perPage,
      currentPage,
      totalMediaMarketings,
      dataMeta,
      perPageOptions,
      searchQuery,
      dateFilter,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,
    } = useMediaList()

    return {
      fetchMediaMarketing,
      tableColumns,
      perPage,
      currentPage,
      totalMediaMarketings,
      dataMeta,
      perPageOptions,
      searchQuery,
      dateFilter,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,
      avatarText,
      hasPermission,
      formatDate
    }
  },
  created() {

  },
  data() {
    return {
      isDownloading: false,
      isButtonLoading: false,
      previewFlyerModal: false,
      previewFlyerUrl: ""
    }
  },
  methods : {
    viewFlyer(item) {
      this.previewFlyerModal = true
      this.previewFlyerUrl = getPreviewFlyerPDF(item.id)
    },
    downloadFlyer(item) {
      this.isDownloading = true
      getDownloadFlyerPDF(item.id).then(response => {
        const fileURL = window.URL.createObjectURL(new Blob([response.data]))
        const fileLink = document.createElement('a')
        const contentDisposition = response.headers['content-disposition']
        fileLink.href = fileURL;
        let fileName = 'unknown';
        if (contentDisposition) {
            const fileNameMatch = contentDisposition.match(/filename="(.+)"/);
            if (fileNameMatch.length === 2)
                fileName = fileNameMatch[1];
        }
        fileLink.setAttribute('download', fileName);
        document.body.appendChild(fileLink);
        fileLink.click();
        this.isDownloading = false
      }).catch(error => {
          this.$bvToast.toast(`${error}`, {
            title: `Error`,
            variant: 'danger',
            toaster: 'b-toaster-top-center',
            solid: true,
          })
          this.isDownloading = false
      })
    },
    clearDate() {
      this.dateFilter = null
    },
  },
}
</script>

<style lang="scss" scoped>
.per-page-selector {
  width: 90px;
}
</style>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
