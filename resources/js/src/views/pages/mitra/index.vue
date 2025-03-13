<template>
  <div>
    <add-sidebar :is-add-sidebar-active.sync="isAddSidebarActive" :gender-options="genderOptions" :crew-role-options="crewRoleOptions" @refetch-data="refetchData" v-if="hasPermission('mitra-add-or-edit')" />

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
              <b-button variant="primary" @click="isAddSidebarActive = true" v-if="hasPermission('mitra-add-or-edit')">
                <span class="text-nowrap">Add Mitra</span>
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
            <template #aside>
              <b-avatar size="40" :src="data.item.profile_thumbnail" :text="avatarText(data.item.name)"
                :variant="`light-primary`"
                :to="{ name: 'mitra-detail', params: { id: data.item.id} }" />
            </template>
            <b-link :to="{ name: 'mitra-detail', params: { id: data.item.id } }"
              class="font-weight-bold d-block text-nowrap">
              {{ data.item.name }}
            </b-link>
            <small>Gender: {{ resolveGender(data.item.gender) }}</small><br>
            <small>{{ data.item.no_hp }}</small>
          </b-media>
        </template>

        <!-- Column: totalsalespax -->
        <template #cell(total_sales_pax)="data">
          <span class="text-nowrap">{{ data.item.total_sales_pax ? data.item.total_sales_pax.toLocaleString() : '-' }}</span>
        </template>

        <!-- Column: totalsaletransaction -->
        <template #cell(total_sales_transaction)="data">
          <span class="text-nowrap">Rp. {{ data.item.total_sales_transaction ? Number(data.item.total_sales_transaction).toLocaleString() : '-' }}</span>
        </template>

        <!-- Column: totalfee -->
        <template #cell(total_fee)="data">
          <span class="text-nowrap">Rp. {{ data.item.total_fee ? Number(data.item.total_fee).toLocaleString() : '-' }}</span>
        </template>

        <!-- Column: totalfee -->
        <template #cell(total_fee_paid)="data">
          <span class="text-nowrap">Rp. {{ data.item.total_fee_paid ? Number(data.item.total_fee_paid).toLocaleString() : '-' }}</span>
        </template>

        <!-- Column: Status -->
        <template #cell(status)="data">
          <b-badge pill :variant="`light-${resolveUserStatusVariant(data.item.status)}`" class="text-capitalize">
            {{ data.item.status }}
          </b-badge>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">

            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item :to="{ name: 'mitra-detail', params: { id: data.item.id, name: data.item.name } }">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Details</span>
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteTourMitra(data.item)">
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
import { avatarText } from '@core/utils/filter'
import useDataList from './useDataList'
import { deleteData, exportMitra } from '@/network/mitra'
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
    const isImportSidebarActive = ref(false)

    const genderOptions = [{ label: 'Man', value: 1 }, { label: 'Woman', value: 2 }]
    const crewRoleOptions = [{ label: 'Tour Leader', value: 1 }, { label: 'Mutawwif', value: 2 }, { label: 'Handling', value: 3 }, { label: 'Mitra', value: 4 }]

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
      resolveGender,
      resolveUserRoleIcon,
      resolveUserStatusVariant,

      // Extra Filters
      roleFilter,
      planFilter,
      statusFilter,
    } = useDataList()

    return {
      // Sidebar
      isAddSidebarActive,
      isImportSidebarActive,

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
      genderOptions,
      crewRoleOptions,

      // Filter
      avatarText,

      // UI
      resolveGender,
      resolveUserRoleIcon,
      resolveUserStatusVariant,

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
    exportMitra() {
      exportMitra({}).then(response => {
          window.location = response.data.downloadLink
      }).catch(error => {
          this.isSubmitModal = false
          this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
      })
    },
    deleteTourMitra(item){
        this.$swal({
        title: `Delete Mitra ${item.name}?`,
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
