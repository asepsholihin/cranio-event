import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/hotel-room-type'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refHotelRoomTypeListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'city', sortable: true },
    { key: 'name', sortable: true, label: 'Room Type' },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalHotelRoomTypes = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const statusFilter = ref(null)

  const dataMeta = computed(() => {
    const localItemsCount = refHotelRoomTypeListTable.value ? refHotelRoomTypeListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalHotelRoomTypes.value,
    }
  })

  const refetchData = () => {
    refHotelRoomTypeListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, statusFilter], () => {
    refetchData()
  })

  const fetchHotelRoomTypes = (ctx, callback) => {
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
        
        totalHotelRoomTypes.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching hotel-room-type list',
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
    fetchHotelRoomTypes,
    tableColumns,
    perPage,
    currentPage,
    totalHotelRoomTypes,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refHotelRoomTypeListTable,

    resolveItineraryStatusVariant,
    resolveItineraryStatusName,
    refetchData,

    // Extra Filters
    statusFilter,
  }
}
