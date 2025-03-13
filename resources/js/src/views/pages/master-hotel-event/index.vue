<template>
  <div>
    <!-- Table Container Card -->
    <b-card no-body class="mb-0">

      <div class="m-2">

        <b-row class="mb-2">
            <b-col cols="8">
                <div class="d-flex">
                    <div class="w-50">
                        <label for="">Filter By Purposes</label>
                        <v-select v-model="purpusesFilter" :options="purposesList"
                        :clearable="true" :reduce="label => label.id" label="name" placeholder="Filter by purposes" />
                    </div>
                    <div class="w-50 ml-1">
                        <label for="">Filter By Preference</label>
                        <v-select v-model="preferenceFilter" :options="preferenceList"
                        :clearable="true" :reduce="label => label.id" label="name" placeholder="Filter by preference" />
                    </div>
                </div>
            </b-col>
            <b-col cols="4">
                <div class="d-flex justify-content-end">
                    <b-button variant="primary" @click="create" v-if="hasPermission('master-hotel-event-add-or-edit')">
                        <span class="text-nowrap">Add New Hotel</span>
                    </b-button>
                </div>
            </b-col>
        </b-row>
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
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refUserListTable" class="position-relative" :items="fetchPackages" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" :tbody-tr-class="rowClass" :tbody-td-class="alignTop" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc">

        <!-- No -->
        <template #cell(index)="data">
            {{ data.index + 1 }}
        </template>

        <!-- Hotel Name -->
         <template #cell(hotel_name)="data">
            <div class="w-250">
                <h5 class="font-800">{{ data.item.hotel_name }}</h5>
                <h5>{{ data.item.hotel_name_city }}</h5>
                <h5 class="text-info" v-if="data.item.updated_name">Last Updated: {{ (data.item.newest_update) ? formatDate(data.item.newest_update) : ' - ' }}</h5>
                <h5 class="text-info" v-if="data.item.updated_name">By {{ data.item.updated_name }}</h5>
            </div>
         </template>


        <!-- Contact Information -->
        <template #cell(contact_information)="data">
            <div class="w-250">
                <h5 class="font-800">{{ data.item.hotel_pic }}</h5>
                <h5 class="text-info">{{ data.item.hotel_pic_number }}</h5>
                <h5><a :href="data.item.hotel_map_url" target="_blank" class="text-primary text-decoration-underline">view google maps</a></h5>
            </div>
         </template>


        <!-- Purposes -->
        <template #cell(purposes)="data">
            <div class="w-250">
                <ul class="p-0 pl-1 pr-1">
                    <li v-if="data.item.is_manasik=='yes'">Manasik</li>
                    <li v-if="data.item.is_transit=='yes'">Transit</li>
                </ul>
                <h5><a @click="showedRate(data.item)" v-if="data.item.is_transit=='yes'" class="text-primary text-decoration-underline">view room rate</a></h5>
            </div>
         </template>


        <!-- Purposes -->
        <template #cell(half_day)="data">
            <div class="w-250" v-if="data.item.is_manasik=='yes'">
                <h5>Rp. {{ (data.item.manasik_hd_price) ? parseInt(data.item.manasik_hd_price).toLocaleString() : " - " }}</h5>
                <ul class="p-0 pl-2 pr-1">
                    <li v-for="item in data.item.manasik_hd">{{ item.custom_text }}</li>
                </ul>
            </div>
            <div class="w-250" v-else><h5> - </h5></div>
         </template>


        <template #cell(full_day)="data">
            <div class="w-250" v-if="data.item.is_manasik=='yes'">
                <h5>Rp. {{ (data.item.manasik_fd_price) ? parseInt(data.item.manasik_fd_price).toLocaleString() : " - " }}</h5>
                <ul class="p-0 pl-2 pr-1">
                    <li v-for="item in data.item.manasik_fd">{{ item.custom_text }}</li>
                </ul>
            </div>
            <div class="w-250" v-else><h5> - </h5></div>
         </template>

         <template #cell(advantages)="data">
            <div class="w-250">
                <ul class="p-0 pl-1 pr-1">
                    <li v-for="item in data.item.advantages">{{ item.custom_text }}</li>
                </ul>
            </div>
         </template>

         <template #cell(disadvantages)="data">
            <div class="w-250">
                <ul class="p-0 pl-1 pr-1">
                    <li v-for="item in data.item.disadvantages">{{ item.custom_text }}</li>
                </ul>
            </div>
         </template>

         <template #cell(counter)="data">
            <div class="w-250">
                <h5><a v-if="data.item.is_manasik=='yes'" @click="showCounter(data.item, 'Manasik')" class="text-info text-decoration-underline">{{ (data.item.manasik_counter) ? data.item.manasik_counter : 0 }}x for Manasik</a></h5>
                <h5><a v-if="data.item.is_transit=='yes'" @click="showCounter(data.item, 'Transit')" class="text-primary text-decoration-underline">{{ (data.item.transit_counter) ? data.item.transit_counter : 0 }}x for Transit</a></h5>
            </div>
         </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item :to="{ name: 'master-hotel-event-detail', params: { id: data.item.id } }">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Update Info</span>
            </b-dropdown-item>
            <!-- FOR MANASIK -->
            <b-dropdown-item v-if="data.item.is_manasik == 'yes' && data.item.prefer_for_manasik == 'no'" @click="preferData(data.item, 'prefer_manasik')">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Prefer for Manasik</span>
            </b-dropdown-item>
            <b-dropdown-item v-if="data.item.is_manasik == 'yes' && data.item.prefer_for_manasik == 'yes'" @click="preferData(data.item, 'remove_manasik')">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Remove Preference for Manasik</span>
            </b-dropdown-item>
            <!-- FOR TRANSIT -->
            <b-dropdown-item v-if="data.item.is_transit == 'yes' && data.item.prefer_for_transit == 'no'" @click="preferData(data.item, 'prefer_transit')">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Prefer for Transit</span>
            </b-dropdown-item>
            <b-dropdown-item v-if="data.item.is_transit == 'yes' && data.item.prefer_for_transit == 'yes'" @click="preferData(data.item, 'remove_transit')">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Remove Preference for Transit</span>
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteHotel(data.item)" v-if="hasPermission('master-hotel-event-delete')">
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

            <b-pagination v-model="currentPage" :total-rows="totalPackages" :per-page="perPage" first-number last-number
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

        <!-- VIEW RATE -->
        <b-modal size="md" v-model="showAddRate" class="modalls" centered hide-footer @hidden="resetModal" no-close-on-backdrop>
            <template #modal-title>
                <h4>View Room Rate</h4>
            </template>
            <div class="table-departments">
                <table class="w-100">
                    <tr>
                        <td style="width: 35%;"><h5>Hotel Name</h5></td>
                        <td style="width: 5%;"><h5> : </h5></td>
                        <td style="width: 60%;"><h5>{{ formData.hotel_name }}</h5></td>
                    </tr>
                </table>
                <table class="w-100">
                    <tr>
                        <td style="width: 35%;"><h5>Last Updated</h5></td>
                        <td style="width: 5%;"><h5> : </h5></td>
                        <td style="width: 60%;"><h5>{{ (formData.newest_update) ? formatDate(formData.newest_update) : ' - ' }}</h5></td>
                    </tr>
                </table>
                <table class="w-100">
                    <tr>
                        <td style="width: 35%;"><h5>Updated Name</h5></td>
                        <td style="width: 5%;"><h5> : </h5></td>
                        <td style="width: 60%;"><h5>{{ (formData.updated_name) ? formData.updated_name : ' - ' }}</h5></td>
                    </tr>
                </table>
              <hr>
              <div class="row">
                <div class="col-12 mb-1" v-for="(item, key) in formData.hotel_rate">
                    <div class="row">
                        <div class="col-12">
                            <h5 class="font-800">{{ item.category_name }}</h5>
                            <div class="list-item" v-for="child in formData.hotel_rate[key].child">
                                <table class="w-100">
                                    <tr>
                                        <td style="width: 65%;"><h5>{{ child.item_name }}</h5></td>
                                        <td style="width: 5%;"><h5> : </h5></td>
                                        <td style="width: 30%;"><h5>Rp. {{ (child.item_price) ? (parseInt(child.item_price).toLocaleString()) : 0 }}</h5></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
              <div class="d-flex justify-content-end">
                    <b-button @click="resetModal" type="button"
                        variant="secondary">
                        Close
                    </b-button>
              </div>
            </div>
        </b-modal>

        <!-- VIEW HOTEL COUNTER -->
        <b-modal size="lg" v-model="showCounterRate" class="modalls" centered hide-footer @hidden="resetModal" no-close-on-backdrop>
            <template #modal-title>
                <h4>Hotel Counter History  - {{ counterTitle }}</h4>
            </template>
            <div class="dbv">
                <table class="w-100">
                    <tr>
                        <td style="width: 35%;"><h5>Hotel Name</h5></td>
                        <td style="width: 5%;"><h5> : </h5></td>
                        <td style="width: 60%;"><h5>{{ formCounter.hotel_name }}</h5></td>
                    </tr>
                </table>
                <table class="w-100">
                    <tr>
                        <td style="width: 35%;"><h5>Last History</h5></td>
                        <td style="width: 5%;"><h5> : </h5></td>
                        <td style="width: 60%;"><h5>{{ (formCounter.last_history) ? formatDate(formCounter.last_history) : ' - ' }}</h5></td>
                    </tr>
                </table>
                <table class="w-100">
                    <tr>
                        <td style="width: 35%;"><h5>Total Counter</h5></td>
                        <td style="width: 5%;"><h5> : </h5></td>
                        <td style="width: 60%;" v-if="counterTitle=='Manasik'"><h5>{{ (formCounter.manasik_counter) ? formCounter.manasik_counter : 0 }} Times</h5></td>
                        <td style="width: 60%;" v-else><h5>{{ (formCounter.transit_counter) ? formCounter.transit_counter : 0 }} Times</h5></td>
                    </tr>
                </table>
                <hr>
                <div class="table-resize">
                    <table class="table">
                        <thead class="thead-secondary">
                            <th>#</th>
                            <th>Departure</th>
                            <th>Counter Date</th>
                        </thead>
                        <tbody v-if="counterTitle=='Manasik'">
                            <tr v-for="(item, key) in formCounter.counter_manasik_list">
                                <td>{{ key+1 }}</td>
                                <td><a class="text-primary" @click="goToEvent(item.event_attendance_id)">{{ item.departure_name }}</a></td>
                                <td>{{ formatDate(item.created_at) }}</td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr v-for="(item, key) in formCounter.counter_transit_list">
                                <td>{{ key+1 }}</td>
                                <td><a class="text-primary" @click="goToEvent(item.event_attendance_id)">{{ item.departure_name }}</a></td>
                                <td>{{ formatDate(item.created_at) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end">
                    <b-button @click="resetModal" type="button"
                        variant="secondary">
                        Close
                    </b-button>
                </div>
            </div>
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
import { ref } from '@vue/composition-api'
import useDataList from './useDataList'
import { prefer, deleteData, getCounterItem } from '@/network/master-hotel-event'
import { formatDateTime, formatDate } from '@core/utils/filter'
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
    BPagination,

    vSelect,
  },
  setup() {

    const packageTypeOptions = [
      { label: 'Ruby', value: 1 },
      { label: 'Emerald', value: 2 },
      { label: 'Sapphire', value: 3 },
      { label: 'Haji Khusus', value: 4 },
      { label: 'Haji Furoda', value: 5 }
    ]

    const genderOptions = [
      { label: 'Man', value: 1 },
      { label: 'Woman', value: 2 },
      { label: 'Both', value: 3 }
    ]

    const {
      fetchPackages,
      tableColumns,
      perPage,
      currentPage,
      totalPackages,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,

      // UI
      resolveGender,
      resolvePackageType,
      resolvePackageStatusVariant,
      resolvePackageStatusName,

      // Extra Filters
      purpusesFilter,
      preferenceFilter,
      statusFilter,
    } = useDataList()

    return {
      // Sidebar
      fetchPackages,
      tableColumns,
      perPage,
      currentPage,
      totalPackages,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,
      packageTypeOptions,
      genderOptions,

      // Filter
      purpusesFilter,
      preferenceFilter,

      // UI
      resolveGender,
      resolvePackageType,
      resolvePackageStatusVariant,
      resolvePackageStatusName,

      // Extra Filters
      statusFilter,

      hasPermission,
      formatDateTime,
      formatDate
    }
  },
  created() {

  },
  data() {
    return {
        purposesList:[
            {id: 1, name:'View All', label: 'View All'},
            {id: 2, name:'Manasik', label: 'Manasik'},
            {id: 3, name:'Transit', label: 'Transit'},
            {id: 4, name:'Both', label: 'Both'}
        ],
        preferenceList:[
            {id: 1, name:'Manasik', label: 'Manasik'},
            {id: 2, name:'Transit', label: 'Transit'},
            {id: 3, name:'Both', label: 'Both & Transit'},
            {id: 4, name:'No', label: 'No Preference'},
            {id: 5, name:'View All', label: 'View All'},
        ],
        showAddRate:false,
        formData:{},
        counterTitle: '',
        showCounterRate:false,
        formCounter:{},
        pages:0
    }
  },
  methods : {
    showCounter(item, name){
        this.counterTitle = name
        this.showCounterRate = true
        this.formCounter = item
    },
    resetModal(){
      this.showCounterRate = false
      this.showAddRate = false
      this.formData = {}
      this.formCounter = {}
    },
    showedRate(item){
      this.showAddRate = true
      this.formData = item
    },
    goToEvent(item){
        this.$router.push({name: 'event-attendance-detail', params:{ id:item }})
    },
    preferData(item, type){
        const vForm = new FormData()
        vForm.append('type', type);
        prefer(item.id, vForm).then(response => {
            this.refetchData()
        })
        .catch(error => {
            if (error.response.data.errors) {
                this.$refs.refObsForm.setErrors(error.response.data.errors)
            } else {
                this.$refs.refObsForm.setErrors(error.response.data)
            }
        })
    },
    alignTop(item){
        return 'align-top';
    },
    rowClass(item) {
        if(item!=null){
            if(item.prefer_for_manasik == 'yes' && item.prefer_for_transit == 'yes'){
                return 'table-primary';
            }else if(item.prefer_for_manasik == 'yes'){
                return 'table-info';
            }else if(item.prefer_for_transit == 'yes'){
                return 'table-success';
            }else{
                return '';
            }
        }
    },
    create(){
        this.$router.push({name:'master-hotel-event-create'});
    },
    deleteHotel(item){
        this.$swal({
        title: `Delete Master Hotel ${item.hotel_name}?`,
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
                }).catch(error => {
                    this.$bvToast.toast(`Error: ${error.response.data.message}`, {
                        title: `Error`,
                        variant: "danger",
                        toaster: "b-toaster-top-center",
                        solid: true,
                    });
                });
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
.w-250{
    display: grid;
    align-items: start;
    width: 250px;
}
.w-350{
    width: 350px;
}
.text-decoration-underline{
    text-decoration: underline !important;
}
.font-800{
    font-weight: 800;
}
.list-item{
    padding-left: 15px;
}
.list-item::before {
    content: "•"; /* Bullet point */
    position: absolute;
    left: 15px;
    margin-top: -19px;
    color: black; /* Bullet color */
    font-size: 35px; /* Bullet size */
}
.table-resize{
    max-height: 450px;
    overflow: auto;
}
</style>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>
<style scoped>
/* .modalls .modal-header {
  background-color: #8c0095 !important;
  color:white !important;
} */
</style>
