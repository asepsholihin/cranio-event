import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/master-hotel-event'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refUserListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'index', sortable: true, label: '#'},
    { key: 'hotel_name', sortable: true, label: 'Hotel Name'},
    { key: 'contact_information', sortable: true, label: 'Contact Information' },
    { key: 'purposes', sortable: false, label: 'Purposes'},
    { key: 'half_day', sortable: false, label: 'Half Day'},
    { key: 'full_day', sortable: false, label: 'Full Day'},
    { key: 'advantages', sortable: false, label: 'Advantages'},
    { key: 'disadvantages', sortable: false, label: 'Disadvantages'},
    { key: 'counter', sortable: false, label: 'Selected Counter'},

    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalPackages = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const purpusesFilter = ref(null)
  const preferenceFilter = ref(null)
  const statusFilter = ref(null)

  const dataMeta = computed(() => {
    const localItemsCount = refUserListTable.value ? refUserListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalPackages.value,
    }
  })

  const refetchData = () => {
    refUserListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, purpusesFilter, preferenceFilter, statusFilter], () => {
    refetchData()
  })

  const fetchPackages = (ctx, callback) => {
    getList({params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        purpuses: purpusesFilter.value,
        preference: preferenceFilter.value,
        status: statusFilter.value,
      }})
      .then(response => {
        const { data, total } = response.data

        totalPackages.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching Package list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, {position: 'top-center'})
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolvePackageStatusVariant = status => {
    if (status) return 'primary'
    return 'warning'
  }

  const resolvePackageStatusName = status => {
    if (status) return 'Active'
    return 'Inactive'
  }

  return {
    fetchPackages,
    tableColumns,
    perPage,
    currentPage,
    totalPackages,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refUserListTable,

    resolvePackageStatusVariant,
    resolvePackageStatusName,
    refetchData,

    // Extra Filters
    purpusesFilter,
    preferenceFilter,
    statusFilter,
  }
}
