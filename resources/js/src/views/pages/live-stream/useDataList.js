import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/live-stream'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refUserListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'thumbnail', sortable: true },
    { key: 'title', sortable: true },
    { key: 'status', sortable: false },

    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalLiveStreams = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const roleFilter = ref(null)
  const planFilter = ref(null)
  const statusFilter = ref(null)

  const dataMeta = computed(() => {
    const localItemsCount = refUserListTable.value ? refUserListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalLiveStreams.value,
    }
  })

  const refetchData = () => {
    refUserListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, roleFilter, planFilter, statusFilter], () => {
    refetchData()
  })

  const fetchLiveStream = (ctx, callback) => {
    getList({
      params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        status: statusFilter.value,
      },
    })
      .then(response => {
        const { data, total } = response.data

        totalLiveStreams.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching Live Stream list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, { position: 'top-center' })
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolveLiveStreamStatusVariant = status => {
    if (status === 0) return 'warning'
    if (status === 1) return 'primary'
    return 'primary'
  }

  const resolveLiveStreamStatusName = status => {
    if (status === 0) return 'Inactive'
    if (status === 1) return 'Active'
    return 'Active'
  }

  return {
    fetchLiveStream,
    tableColumns,
    perPage,
    currentPage,
    totalLiveStreams,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refUserListTable,

    resolveLiveStreamStatusVariant,
    resolveLiveStreamStatusName,
    refetchData,

    // Extra Filters
    roleFilter,
    planFilter,
    statusFilter,
  }
}
