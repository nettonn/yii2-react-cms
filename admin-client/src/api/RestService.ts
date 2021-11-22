import { $api } from "../http/api";
import { IModel, IValidationErrorType } from "../types";
import { AxiosRequestConfig, AxiosResponse } from "axios";
import { prepareAxiosConfig, requestErrorHandler } from "../utils/functions";
import { queryClient } from "../http/query-client";

const axiosClient = $api;

export interface IRestServiceReturn<T> {
  success?: boolean;
  data?: T;
  status?: number;
  error?: string;
  pagination?: IRestServicePagination;
  validationErrors?: IValidationErrorType[];
}

export interface IRestServiceIndexQueryParams {
  page?: number;
  sortField?: string | null;
  sortDirection?: string | null;
  search?: string;
  filters?: any;
  ids?: number[];
}

export interface IRestServicePagination {
  currentPage?: number;
  totalCount?: number;
  perPage?: number;
  pageCount?: number;
}

export default class RestService {
  name: string = "";
  url: string = "";

  listQueryKey() {
    return `${this.name}-list`;
  }

  indexQueryKey() {
    return `${this.name}-index`;
  }

  modelOptionsQueryKey() {
    return `${this.name}-model-options`;
  }

  modelDefaultsQueryKey() {
    return `${this.name}-model-defaults`;
  }

  viewQueryKey() {
    return `${this.name}-view`;
  }

  async list<T extends IModel = IModel>(
    limit?: number,
    signal?: AbortSignal
  ): Promise<IRestServiceReturn<T[]>> {
    try {
      const config = prepareAxiosConfig(this.indexConfig(), {
        list: true,
        limit,
      });
      if (signal) config.signal = signal;
      const response = await axiosClient.request<T[]>(config);

      return {
        success: true,
        data: response.data,
      };
    } catch (e: any) {
      const errors = requestErrorHandler(e);
      return {
        success: false,
        status: errors.status,
        error: errors.message,
      };
    }
  }

  async index<T extends IModel = IModel>(
    params?: IRestServiceIndexQueryParams,
    signal?: AbortSignal
  ): Promise<IRestServiceReturn<T[]>> {
    try {
      const config = prepareAxiosConfig(this.indexConfig(), params);
      if (signal) config.signal = signal;
      const response = await axiosClient.request<T[]>(config);
      const pagination: IRestServicePagination = {};

      if (response.headers["x-pagination-current-page"] !== undefined)
        pagination.currentPage = parseInt(
          response.headers["x-pagination-current-page"]
        );
      if (response.headers["x-pagination-total-count"] !== undefined)
        pagination.totalCount = parseInt(
          response.headers["x-pagination-total-count"]
        );

      if (response.headers["x-pagination-per-page"] !== undefined)
        pagination.perPage = parseInt(
          response.headers["x-pagination-per-page"]
        );

      if (response.headers["x-pagination-page-count"] !== undefined)
        pagination.pageCount = parseInt(
          response.headers["x-pagination-page-count"]
        );

      this.prepareModelOptions<T[]>(response);

      return {
        success: true,
        data: response.data,
        pagination,
      };
    } catch (e: any) {
      const errors = requestErrorHandler(e);
      return {
        success: false,
        status: errors.status,
        error: errors.message,
      };
    }
  }

  async view<T>(
    id: number | string,
    signal?: AbortSignal
  ): Promise<IRestServiceReturn<T>> {
    try {
      const config = prepareAxiosConfig(this.viewConfig(id));
      if (signal) config.signal = signal;
      const response = await axiosClient.request<T>(config);
      this.prepareModelOptions(response);
      return {
        success: true,
        data: response.data,
      };
    } catch (e: any) {
      const errors = requestErrorHandler(e);
      return {
        success: false,
        status: errors.status,
        error: errors.message,
      };
    }
  }

  async create<T>(values: T): Promise<IRestServiceReturn<T>> {
    try {
      const config = prepareAxiosConfig(this.createConfig(), null, values);

      const response = await axiosClient.request<T>(config);

      // queryClient.invalidateQueries(this.listQueryKey());
      // queryClient.invalidateQueries(this.indexQueryKey());

      this.prepareModelOptions(response);

      return { success: true, data: response.data };
    } catch (e: any) {
      const errors = requestErrorHandler(e);
      if (errors.validationErrors) {
        return {
          success: false,
          validationErrors: errors.validationErrors,
        };
      }
      return {
        success: false,
        status: errors.status,
        error: errors.message,
      };
    }
  }

