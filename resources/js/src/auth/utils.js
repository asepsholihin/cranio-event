export const authUser = 'auth-user'

export const isUserLoggedIn = () => {
    if (localStorage.getItem(authUser) === null) {
        return false
    }

    return true
}

export const getUserData = () => {
    return JSON.parse(localStorage.getItem(authUser))
}

export const hasPermission = permission => {
    const user = getUserData()

    if (! user)
        return false

    for (let role of user.role_list) {
        if (role == 'Super-Admin')
            return true
    }

    for (let element of user.permission_list) {
        if (element == permission)
            return true
    }

    return false
}

export const removeUserData = () => {
    return localStorage.removeItem(authUser)
}

export const getHomeRouteForLoggedInUser = () => {
    return '/'
}
