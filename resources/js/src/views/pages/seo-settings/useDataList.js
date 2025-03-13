import { ref, watch, computed } from '@vue/composition-api'
import { getList } from '@/network/seo-settings'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useUsersList() {
  // Use toast
  const toast = useToast()

  const refUserListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'page', sortable: true },
    { key: 'meta_title', sortable: false },
    { key: 'meta_description', sortable: true },

    { key: 'actions' },
  ]
  const perPage = ref(10)
  const totalSocialMedias = ref(0)
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
      of: totalSocialMedias.value,
    }
  })

  const refetchData = () => {
    refUserListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery, roleFilter, planFilter, statusFilter], () => {
    refetchData()
  })

  const fetchSocialMedia = (ctx, callback) => {
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

        totalSocialMedias.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching SocialMedia list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, { position: 'top-center' })
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolveSocialMediaStatusVariant = status => {
    if (status === 0) return 'warning'
    if (status === 1) return 'primary'
    return 'primary'
  }

  const resolveSocialMediaStatusName = status => {
    if (status === 0) return 'Inactive'
    if (status === 1) return 'Active'
    return 'Active'
  }

  return {
    fetchSocialMedia,
    tableColumns,
    perPage,
    currentPage,
    totalSocialMedias,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refUserListTable,

    resolveSocialMediaStatusVariant,
    resolveSocialMediaStatusName,
    refetchData,

    // Extra Filters
    roleFilter,
    planFilter,
    statusFilter,
  }
}
