import { ref, watch, computed } from '@vue/composition-api'
import { getWithdrawList } from '@/network/mitra'

// Notification
import { useToast } from 'vue-toastification/composition'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default function useTransactionsList() {
  // Use toast
  const toast = useToast()

  const refTransactionListTable = ref(null)

  // Table Handlers
  const tableColumns = [
    { key: 'invoice_no', sortable: true, label: 'Invoice' },
    { key: 'fee', sortable: true, label: 'Nominal' },
    { key: 'payment_method', sortable: true },
    { key: 'created_at', sortable: true, label: 'Tanggal Pengajuan' },
    { key: 'status', sortable: true },
  ]
  const perPage = ref(10)
  const totalTransactions = ref(0)
  const currentPage = ref(1)
  const perPageOptions = [10, 25, 50, 100]
  const searchQuery = ref('')
  const sortBy = ref('id')
  const isSortDirDesc = ref(true)

  const dataMeta = computed(() => {
    const localItemsCount = refTransactionListTable.value ? refTransactionListTable.value.localItems.length : 0
    return {
      from: perPage.value * (currentPage.value - 1) + (localItemsCount ? 1 : 0),
      to: perPage.value * (currentPage.value - 1) + localItemsCount,
      of: totalTransactions.value,
    }
  })

  const refetchData = () => {
    refTransactionListTable.value.refresh()
  }

  watch([currentPage, perPage, searchQuery], () => {
    refetchData()
  })

  const fetchTransactions = (ctx, callback) => {
    getWithdrawList({params: {
        q: searchQuery.value,
        perPage: perPage.value,
        page: currentPage.value,
        sortBy: sortBy.value,
        sortDesc: isSortDirDesc.value,
      }})
      .then(response => {
        const { data, total } = response.data
        
        totalTransactions.value = total
        callback(data)
      })
      .catch(() => {
        toast({
          component: ToastificationContent,
          props: {
            title: 'Error fetching Mitra list',
            icon: 'AlertTriangleIcon',
            variant: 'danger',
          },
        }, {position: 'top-center'})
      })
  }

  // *===============================================---*
  // *--------- UI ---------------------------------------*
  // *===============================================---*

  const resolveTransactionStatusVariant = status => {
    if (status === 1) return 'warning'
    if (status === 2) return 'info'
    if (status === 3) return 'success'
    if (status === 4) return 'danger'
    return 'warning'
  }

  const resolveTransactionStatus = status => {
    if (status === 1) return 'Menunggu Konfirmasi'
    if (status === 2) return 'Dalam Proses'
    if (status === 3) return 'Sudah ditransfer'
    if (status === 4) return 'Ditolak'
    return 'Menunggu Konfirmasi'
  }

  return {
    fetchTransactions,
    tableColumns,
    perPage,
    currentPage,
    totalTransactions,
    dataMeta,
    perPageOptions,
    searchQuery,
    sortBy,
    isSortDirDesc,
    refTransactionListTable,

    resolveTransactionStatusVariant,
    resolveTransactionStatus,
    refetchData,
  }
}
