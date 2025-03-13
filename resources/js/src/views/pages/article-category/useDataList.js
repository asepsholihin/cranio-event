import { ref, watch, computed } from "@vue/composition-api";
import { getList } from "@/network/article-category";

// Notification
import { useToast } from "vue-toastification/composition";
import ToastificationContent from "@core/components/toastification/ToastificationContent.vue";

export default function useUsersList() {
    // Use toast
    const toast = useToast();

    const refUserListTable = ref(null);

    // Table Handlers
    const tableColumns = [
        { key: "name", label: "Category Name", sortable: true },
        { key: "slug", sortable: true },
        { key: "description", sortable: false },
        { key: "order", sortable: true },

        { key: "actions" },
    ];
    const perPage = ref(10);
    const totalItems = ref(0);
    const currentPage = ref(1);
    const perPageOptions = [10, 25, 50, 100];
    const searchQuery = ref("");
    const sortBy = ref("id");
    const isSortDirDesc = ref(true);
    const roleFilter = ref(null);
    const planFilter = ref(null);
    const statusFilter = ref(null);

    const dataMeta = computed(() => {
        const localItemsCount = refUserListTable.value
            ? refUserListTable.value.localItems.length
            : 0;
        return {
            from:
                perPage.value * (currentPage.value - 1) +
                (localItemsCount ? 1 : 0),
            to: perPage.value * (currentPage.value - 1) + localItemsCount,
            of: totalItems.value,
        };
    });

    const refetchData = () => {
        refUserListTable.value.refresh();
    };

    watch(
        [
            currentPage,
            perPage,
            searchQuery,
            roleFilter,
            planFilter,
            statusFilter,
        ],
        () => {
            refetchData();
        }
    );

    const fetchData = (ctx, callback) => {
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
            .then((response) => {
                const { data, total } = response.data;

                totalItems.value = total;
                callback(data);
            })
            .catch(() => {
                toast(
                    {
                        component: ToastificationContent,
                        props: {
                            title: "Error fetching Category list",
                            icon: "AlertTriangleIcon",
                            variant: "danger",
                        },
                    },
                    { position: "top-center" }
                );
            });
    };

    // *===============================================---*
    // *--------- UI ---------------------------------------*
    // *===============================================---*

    const resolveStatusVariant = (status) => {
        return status == 0 ? 'warning' : 'primary';
    };

    const resolveStatusName = (status) => {
        return status == 0 ? 'Inactive' : 'Active';
    };

    const resolveActiveVariant = resolveStatusVariant;
    const resolveActiveName = resolveStatusName;

    return {
        fetchData,
        tableColumns,
        perPage,
        currentPage,
        totalItems,
        dataMeta,
        perPageOptions,
        searchQuery,
        sortBy,
        isSortDirDesc,
        refUserListTable,

        resolveStatusVariant,
        resolveStatusName,
        resolveActiveVariant,
        resolveActiveName,
        refetchData,

        // Extra Filters
        roleFilter,
        planFilter,
        statusFilter,
    };
}
