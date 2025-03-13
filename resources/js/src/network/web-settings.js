import httpService from "./service";
import { toFormData } from '@core/utils/utils';

const resourcePath = "/spa/web-settings";

export const getGeneralSetting = () => {
    return httpService.getHttp().get(`${resourcePath}/general`);
};

export const getIndexPageSetting = () => {
    return httpService.getHttp().get(`${resourcePath}/index-page`);
};

export const storeGeneralSetting = async (data) => {
    return httpService.getHttp().post(`${resourcePath}/general`, await toFormData(data));
};

export const storeIndexPageSetting = async (data) => {
    return httpService.getHttp().post(`${resourcePath}/index-page`, await toFormData(data));
};

export const getCountryCodes = (...args) => {
    return httpService.getHttp().get(resourcePath + '/country-codes', ...args)
}