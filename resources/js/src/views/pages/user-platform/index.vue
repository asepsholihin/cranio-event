<template>
  <div>
    <add-sidebar :is-add-sidebar-active.sync="isAddSidebarActive" :permission-list="permissionList" :department-options="departmentOptions"
      @refetch-data="refetchData" v-if="hasPermission('user-platform-add-or-edit')"/>

    <!-- Table Container Card -->
    <b-card no-body class="mb-0">

      <div class="m-2">

        <!-- Table Top -->
        <b-row class="mb-2">
          <b-col cols="12" md="4" class="mb-lg-0 mb-2">
            <label for="">Department</label>
            <v-select v-model="departmentFilter" :options="departmentOptions" placeholder="Please Select Department"
                :reduce="label => label.id" label="name" />
          </b-col>
          <b-col cols="12" md="4" class="mb-lg-0 mb-2">
            <label for="">Search</label>
            <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1" placeholder="Search..." />
          </b-col>
        </b-row>
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
               <b-button variant="primary" class="mr-2 mb-lg-0 mb-2" @click="showDepartment" v-if="hasPermission('user-platform-add-or-edit')">
                <span class="text-nowrap">Add Department</span>
               </b-button>
              <b-button variant="primary" @click="isAddSidebarActive = true" class="mb-lg-0 mb-2" v-if="hasPermission('user-platform-add-or-edit')">
                <span class="text-nowrap">Add User Platform</span>
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
          <b-media vertical-align="center">
            <template v-if="data.item.is_mitra == 1">
                <span class="badge badge-pill badge-info">Mitra</span>
            </template>
            <b-link :to="{ name: 'user-platform-detail', params: { id: data.item.id } }"
              class="font-weight-bold d-block text-nowrap">
              {{ data.item.name }}
            </b-link>
          </b-media>
        </template>

        <!-- Column: Status -->
        <template #cell(access_status)="data">
            {{ resolveAccessStatus(data.item.access_status) }}
        </template>

        <!-- Column: Department -->
        <template #cell(departments)="data">
            <b-link v-if="data.item.departments.length > 0" variant="primary" @click="viewDepartment(data.item)">View Department</b-link>
        </template>

        <!-- Column: Permission -->
        <template #cell(permission_list)="data">
          <b-link variant="primary" @click="viewPermission(data.item)">View Permission</b-link>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item :to="{ name: 'user-platform-detail', params: { id: data.item.id, name: data.item.name } }">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Details</span>
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

    <b-modal v-model="previewPermissionModal" no-close-on-backdrop ok-only>
        <template #modal-title>
            <h4>Permission {{ user.name }}</h4>
        </template>

        <p v-if="user.permission_list">{{ resolvePermissions(user.permission_list) }}</p>
    </b-modal>

    <b-modal v-model="previewDepartmentModal" no-close-on-backdrop ok-only>
        <template #modal-title>
            <h4>Departments - {{ user.name }}</h4>
        </template>
        <ul>
          <li v-for="(department, index) in user.departments" :key="index">{{ department.name }}</li>
        </ul>
    </b-modal>

    <!-- ADD DEPARTMENT -->
    <b-modal size="lg" v-model="showAddDepartment" ok-title="Add" @hidden="resetModal" no-close-on-backdrop>
        <template #modal-title>
            <h4>Department</h4>
        </template>
        <div class="table-departments">
            <b-table striped hover :fields="departementFields" :items="departementList" width="100%" show-empty>
                <template #cell(index)="data">
                    {{ data.index + 1 }}
                </template>
                <template #cell(is_active)="data">
                    <div class=""><b-form-checkbox v-model="data.item.is_active" name="check-button" switch @input="changeStatus(data.item.id, data.item.is_active)"/></div>
                </template>
            </b-table>
        </div>
        <template #modal-footer>
            <validation-observer ref="refObsForm" class="w-100">
                <b-form class="w-100" @submit.prevent="postData" @reset.prevent="resetData">
                    <div class="w-100">
                        <validation-provider #default="{ errors }" name="Department Name" vid="name" rules="required">
                            <b-form-group label="Add new department name">
                                <b-form-input placeholder="please type department name here" v-model="value.name" name="name" :state="errors.length > 0 ? false : null" trim />
                            </b-form-group>
                            <b-form-invalid-feedback>
                                {{ errors[0] }}
                            </b-form-invalid-feedback>
                        </validation-provider>
                    </div>
                    <div class="d-flex align-items-center justify-content-end">
                        <b-button variant="secondary" class="mr-2" @click="resetModal">
                            <span class="text-nowrap">Cancel</span>
                        </b-button>
                        <b-button variant="primary" type="submit" :disabled="isButtonLoading">
                            <span class="text-nowrap"><b-spinner small v-show="isButtonLoading" /> Add</span>
                        </b-button>
                    </div>
                </b-form>
            </validation-observer>
        </template>
    </b-modal>
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
  BModal,
  BLink,
  BForm,
  BBadge,
  BDropdown,
  BFormCheckbox,
  BDropdownItem,
  BFormGroup,
  BPagination,
  BAlert,
  BFormInvalidFeedback,
  BSpinner,
} from 'bootstrap-vue'
import vSelect from 'vue-select'
import { ref } from '@vue/composition-api'
import useDataList from './useDataList'
import addSidebar from './addSidebar.vue'
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import { hasPermission } from '@/auth/utils'
import { getListDepartment, addDepartment, changeStatusDepartment} from '@/network/user-platform'

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
    BModal,
    BSpinner,
    BDropdown,
    BFormGroup,
    BForm,
    BAlert,
    BFormInvalidFeedback,
    BFormCheckbox,
    BDropdownItem,
    BPagination,
    ValidationProvider,
    ValidationObserver,

    vSelect,
  },
  setup() {
    const isAddSidebarActive = ref(false)
    const departmentOptions = []
    const departementFields = [
        {
            key: 'index',
            label: 'No. ',
            sortable: false,
        },
        {
            key: 'name',
            label: 'Department',
            sortable: false,
        },
        {
            key: 'is_active',
            label: 'Active/Inactive',
            sortable: false,
        }
    ]
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
      permissionList,
      refetchData,

      // UI
      resolveAccessStatus,
      resolvePermissions,
      resolveUserRoleIcon,
      resolveUserStatusVariant,
      resolveDepartment,

      // Extra Filters
      departmentFilter,
    } = useDataList()

    return {
      // Sidebar
      isAddSidebarActive,

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
      permissionList,
      refetchData,
      // Filter

      // UI
      resolveAccessStatus,
      resolvePermissions,
      resolveUserRoleIcon,
      resolveUserStatusVariant,
      resolveDepartment,

      // Extra Filters
      departmentFilter,

      hasPermission,
      departmentOptions,

      // list Department
      departementFields
    }
  },
  methods : {
    viewDepartment(user) {
      this.previewDepartmentModal = true
      this.user = user
    },
    viewPermission(user) {
      this.previewPermissionModal = true
      this.user = user
    },
    showDepartment(){
        this.refactDataDepartment()
        this.showAddDepartment = true
    },
    resetModal(){
        this.showAddDepartment = false
        this.value = {}
        this.refactDataDepartment()
    },
    resetData(){
        this.value = {}
        this.$refs.refObsForm.reset()
    },
    changeStatus(id, status){
        const stat = {'id':id, 'is_active':status}
        changeStatusDepartment(stat)
        .then((response) => {
            console.log('great')
        })
        .catch((error) => {
            console.log(error.response.data)
        });
    },
    postData(){
        this.$refs.refObsForm.validate().then(success => {
            if (!success) return
            this.$swal({
                title: `Are you sure want to add new department ?`,
                text: "",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes!",
                customClass: {
                    confirmButton: "btn btn-danger",
                    cancelButton: "btn btn-outline-primary ml-1",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.value) {
                this.isButtonLoading = true
                addDepartment(this.value)
                .then((response) => {
                    this.$bvToast.toast("Deparments has been inserted", {
                        title: "Success",
                        letiant: "primary",
                        toaster: "b-toaster-top-center",
                        timeout: 1000,
                        solid: true,
                    });
                    response.data.is_active = true
                    this.refactDataDepartment()
                    this.value = {}
                    this.isButtonLoading = false
                    this.$refs.refObsForm.reset()
                })
                .catch((error) => {
                    if (error.response.data.errors) {
                        this.$refs.refObsForm.setErrors(error.response.data.errors);
                    } else {
                        this.$refs.refObsForm.setErrors(error.response.data);
                    }
                    this.isButtonLoading = false
                });
                }
            })
        })
    },
    refactDataDepartment(){
        getListDepartment().then(res => {
            this.departementList = res.data
            var departmentOptions = []
            this.departementList.forEach(element => {
              if(element.is_active == 1) departmentOptions.push(element)
            });
            this.departmentOptions = departmentOptions
        })
        .catch(error => {
            this.$bvToast.toast(`Error: ${error.response.data.message}`, { title: `Error`, variant: 'danger', toaster: 'b-toaster-top-center', solid: true })
        })
    }
  },
  data() {
    this.refactDataDepartment()
    return {
      user: {},
      previewPermissionModal: false,
      previewDepartmentModal: false,
      showAddDepartment: false,
      value:{},
      isButtonLoading: false,
      departementList:[]
    }
  }
}
</script>

<style lang="scss" scoped>
.per-page-selector {
  width: 90px;
}

.table-departments{
    max-height: 270px;
    overflow: auto;
}
</style>

<style lang="scss">
@import '~@resources/scss/vue/libs/vue-select.scss';
@import "~@resources/scss/vue/libs/vue-sweetalert.scss";
</style>
