import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/booking-hotel-event'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refDataListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'name', sortable: true },
    { key: 'umroh_trip_title', label: 'Departure', sortable: false },
    { key: 'checkin', label: 'Check in Date', sortable: false },
    { key: 'hotel_name', label: 'Hotel', sortable: false },
    { key: 'room', label: 'Choose Room', sortable: false },
    { key: 'total_room', label: 'Total Room', sortable: false },
    { key: 'additional_item', label: 'Additional Notes', sortable: false },
    { key: 'payment_method', label: 'Payment Information', sortable: false },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalCurrencies = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const statusFilter = ref(null)
  const umrohTripFilter = ref(null)
  const packageFilter = ref(null)
  const hotelFilter = ref(null)
  const roomTypeFilter = ref(null)
  const checkinDateFilter = ref(null)

  const dataMeta = computed(() => {
    const localItemsCount = refDataListTable.value ? refDataListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalCurrencies.value,
    }
  })

  const refetchData = () => {
    refDataListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, statusFilter, umrohTripFilter, packageFilter, hotelFilter, roomTypeFilter, checkinDateFilter], () => {
    refetchData()
  })

  const fetchData = (ctx, callback) => {
    getList({params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        status: statusFilter.value,
        umrohTripId: umrohTripFilter.value,
        packageName: packageFilter.value,
        hotel: hotelFilter.value,
        roomType: roomTypeFilter.value,
        checkinDate: checkinDateFilter.value
      }})
      .then(response => {
        const { data, total } = response.data
        
        totalCurrencies.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching data list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, {position: 'top-center'})
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  return {
    fetchData,
    tableColumns,
    perPage,
    currentPage,
    totalCurrencies,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refDataListTable,

    refetchData,

    // Extra Filters
    statusFilter,
    umrohTripFilter,
    packageFilter,
    hotelFilter,
    roomTypeFilter,
    checkinDateFilter
  }
}
