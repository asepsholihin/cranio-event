import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/vendor'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refVendorListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'name', sortable: true },
    { key: 'pic', sortable: true },
    { key: 'address', sortable: false },
    { key: 'speciality', sortable: true },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalVendors = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)

  const dataMeta = computed(() => {
    const localItemsCount = refVendorListTable.value ? refVendorListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalVendors.value,
    }
  })

  const refetchData = () => {
    refVendorListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery], () => {
    refetchData()
  })

  const fetchVendors = (ctx, callback) => {
    getList({params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
      }})
      .then(response => {
        const { data, total } = response.data
        
        totalVendors.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching attendance category list',
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
    fetchVendors,
    tableColumns,
    perPage,
    currentPage,
    totalVendors,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refVendorListTable,
    
    refetchData,

    // Extra Filters
  }
}
