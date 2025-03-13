import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/web-inquiry'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refInquiryListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'full_name', sortable: true },
    { key: 'wa_number', sortable: true },
    { key: 'email', sortable: true },
    { key: 'message', sortable: false },
    { key: 'from_page', sortable: false },
    { key: 'created_at', sortable: false },
    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalInquiries = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const startDate = ref('')
  const endDate = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)
  const roleFilter = ref(null)
  const planFilter = ref(null)
  const statusFilter = ref(null)
  const pageFilter = ref(null)

  const dataMeta = computed(() => {
    const localItemsCount = refInquiryListTable.value ? refInquiryListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalInquiries.value,
    }
  })

  const refetchData = () => {
    refInquiryListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, roleFilter, planFilter, statusFilter, startDate, endDate, pageFilter], () => {
    refetchData()
  })

  const fetchInquiries = (ctx, callback) => {
    getList({
      params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
        status: statusFilter.value,
        fromPage: pageFilter.value,
        startDate: startDate.value,
        endDate: endDate.value,
      },
    })
      .then(response => {
        const { data, total } = response.data
        totalInquiries.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching Inquiry list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, { position: 'top-center' })
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*
  const resolveInquiryStatusVariant = status => {
    if (status === 0) return 'warning'
    if (status === 1) return 'primary'
    return 'primary'
  }

  const resolveInquiryStatusName = status => {
    if (status === 0) return 'Inactive'
    if (status === 1) return 'Active'
    return 'Active'
  }

  return {
    fetchInquiries,
    tableColumns,
    perPage,
    currentPage,
    totalInquiries,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refInquiryListTable,
    startDate,
    endDate,

    resolveInquiryStatusVariant,
    resolveInquiryStatusName,
    refetchData,

    // Extra Filters
    roleFilter,
    planFilter,
    statusFilter,
    pageFilter,
  }
}
