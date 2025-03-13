import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/survey'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'select', sortable: false, label: '#' },
    { key: 'title', sortable: true },
    { key: 'respons', sortable: true },
    { key: 'created_by_name', label: 'Created By', sortable: true },
    { key: 'created_at', sortable: true },
    { key: 'updated_at', sortable: true },
    { key: 'status', sortable: true },

    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalSurveys = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const roleFilter = ref(null)
  const planFilter = ref(null)
  const statusFilter = ref(null)

  const dataMeta = computed(() => {
    const localItemsCount = refListTable.value ? refListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalSurveys.value,
    }
  })

  const refetchData = () => {
    refListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, roleFilter, planFilter, statusFilter], () => {
    refetchData()
  })

  const fetchSurveys = (ctx, callback) => {
    getList({
      params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        status: statusFilter.value,
      }
    })
      .then(response => {
        const { data, total } = response.data

        totalSurveys.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching Survey list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, { position: 'top-center' })
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolveSurveyStatusVariant = status => {
    if (status === 1) return 'primary'
    if (status === 0) return 'warning'
    return 'primary'
  }

  const resolveSurveyStatusName = status => {
    if (status === 1) return 'Active'
    if (status === 0) return 'Inactive'
    return 'Active'
  }

  return {
    fetchSurveys,
    tableColumns,
    perPage,
    currentPage,
    totalSurveys,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refListTable,

    resolveSurveyStatusVariant,
    resolveSurveyStatusName,
    refetchData,

    // Extra Filters
    roleFilter,
    planFilter,
    statusFilter,
  }
}
