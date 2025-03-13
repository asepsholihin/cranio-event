<template>
  <div>
    <add-sidebar :is-add-sidebar-active.sync="isAddSidebarActive" :copy-attendee-from-event-id.sync="addCopyEventId"
      @refetch-data="refetchData" v-if="hasPermission('event-attendance-add-or-edit')"/>

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
              <b-button variant="primary" @click="isAddSidebarActive = true; addCopyEventId = 0" v-if="hasPermission('event-attendance-add-or-edit')">
                <span class="text-nowrap">Add Event</span>
              </b-button>
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refUserListTable" class="position-relative" :items="fetchUsers" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc">

        <!-- Column: Name -->
        <template #cell(name)="data">
            <b-link :to="{ name: 'event-attendee-open-registration', params: { id: data.item.id } }"
              class="font-weight-bold d-block">
              {{ data.item.name }}
            </b-link>
        </template>

        <!-- Column: Total Check In -->
        <template #cell(total_check_in)="data">
            <div class="text-nowrap"><feather-icon icon="UserCheckIcon" size="18" class="mr-50" :class="`text-${resolveUserRoleVariant(data.item)}`" />
            <span class="align-text-top">{{ data.item.total_checkin }} of {{ data.item.total_attendance }}</span></div>
        </template>

         <!-- Column: Close Registration -->
        <template #cell(is_paid_event)="data">
            <div class="text-center">
              <span v-if="data.item.is_paid_event == 1" class="text-success">Yes</span>
              <span v-else class="text-danger">No</span>
            </div>
        </template>


        <!-- Column: Event Date -->
        <template #cell(event_date)="data">
          <span class="text-nowrap">{{formatDate(data.item.event_date)}}<br>{{data.item.event_at}}</span>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret>

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item :to="{ name: 'event-attendance-open-registration-detail', params: { id: data.item.id, name: data.item.name } }">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Details</span>
            </b-dropdown-item>
            <b-dropdown-item :to="{ name: 'event-attendance-open-registration-report', params: { id: data.item.id, name: data.item.name } }">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Attendance Report</span>
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteEvent(data.item)">
              <feather-icon icon="Trash2Icon" />
              <span class="align-middle ml-50">Delete Event</span>
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
import addSidebar from './addSidebar.vue'
import { hasPermission } from '@/auth/utils'
import { formatDate } from '@core/utils/filter'
import { deleteEventNetwork } from '@/network/event-open-registration'

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
    const addCopyEventId = ref(0)
    const resolveUserRoleVariant = (item) => {
        if (item.total_attendance == 0)
            return 'danger'
        if (item.total_checkin == item.total_attendance)
            return 'success'
        return 'default'
    }
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

      // UI
      resolveAccessStatus,
      resolvePermissions,
      resolveUserRoleIcon,
      resolveUserStatusVariant,

      // Extra Filters
      roleFilter,
      planFilter,
      statusFilter,
    } = useDataList()
    return {
      resolveUserRoleVariant,
      // Sidebar
      isAddSidebarActive,
      addCopyEventId,

      fetchUsers,
      formatDate,
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
      // Filter

      // UI
      resolveAccessStatus,
      resolvePermissions,
      resolveUserRoleIcon,
      resolveUserStatusVariant,

      // Extra Filters
      roleFilter,
      planFilter,
      statusFilter,

      hasPermission
    }
  },
  methods: {
    deleteEvent(item){
        this.$swal({
        title: `Delete Event ${item.name}?`,
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
                deleteEventNetwork(item.id).then(response => {
                    this.refetchData()
                })
            }
      })
    }
  },
  created() {

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
