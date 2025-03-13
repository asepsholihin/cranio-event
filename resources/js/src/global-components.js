import Vue from 'vue'
import FeatherIcon from '@core/components/feather-icon/FeatherIcon.vue'
import refetchCountBadgeNav from '@/navigation/count-badge'

Vue.component(FeatherIcon.name, FeatherIcon)

Vue.component('CountBadge', {
    template: '<div></div>',
    data () {
        return {
            list: [],
            timer: ''
        }
    },
    created () {
        this.timer = setInterval(this.fetchEventsList, 60000);
    },
    destroyed() {
        this.cancelAutoUpdate()
    },
    methods: {
        fetchEventsList () {
            refetchCountBadgeNav()
        },
        cancelAutoUpdate () {
            clearInterval(this.timer);
        }
    },
    beforeUnmount () {
      this.cancelAutoUpdate();
    }
});