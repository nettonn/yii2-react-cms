import { AxiosRequestConfig } from "axios";
import { authService } from "../api/AuthService";
import { qs, sleep } from "../utils/functions";
import { $api, $apiNoAuth } from "./api";

export const authRequest = (config: AxiosRequestConfig) => {
  if (config.headers) {
    config.headers.Authorization = `Bearer ${authService.getToken()}`;
  }
  return config;
};

export const acceptJsonRequest = (config: AxiosRequestConfig) => {
  if (config.headers) {
    config.headers.Accept = "application/json";
  }
  return config;
};

export const qsParamsSerializerRequest = (config: AxiosRequestConfig) => {
  config.paramsSerializer = (params: {}) => {
    return qs.stringify(params, {
      encodeValuesOnly: true,
      arrayFormat: "comma",
    });
  };

  return config;
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
        authService.setToken(response.data.token);
        isRefreshing = false;
        return $api.request(originalRequest);
      }
    } catch (e) {
      isRefreshing = false;
      authService.removeAuthData();
      window.location.reload();
    }
  }
  return Promise.reject(error);
};
