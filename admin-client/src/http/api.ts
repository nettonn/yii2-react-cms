import axios from "axios";
import {
  acceptJsonRequest,
  authRequest,
  qsParamsSerializerRequest,
  refreshTokenResponse,
  responseInterceptor,
} from "./interceptors";

export const API_URL = process.env.REACT_APP_API_URL;

const baseConfig = {
  withCredentials: true,
  baseURL: API_URL,
};

const $api = axios.create(baseConfig);
const $apiNoAuth = axios.create(baseConfig);
const $axios = axios.create();

$api.interceptors.request.use(authRequest);
$api.interceptors.request.use(acceptJsonRequest);
$api.interceptors.request.use(qsParamsSerializerRequest);

$api.interceptors.response.use(responseInterceptor, refreshTokenResponse);

$apiNoAuth.interceptors.request.use(acceptJsonRequest);
$apiNoAuth.interceptors.request.use(qsParamsSerializerRequest);

export { $api, $apiNoAuth, $axios };
