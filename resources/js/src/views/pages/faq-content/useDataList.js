import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/faq-content'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refItemListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'category_name', sortable: true },
    { key: 'title', sortable: true },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalItems = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const statusFilter = ref(null)

  const dataMeta = computed(() => {
    const localItemsCount = refItemListTable.value ? refItemListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalItems.value,
    }
  })

  const refetchData = () => {
    refItemListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, statusFilter], () => {
    refetchData()
  })

  const fetchItems = (ctx, callback) => {
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
        
        totalItems.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching item list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, {position: 'top-center'})
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolveItineraryStatusVariant = status => {
    if (status) return 'primary'
    return 'warning'
  }

  const resolveItineraryStatusName = status => {
    if (status) return 'Active'
    return 'Inactive'
  }

  return {
    fetchItems,
    tableColumns,
    perPage,
    currentPage,
    totalItems,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refItemListTable,

    resolveItineraryStatusVariant,
    resolveItineraryStatusName,
    refetchData,

    // Extra Filters
    statusFilter,
  }
}
