import Vue from 'vue'

// axios
import axios from 'axios'

const axiosIns = axios.create({
    headers: {'Accept': 'application/json'},
    withCredentials: true
})

Vue.prototype.$http = axiosIns

export default axiosIns
