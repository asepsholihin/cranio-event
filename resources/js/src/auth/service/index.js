import axiosIns from '@/libs/axios'

const csrfCookiePath = '/sanctum/csrf-cookie'
const loginPath = '/login'
const logoutPath = '/logout'

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

                return Promise.reject(error)
            },
          )
    }

    logout(...args) {
        return this.axiosIns.post(logoutPath, ...args)
    }

    login(...args) {
        return this.axiosIns.post(loginPath, ...args)
    }

    refreshCookieSession(...args) {
        return this.axiosIns.get(csrfCookiePath, ...args)
    }
}
