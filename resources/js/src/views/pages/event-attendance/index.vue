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
              <div class="input-group mr-1">
                  <flat-pickr v-model="monthFilter" :config="monthSelect" class="form-control" name="date"/>
                  <div class="input-group-append">
                      <button class="btn btn-danger btn-sm" type="button" @click="clearMonth">
                          <feather-icon icon="XIcon" />
                      </button>
                  </div>
              </div>
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
            <b-link :to="{ name: 'event-attendee', params: { id: data.item.id } }"
              class="font-weight-bold d-block text-nowrap">
              {{ data.item.name }}
            </b-link>
        </template>

        <!-- Column: Total Check In -->
        <template #cell(total_check_in)="data">
            <div class="text-nowrap">
              <feather-icon @click="detailAttendance(data.item.id)" icon="UserCheckIcon" size="18" class="mr-50" :class="`text-info`" />
              <span class="align-text-top">{{ data.item.total_checkin }} of {{ data.item.total_attendance }}</span>
              <span class="align-text-top font-weight-bold" v-if="data.item.total_checkin > 0 && data.item.total_attendance > 0">| {{ ((parseInt(data.item.total_checkin) / parseInt(data.item.total_attendance)) * 100).toFixed(0) || 0 }}%</span>
          </div>
        </template>


        <!-- Column: Event Date -->
        <template #cell(event_date)="data">
            {{formatDate(data.item.event_date)}}
        </template>

        <!-- Column: Report -->
        <template #cell(report)="data">
            <b-button class="mr-1" variant="warning" size="sm" :to="{ name: 'manasik-report', params: { id: data.item.id, online: 'offline' } }"><feather-icon icon="PieChartIcon" /><span class="align-middle ml-50">Offline</span></b-button>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret>

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item variant="success" :to="{ name: 'event-attendance-confirmation', params: { id: data.item.id, name: data.item.name } }">
              <feather-icon icon="CheckIcon" />
              <span class="align-middle ml-50">Attendance Confirmation Report</span>
            </b-dropdown-item>
            <b-dropdown-item variant="warning" @click="isAddSidebarActive = true; addCopyEventId = data.item.id" v-if="hasPermission('event-attendance-add-or-edit')">
              <feather-icon icon="CopyIcon" />
              <span class="align-middle ml-50">Copy Attendee Into New Event</span>
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteEvent(data.item)" v-if="hasPermission('event-attendance-add-or-edit')">
              <feather-icon icon="Trash2Icon" />
              <span class="align-middle ml-50">Delete Event</span>
            </b-dropdown-item>
          </b-dropdown>
          <b-button variant="info" size="sm" :to="{ name: 'event-attendance-detail', params: { id: data.item.id, name: data.item.name } }">
            <feather-icon icon="SearchIcon" />
            <span class="align-middle ml-50">Details</span>
          </b-button>
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

      <b-modal v-model="participantModalShow" size="lg" ok-title="Close" :title="`Attendance Participant`" centered ok-only>
        <b-row>
          <b-col cols="6">
            <h4>Sudah Absen</h4>
            <ul class="list-unstyled">
              <li v-for="(row, i) in participantAttendeeList" :key="i">{{ i+1}}. {{ row.name }}</li>
            </ul>
          </b-col>
          <b-col cols="6">
            <h4>Belum Absen</h4>
            <ul class="list-unstyled">
              <li v-for="(row, i) in participantUnAttendeeList" :key="i">{{ i+1}}. {{ row.name }}</li>
            </ul>
          </b-col>
        </b-row>
      </b-modal>
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
import flatPickr from 'vue-flatpickr-component'
import { ref } from '@vue/composition-api'
import useDataList from './useDataList'
import addSidebar from './addSidebar.vue'
import { hasPermission } from '@/auth/utils'
import { formatDate } from '@core/utils/filter'
import { deleteEventNetwork, getListAttendance, generateEventLink } from '@/network/event-attendance'
import monthSelectPlugin from "flatpickr/dist/plugins/monthSelect"

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
    flatPickr
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
      // Extra Filters
      monthFilter
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
      // Extra Filters
      monthFilter,
      hasPermission
    }
  },
  data() {
    return {
      participantAttendeeList: [],
      participantUnAttendeeList: [],
      participantModalShow: false,
      monthSelect: {
        plugins: [
          new monthSelectPlugin({
            shorthand: true, //defaults to false
            dateFormat: "Y-m", //defaults to "F Y"
            altFormat: "F Y", //defaults to "F Y"
          })
        ]
      },
    }
  },
  methods: {
    clearMonth() {
      this.monthFilter = null
    },
    detailAttendance(eventId) {
      this.participantModalShow = true;
      getListAttendance(eventId, {})
      .then((res) => {
          var data = res.data
          data.forEach(element => {
            if(element.check_in_at) {
              this.participantAttendeeList.push(element)
            } else {
              this.participantUnAttendeeList.push(element);
            }
          });
      })
      .catch((error) => {
          this.$bvToast.toast(`Error: ${error.response.data.message}`, {
              title: `Error`,
              variant: "danger",
              toaster: "b-toaster-top-center",
              solid: true,
          });
      });
    },
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
    },
    generateEventLink(id) {
      const form = {}
      form.id = id
      generateEventLink(form).then(response => {
        this.$swal({ icon: 'success', title: 'Success', text: `Event Link generated, link copied`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
        navigator.clipboard.writeText(response.data)
      }).catch(error => {
        this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
        this.isSubmitModal = false
      })
    },
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
@import '~@resources/scss/vue/libs/vue-flatpicker.scss';
</style>
