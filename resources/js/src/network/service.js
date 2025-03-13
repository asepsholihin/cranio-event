import axiosIns from '@/libs/axios'
import { removeUserData } from '@/auth/utils'

const csrfCookiePath = '/sanctum/csrf-cookie'

export default new class {
    axiosIns = null

    constructor() {
        this.axiosIns = axiosIns
        this.axiosIns.interceptors.response.use(
            response => response,
            error => {
                const { config, response } = error
                const originalRequest = config

                if (response && response.status === 419) {
                    return new Promise(resolve => {
                        this.refreshCookieSession().then(success => {
                            resolve(this.axiosIns(originalRequest));
                        })
                        .catch(error => {
                            return Promise.reject(error)
                        })
                    })
                }

                if (response && response.status === 404) {
                    window.location.href = "/not-found";
                }

                if (response && response.status === 401) {
                    removeUserData()
                    window.location.href = "/login";
                }

                return Promise.reject(error)
            },
          )
    }

    getHttp() {
        return this.axiosIns
    }

    refreshCookieSession(...args) {
        return this.axiosIns.get(csrfCookiePath, ...args)
    }
}
