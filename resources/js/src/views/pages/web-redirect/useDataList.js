import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/web-redirect'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refRedirectListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'redirect_type', sortable: true },
    { key: 'old_url', sortable: true },
    { key: 'new_url', sortable: true },
    { key: 'status', sortable: false },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalItemCategorys = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const statusFilter = ref(null)

  const dataMeta = computed(() => {
    const localItemsCount = refRedirectListTable.value ? refRedirectListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalItemCategorys.value,
    }
  })

  const refetchData = () => {
    refRedirectListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, statusFilter], () => {
    refetchData()
  })

  const fetchRedirects = (ctx, callback) => {
    getList({params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        status: statusFilter.value,
      }})
      .then(response => {
        const { data, total } = response.data
        
        totalItemCategorys.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching navbar list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, {position: 'top-center'})
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolveShowVariant = status => {
    if (status) return 'primary'
    return 'warning'
  }

  const resolveShowName = status => {
    if (status) return 'Show'
    return 'Hidden'
  }

  return {
    fetchRedirects,
    tableColumns,
    perPage,
    currentPage,
    totalItemCategorys,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refRedirectListTable,

    resolveShowVariant,
    resolveShowName,
    refetchData,

    // Extra Filters
    statusFilter,
  }
}
