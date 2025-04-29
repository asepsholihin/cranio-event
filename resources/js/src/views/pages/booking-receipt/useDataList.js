import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/booking-receipt'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refDataListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'booking_no', sortable: true },
    { key: 'sender_name', sortable: true },
    { key: 'bank_account', sortable: true },
    { key: 'total_price', sortable: true },
    { key: 'created_at', sortable: true },
    { key: 'status', sortable: true },
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
  const packageFilter = ref(null)

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

  watch([currentPage, perPage, searchQuery, statusFilter, packageFilter], () => {
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
        packageName: packageFilter.value,
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
    packageFilter,
  }
}
