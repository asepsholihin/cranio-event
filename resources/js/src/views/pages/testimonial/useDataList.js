import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/testimonial'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refUserListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'customer_name', sortable: true },
    { key: 'customer_job', sortable: false },
    { key: 'trip', sortable: true },
    { key: 'testimony', sortable: true },
    { key: 'publish_status', sortable: false },

    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalTestimonials = ref(0)
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
      of: totalTestimonials.value,
    }
  })

  const refetchData = () => {
    refUserListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, roleFilter, planFilter, statusFilter], () => {
    refetchData()
  })

  const fetchTestimonials = (ctx, callback) => {
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
        
        totalTestimonials.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching Testimonial list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, {position: 'top-center'})
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolveTestimonialStatusVariant = publish_status => {
    if (publish_status === 0) return 'warning'
    if (publish_status === 1) return 'primary'
    return 'primary'
  }

  const resolveTestimonialStatusName = publish_status => {
    if (publish_status === 0) return 'Inactive'
    if (publish_status === 1) return 'Active'
    return 'Active'
  }

  return {
    fetchTestimonials,
    tableColumns,
    perPage,
    currentPage,
    totalTestimonials,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refUserListTable,

    resolveTestimonialStatusVariant,
    resolveTestimonialStatusName,
    refetchData,

    // Extra Filters
    roleFilter,
    planFilter,
    statusFilter,
  }
}
