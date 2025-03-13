import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/hotel'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refHotelListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'name', sortable: true, label: 'Hotel Name' },
    { key: 'city', sortable: true },
    { key: 'star', sortable: true },
    { key: 'pic', sortable: false },
    { key: 'last_updated_rate_at', sortable: false, label: 'Last Update Rate' },
    { key: 'last_updated_by_name', sortable: false, label: 'Last Update By' },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalHotels = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const statusFilter = ref(null)

  const dataMeta = computed(() => {
    const localItemsCount = refHotelListTable.value ? refHotelListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalHotels.value,
    }
  })

  const refetchData = () => {
    refHotelListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, statusFilter], () => {
    refetchData()
  })

  const fetchHotels = (ctx, callback) => {
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
        
        totalHotels.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching hotel list',
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

    resolveItineraryStatusVariant,
    resolveItineraryStatusName,
    refetchData,

    // Extra Filters
    statusFilter,
  }
}
