import { AxiosRequestConfig, AxiosResponse } from "axios";
import { authService } from "../api/AuthService";
import { logMessage, sleep } from "../utils/functions";
import { $api, $apiNoAuth } from "./axios";
import { queryStringStringify } from "../utils/qs";
import isEmpty from "lodash/isEmpty";

export const authRequest = (config: AxiosRequestConfig) => {
  if (config.headers) {
    config.headers.Authorization = `Bearer ${authService.getStorageToken()}`;
  }
  return config;
};

export const acceptJsonRequest = (config: AxiosRequestConfig) => {
  if (config.headers) {
    config.headers.Accept = "application/json";
  }
  if (!isEmpty(config.data) && config.headers) {
    config.headers["Content-Type"] = "application/json";
  }
  return config;
};

export const qsParamsSerializerRequest = (config: AxiosRequestConfig) => {
  config.paramsSerializer = (params: {}) => {
    return queryStringStringify(params);
  };

  return config;
};

export const responseInterceptor = (response: AxiosResponse) => {
  if (
    process.env.NODE_ENV === "development" &&
    response.headers["x-development-log"] &&
    response.headers["x-debug-link"]
  ) {
    logMessage(response.headers["x-debug-link"]);
  }
  return response;
};

let isRefreshing = false;

export const refreshTokenResponse = async (error: any) => {
  const originalRequest = error.config;
  if (isRefreshing) {
    await sleep(100);
    return $api.request(originalRequest);
  }
  if (
    error.response &&
    error.response.status === 401 &&
    !originalRequest._retry
  ) {
    originalRequest._retry = true;
    isRefreshing = true;
    try {
      const response = await $apiNoAuth.request(authService.refreshConfig());
      if (response && response.data) {
        authService.setStorageToken(response.data.token);
        isRefreshing = false;
        return $api.request(originalRequest);
      }
    } catch (e) {
      isRefreshing = false;
      authService.clearStorage();
      window.location.reload();
    }
  }
  return Promise.reject(error);
};