  async update<T>(
    id: number | string,
    values: T
  ): Promise<IRestServiceReturn<T>> {
    try {
      const config = prepareAxiosConfig(this.updateConfig(id), null, values);

      const response = await axiosClient.request<T>(config);

      // queryClient.invalidateQueries(this.listQueryKey());
      // queryClient.invalidateQueries(this.indexQueryKey());

      this.prepareModelOptions(response);

      return { success: true, data: response.data };
    } catch (e: any) {
      const errors = requestErrorHandler(e);
      if (errors.validationErrors) {
        return {
          success: false,
          validationErrors: errors.validationErrors,
        };
      }
      return {
        success: false,
        status: errors.status,
        error: errors.message,
      };
    }
  }

  async delete(id: number | string): Promise<IRestServiceReturn<null>> {
    try {
      const response = await axiosClient.request(this.deleteConfig(id));

      // queryClient.invalidateQueries(this.listQueryKey());
      // queryClient.invalidateQueries(this.indexQueryKey());

      this.prepareModelOptions(response);

      return { success: true, data: response.data };
    } catch (e: any) {
      const errors = requestErrorHandler(e);
      return {
        success: false,
        status: errors.status,
        error: errors.message,
      };
    }
  }

  async modelOptions<T>(signal?: AbortSignal): Promise<IRestServiceReturn<T>> {
    try {
      const config = prepareAxiosConfig(this.modelOptionsConfig());
      if (signal) config.signal = signal;
      const response = await axiosClient.request<T>(config);
      return { success: true, data: response.data };
    } catch (e: any) {
      const errors = requestErrorHandler(e);
      return {
        success: false,
        status: errors.status,
        error: errors.message,
      };
    }
  }

  async modelDefaults<T>(signal?: AbortSignal): Promise<IRestServiceReturn<T>> {
    try {
      const config = prepareAxiosConfig(this.modelDefaultsConfig());
      if (signal) config.signal = signal;
      const response = await axiosClient.request<T>(config);
      return { success: true, data: response.data };
    } catch (e: any) {
      const errors = requestErrorHandler(e);
      return {
        success: false,
        status: errors.status,
        error: errors.message,
      };
    }
  }

  indexConfig(): AxiosRequestConfig {
    return {
      url: this.url,
      method: "get",
    };
  }

  createConfig(): AxiosRequestConfig {
    return {
      url: this.url,
      method: "post",
      headers: { "Content-Type": "multipart/form-data" },
    };
  }

  viewConfig(id: number | string): AxiosRequestConfig {
    return {
      url: `${this.url}/${id}`,
      method: "get",
    };
  }

  updateConfig(id: number | string): AxiosRequestConfig {
    return {
      url: `${this.url}/${id}`,
      method: "put",
    };
  }

  deleteConfig(id: number | string): AxiosRequestConfig {
    return {
      url: `${this.url}/${id}`,
      method: "delete",
    };
  }

  modelOptionsConfig(): AxiosRequestConfig {
    return {
      url: `${this.url}/model-options`,
      method: "get",
    };
  }

  modelDefaultsConfig(): AxiosRequestConfig {
    return {
      url: `${this.url}/model-defaults`,
      method: "get",
    };
  }

  prepareModelOptions<T>(response: AxiosResponse<T>) {
    if (response.headers["x-model-options-last-modified"] !== undefined) {
      const modelOptionsLastModified = parseInt(
        response.headers["x-model-options-last-modified"]
      );

      const modelOptionsQueryState = queryClient.getQueryState(
        this.modelOptionsQueryKey()
      );

      if (
        !modelOptionsLastModified ||
        !modelOptionsQueryState ||
        (modelOptionsQueryState &&
          modelOptionsQueryState.dataUpdatedAt <
            modelOptionsLastModified * 1000)
      ) {
        queryClient.invalidateQueries(this.modelOptionsQueryKey());
      }
    } else {
      queryClient.invalidateQueries(this.modelOptionsQueryKey());
    }
  }
}
