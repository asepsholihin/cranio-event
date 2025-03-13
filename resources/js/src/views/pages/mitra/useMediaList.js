import { ref, watch, computed } from '@vue/composition-api'
import { getMediaList } from '@/network/mitra'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refUserListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'title', sortable: true },
    { key: 'category', sortable: true },
    { key: 'created_at', sortable: true },
    { key: 'updated_at', sortable: true },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalMediaMarketings = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const dateFilter = ref('')

  const dataMeta = computed(() => {
    const localItemsCount = refUserListTable.value ? refUserListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalMediaMarketings.value,
    }
  })

  const refetchData = () => {
    refUserListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, dateFilter], () => {
    refetchData()
  })

  const fetchMediaMarketing = (ctx, callback) => {
    getMediaList({
      params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        date: dateFilter.value
      },
    })
      .then(response => {
        const { data, total } = response.data

        totalMediaMarketings.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching MediaMarketing list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, { position: 'top-center' })
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  return {
    fetchMediaMarketing,
    tableColumns,
    perPage,
    currentPage,
    totalMediaMarketings,
    dataMeta,
    perPageOptions,
    searchQuery,
    dateFilter,
    sortBy,
    isSortDirDesc,
    refUserListTable,
    
    refetchData,
  }
}
