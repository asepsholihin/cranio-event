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
            <div class="d-flex align-items-center justify-content-end">
              <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1" placeholder="Search..." />
              <b-button variant="primary" :to="{ name: 'hotel-add' }" v-if="hasPermission('land-arrangement-add-or-edit')">
                <span class="text-nowrap">Add Hotel</span>
              </b-button>
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refHotelListTable" class="position-relative" :items="fetchHotels" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc">

        <!-- Column: Name -->
        <template #cell(name)="data">
          <b-link :to="{ name: 'hotel-detail', params: { id: data.item.id } }"
            class="font-weight-bold d-block text-nowrap">
            {{ data.item.name }}
          </b-link>
        </template>

        <!-- Column: Star -->
        <template #cell(star)="data">
          <feather-icon class="text-primary" v-for="index in parseInt(data.item.star)" :key="index" icon="StarIcon" size="18" />
        </template>

        <!-- Column: updated_at_rate -->
        <template #cell(last_updated_rate_at)="data">
          {{ formatDate(data.item.last_updated_rate_at) }}
        </template>

        <!-- Column: PIC -->
        <template #cell(pic)="data">
          {{ data.item.pic_name }}<br>
          {{ data.item.pic_phone }}
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="primary" size="sm" text="Options" :right="$store.state.appConfig.isRTL">
            <b-dropdown-item @click="viewHotelGraphic(data.item)">
              <feather-icon icon="SearchIcon" />
              <span class="align-middle ml-50">View Infographic</span>
            </b-dropdown-item>
            <b-dropdown-item :to="{ name: 'hotel-detail', params: { id: data.item.id, name: data.item.name } }">
              <feather-icon icon="EditIcon" />
              <span class="align-middle ml-50">Update Infographic</span>
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteHotel(data.item)">
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

            <b-pagination v-model="currentPage" :total-rows="totalHotels" :per-page="perPage" first-number last-number
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
  BOverlay,
  BCard,
  BRow,
  BCol,
  BFormInput,
  BButton,
  BTable,
  BLink,
  BBadge,
  BDropdown,
  BDropdownItem,
  BPagination,
} from 'bootstrap-vue'
import useDataList from './useDataList'
import { deleteData, getHotelGraphicUrl } from '@/network/hotel'
import { hasPermission } from '@/auth/utils'
import vSelect from 'vue-select'
import { formatDateToMonth, formatDate } from '@core/utils/filter'

export default {
  components: {
    BOverlay,
    BCard,
    BRow,
    BCol,
    BFormInput,
    BButton,
    BTable,
    BLink,
    BBadge,
    BDropdown,
    BDropdownItem,
    BPagination,
    vSelect,
  },
  setup() {
    const {
      fetchHotels,
      tableColumns,
      perPage,
      currentPage,
      totalHotels,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refHotelListTable,
      refetchData,

      // Extra Filters
      statusFilter,
    } = useDataList()

    return {
      fetchHotels,
      tableColumns,
      perPage,
      currentPage,
      totalHotels,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refHotelListTable,
      refetchData,

      // Extra Filters
      statusFilter,

      hasPermission,
      formatDateToMonth,
      formatDate
    }
  },
  methods : {
    viewHotelGraphic(item) {
        window.open(getHotelGraphicUrl(item.id), '_blank');
    },
    deleteHotel(item){
        this.$swal({
        title: `Delete Hotel ${item.name}?`,
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
                .catch(error => {
                    if (error.response.data.errors) {
                        this.$bvToast.toast(`Error: ${error.response.data.errors}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                    } else {
                        this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
                    }
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
