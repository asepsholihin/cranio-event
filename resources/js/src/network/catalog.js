import { generateResource } from "../@core/utils/utils";
import httpService from './service'

const resourcePath = "/spa/catalog";

export const categories = generateResource(`${resourcePath}/categories`);
export const subCategories = generateResource(`${resourcePath}/sub-categories`);
export const products = generateResource(`${resourcePath}/products`);
export const getListCategories = (...args) => {
    return httpService.getHttp().get(resourcePath+"/categories-list", ...args)
}
export const getListSubcategory = (id) => {
    return httpService.getHttp().get(resourcePath+"/sub-categories-list"+'/'+id)
}
export const postAction = (...args) => {
    return httpService.getHttp().post(`${resourcePath}/products/action`, ...args)
}

export const deleteProductImage = (...args) => {
    return httpService.getHttp().post(`${resourcePath}/products/delete-image`, ...args)
}

export const getCategorySearch = (args) => {
    return httpService.getHttp().get(resourcePath + '/categories-search', {params: args})
}

export const getSubCategorySearch = (args) => {
    return httpService.getHttp().get(resourcePath + '/sub-categories-search', {params: args})
}
