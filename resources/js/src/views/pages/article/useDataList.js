import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/article'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refUserListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'select', sortable: false },
    { key: 'image_url', label: 'Image', sortable: false },
    { key: 'title', sortable: true },
    { key: 'category', sortable: true },
    { key: 'created_at', sortable: true },
    { key: 'created_by_name', label: 'Created By', sortable: true },
    { key: 'status', sortable: true },
    { key: 'seo_score', sortable: true },

    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalArticles = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const roleFilter = ref(null)
  const planFilter = ref(null)
  const statusFilter = ref(null)
  const categoryFilter = ref(null)
  const showInPageFilter = ref(null)

  const dataMeta = computed(() => {
    const localItemsCount = refUserListTable.value ? refUserListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalArticles.value,
    }
  })

  const refetchData = () => {
    refUserListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, roleFilter, planFilter, statusFilter, categoryFilter, showInPageFilter], () => {
    refetchData()
  })

  const fetchArticles = (ctx, callback) => {
    getList({
      params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        status: statusFilter.value,
        categoryId: categoryFilter.value,
        showInPage: showInPageFilter.value
      }
    })
      .then(response => {
        const { data, total } = response.data

        totalArticles.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching Article list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, { position: 'top-center' })
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolvePackageType = packageType => {
    if (packageType == '1') return 'Ruby'
    if (packageType == '2') return 'Emerald'
    if (packageType == '3') return 'Sapphire'
    return 'Ruby'
  }

  const resolveArticleStatusVariant = status => {
    if (status === 0) return 'info'
    if (status === 1) return 'primary'
    return 'primary'
  }

  const resolveArticleStatusName = status => {
    if (status === 0) return 'Draft'
    if (status === 1) return 'Publish'
    return 'Publish'
  }

  const resolveShowInRoomVariant = status => {
    if (status === 'article') return 'info'
    if (status === 'participant-room') return 'primary'
    return 'primary'
  }

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

    resolvePackageType,
    resolveArticleStatusVariant,
    resolveArticleStatusName,
    resolveShowInRoomVariant,
    refetchData,

    // Extra Filters
    roleFilter,
    planFilter,
    statusFilter,
    categoryFilter,
    showInPageFilter
  }
}
