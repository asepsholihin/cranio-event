<template>
  <div>
    <!-- Table Container Card -->
    <b-card no-body class="mb-0">
      <div class="m-2">
        <b-row>

          <!-- Filter -->
          <b-col cols="12" md="4" class="mb-1 mb-md-0">
            <label>Category</label>
            <v-select id="category_id" v-model="categoryFilter" :options="categoryOptions" :clearable="true"
              :reduce="(label) => label.id" label="name" />
          </b-col>
          <b-col cols="12" md="4" class="mb-1 mb-md-0">
            <label>Show in Page</label>
            <v-select id="show_in_page" v-model="showInPageFilter" :options="showInPageOptions" :clearable="true"
              :reduce="label => label.value" />
          </b-col>
        </b-row>
      </div>

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
              <b-button class="mr-1" size="sm" variant="primary" @click="selectRowAction">
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
              <b-dropdown class="mr-1" size="sm" :disabled="!isRowChecked" text="Action" variant="gradient-primary">
                <b-dropdown-item @click="setNoIndexes">
                  Set No Index
                </b-dropdown-item>
                <!-- <b-dropdown-item @click="setNoFollows">
                  Set No Follow
                </b-dropdown-item>
                <b-dropdown-item @click="setNoImageIndexes">
                  Set No Image Index
                </b-dropdown-item>
                <b-dropdown-item @click="setNoArchives">
                  Set No Archive
                </b-dropdown-item>
                <b-dropdown-item @click="setNoSnippets">
                  Set No Snippet
                </b-dropdown-item> -->
                <b-dropdown-divider />
                <b-dropdown-item variant="danger" @click="onDelete">Delete</b-dropdown-item>
              </b-dropdown>
              <b-form-input v-model="searchQuery" debounce="350" type="search" class="d-inline-block mr-1"
                placeholder="Search..." />
              <b-button variant="primary" :to="{ name: 'add-article' }">
                <span class="text-nowrap">Add Articles</span>
              </b-button>
            </div>
          </b-col>
        </b-row>

      </div>

      <b-table ref="refUserListTable" class="position-relative" :items="fetchArticles" responsive hover
        :fields="tableColumns" primary-key="id" :sort-by.sync="sortBy" show-empty empty-text="No matching records found"
        :sort-desc.sync="isSortDirDesc" @row-selected="onRowSelected" :tbody-tr-class="rowClass" select-mode="multi"
        selected-variant="primary" selectable>

        <!-- Column: Title -->
        <template #cell(title)="data">
          {{ data.item.title }}<br>
          <b-badge pill :variant="`light-${resolveShowInRoomVariant(data.item.show_in_page)}`" class="text-capitalize">
            {{ data.item.show_in_page }}
          </b-badge>
        </template>

        <!-- Column: Image Url -->
        <template #cell(image_url)="data">
          <img :src="data.item.image_url" height="40">
        </template>

        <!-- Column: Created at -->
        <template #cell(created_at)="data">
          <span class="text-nowrap">{{ formatDateTime(data.item.created_at) }}</span>
        </template>

        <!-- Column: Status -->
        <template #cell(status)="data">
          <b-badge pill :variant="`light-${resolveArticleStatusVariant(data.item.status)}`" class="text-capitalize">
            {{ resolveArticleStatusName(data.item.status) }}
          </b-badge>
        </template>

        <!-- Column: SEO Score -->
        <template #cell(seo_score)="data">
          <span class="text-nowrap">
            SEO Score: {{ data.item.seo_score }} <br>
            Internal Link: {{ data.item.count_internal_link }}<br>
            External Link: {{ data.item.count_external_link }}
          </span>
        </template>

        <!-- Column: Actions -->
        <template #cell(actions)="data">
          <b-dropdown variant="link" no-caret :right="$store.state.appConfig.isRTL">
            <template #button-content>
              <feather-icon icon="MoreVerticalIcon" size="16" class="align-middle text-body" />
            </template>
            <b-dropdown-item :to="{ name: 'article-detail', params: { id: data.item.id } }">
              <feather-icon icon="FileTextIcon" />
              <span class="align-middle ml-50">Details</span>
            </b-dropdown-item>
            <b-dropdown-item variant="danger" @click="deleteArticle(data.item)">
              <feather-icon icon="Trash2Icon" />
              <span class="align-middle ml-50">Delete</span>
            </b-dropdown-item>
          </b-dropdown>
        </template>

        <template #cell(select)="{ rowSelected }">
          <template v-if="rowSelected">
            <feather-icon icon="CheckSquareIcon" size="20" class="align-end" />
          </template>
          <template v-else>
            <feather-icon icon="SquareIcon" size="20" class="align-end" />
          </template>
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

            <b-pagination v-model="currentPage" :total-rows="totalArticles" :per-page="perPage" first-number last-number
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
import { avatarText, formatDateTime, formatDate } from '@core/utils/filter'
import useDataList from './useDataList'
import { deleteData, postAction } from '@/network/article'
import { hasPermission } from '@/auth/utils'
import { getCategorySearch } from '@/network/article-category'

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
      fetchArticles,
      tableColumns,
      perPage,
      currentPage,
      totalArticles,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,

      // UI
      resolvePackageType,
      resolveArticleStatusVariant,
      resolveShowInRoomVariant,
      resolveArticleStatusName,

      // Extra Filters
      roleFilter,
      planFilter,
      statusFilter,
      categoryFilter,
      showInPageFilter
    } = useDataList()

    return {
      fetchArticles,
      tableColumns,
      perPage,
      currentPage,
      totalArticles,
      dataMeta,
      perPageOptions,
      searchQuery,
      sortBy,
      isSortDirDesc,
      refUserListTable,
      refetchData,

      // Filter
      avatarText,
      formatDateTime,
      formatDate,

      // UI
      resolvePackageType,
      resolveArticleStatusVariant,
      resolveShowInRoomVariant,
      resolveArticleStatusName,

      // Extra Filters
      roleFilter,
      planFilter,
      statusFilter,
      categoryFilter,
      showInPageFilter,

      hasPermission
    }
  },
  data() {
    const categoryOptions = []
    const showInPageOptions = [
      { label: 'Artikel', value: 'article' },
      { label: 'Ruang Participant', value: 'participant-room' },
    ]
    getCategorySearch().then(response => {
      this.categoryOptions = response.data;
    }).catch(error => {
      if (error.response.data.errors) {
        this.$refs.refObsForm.setErrors(error.response.data.errors)
      } else {
        this.$refs.refObsForm.setErrors(error.response.data)
      }
    })
    

    return { categoryOptions, showInPageOptions, isRowChecked: false, selectedIds: [] }
  },
  created() {

  },
  methods: {
    selectRowAction() {
      if (this.isRowChecked)
        this.$refs.refUserListTable.clearSelected()
      else
        this.$refs.refUserListTable.selectAllRows()
    },
    rowClass(item, type) {
      if (!item || type !== 'row') return
      if (item.waiting_list == 1) return 'table-secondary'
    },
    onRowSelected(items) {
      const selectedIds = []
      items.forEach(function (item) {
        selectedIds.push(item.id)
      })
      this.selectedIds = selectedIds
      if (items.length > 0)
        this.isRowChecked = true
      else this.isRowChecked = false
    },
    setNoIndexes() {
      const vForm = {}
      vForm.ids = this.selectedIds
      vForm.noindex = true
      this.$swal({
        title: `Set No Index this articles?`,
        text: "It cannot be reverted",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        customClass: {
          confirmButton: 'btn btn-outline-primary mr-1',
          cancelButton: 'btn btn-success',
        },
        buttonsStyling: false,
      }).then(result => {
        if (result.value) {
          postAction(vForm).then(response => {
            this.$swal({ icon: 'success', title: 'Success', text: `Articles has been set to no index successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
            this.refetchData()
          }).catch(error => {
            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
          })
        }
      })
    },
    setNoFollows() {
      const vForm = {}
      vForm.ids = this.selectedIds
      vForm.nofollow = true
      this.$swal({
        title: `Set No Follow this articles?`,
        text: "It cannot be reverted",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        customClass: {
          confirmButton: 'btn btn-outline-primary mr-1',
          cancelButton: 'btn btn-success',
        },
        buttonsStyling: false,
      }).then(result => {
        if (result.value) {
          postAction(vForm).then(response => {
            this.$swal({ icon: 'success', title: 'Success', text: `Articles has been to no follow successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
            this.refetchData()
          }).catch(error => {
            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
          })
        }
      })
    },
    setNoImageIndexes() {
      const vForm = {}
      vForm.ids = this.selectedIds
      vForm.noimageindex = true
      this.$swal({
        title: `Set No Image Index this articles?`,
        text: "It cannot be reverted",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        customClass: {
          confirmButton: 'btn btn-outline-primary mr-1',
          cancelButton: 'btn btn-success',
        },
        buttonsStyling: false,
      }).then(result => {
        if (result.value) {
          postAction(vForm).then(response => {
            this.$swal({ icon: 'success', title: 'Success', text: `Articles has been set to no image index successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
            this.refetchData()
          }).catch(error => {
            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
          })
        }
      })
    },
    setNoArchives() {
      const vForm = {}
      vForm.ids = this.selectedIds
      vForm.noarchive = true
      this.$swal({
        title: `Set No Archive this articles?`,
        text: "It cannot be reverted",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        customClass: {
          confirmButton: 'btn btn-outline-primary mr-1',
          cancelButton: 'btn btn-success',
        },
        buttonsStyling: false,
      }).then(result => {
        if (result.value) {
          postAction(vForm).then(response => {
            this.$swal({ icon: 'success', title: 'Success', text: `Articles has been set to no archive successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
            this.refetchData()
          }).catch(error => {
            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
          })
        }
      })
    },
    setNoSnippets() {
      const vForm = {}
      vForm.ids = this.selectedIds
      vForm.nosnippet = true
      this.$swal({
        title: `Set No Snippet this articles?`,
        text: "It cannot be reverted",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        customClass: {
          confirmButton: 'btn btn-outline-primary mr-1',
          cancelButton: 'btn btn-success',
        },
        buttonsStyling: false,
      }).then(result => {
        if (result.value) {
          postAction(vForm).then(response => {
            this.$swal({ icon: 'success', title: 'Success', text: `Articles has been set to no snippet successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
            this.refetchData()
          }).catch(error => {
            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
          })
        }
      })
    },
    onDelete() {
      const vForm = {}
      vForm.ids = this.selectedIds
      vForm.delete = true
      this.$swal({
        title: `Delete ${this.selectedNames}?`,
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
          postAction(vForm).then(response => {
            this.$swal({ icon: 'success', title: 'Success', text: `Articles has been deleted successfully`, timer: 2500, customClass: { confirmButton: 'btn btn-primary', }, buttonsStyling: false })
            this.refetchData()
          }).catch(error => {
            this.$swal({ icon: 'error', title: 'Error', text: `${error.response.data.message}`, customClass: { confirmButton: 'btn btn-warning', }, buttonsStyling: false })
          })
        }
      })
    },
    deleteArticle(item) {
      this.$swal({
        title: `Delete Article ${this.resolvePackageType(item.package_type)}?`,
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
