import { $api } from "../http/axios";
import { ApiServicePagination, Model } from "../types";
import { AxiosRequestConfig, AxiosResponse } from "axios";
import { prepareAxiosConfig } from "../utils/functions";
import { queryClient } from "../http/query-client";

export interface IRestServiceIndexQueryParams {
  page?: number;
  sortField?: string | null;
  sortDirection?: string | null;
  search?: string;
  filters?: any;
  ids?: number[];
  list?: boolean;
  limit?: number;
}

export default class RestService {
  protected name?: string;
  protected url?: string;

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

  async index<T extends Model = Model>(
    params?: IRestServiceIndexQueryParams,
    signal?: AbortSignal
  ): Promise<{ pagination: ApiServicePagination; data: T[] }> {
    const config = prepareAxiosConfig(this.indexConfig(), { params });
    if (signal) config.signal = signal;
    const response = await $api.request<T[]>(config);
    const pagination: ApiServicePagination = {};

    if (response.headers["x-pagination-current-page"] !== undefined)
      pagination.currentPage = parseInt(
        response.headers["x-pagination-current-page"]
      );
    if (response.headers["x-pagination-total-count"] !== undefined)
      pagination.totalCount = parseInt(
        response.headers["x-pagination-total-count"]
      );

    if (response.headers["x-pagination-per-page"] !== undefined)
      pagination.perPage = parseInt(response.headers["x-pagination-per-page"]);

    if (response.headers["x-pagination-page-count"] !== undefined)
      pagination.pageCount = parseInt(
        response.headers["x-pagination-page-count"]
      );

    this.prepareModelOptions<T[]>(response);

    return {
      data: response.data,
      pagination,
    };
  }

  async view<T>(id: number | string, signal?: AbortSignal): Promise<T> {
    const config = prepareAxiosConfig(this.viewConfig(id));
    if (signal) config.signal = signal;
    const response = await $api.request<T>(config);
    this.prepareModelOptions(response);

    return response.data;
  }

  async create<T>(values: T): Promise<T> {
    const config = prepareAxiosConfig(this.createConfig(), { data: values });

    const response = await $api.request<T>(config);

    // queryClient.invalidateQueries(this.listQueryKey());
    // queryClient.invalidateQueries(this.indexQueryKey());

    this.prepareModelOptions(response);

    return response.data;
  }

  async update<T>(id: number | string, values: T): Promise<T> {
    const config = prepareAxiosConfig(this.updateConfig(id), {
      data: values,
    });

    const response = await $api.request<T>(config);

    // queryClient.invalidateQueries(this.listQueryKey());
    // queryClient.invalidateQueries(this.indexQueryKey());

    this.prepareModelOptions(response);

    return response.data;
  }

  async delete(id: number | string): Promise<null> {
    const response = await $api.request(this.deleteConfig(id));

    queryClient.invalidateQueries(this.listQueryKey());
    queryClient.invalidateQueries(this.indexQueryKey());

    this.prepareModelOptions(response);

    return response.data;
  }

  async modelOptions<T>(signal?: AbortSignal): Promise<T> {
    const config = prepareAxiosConfig(this.modelOptionsConfig());
    if (signal) config.signal = signal;
    const response = await $api.request<T>(config);
    return response.data;
  }

  async modelDefaults<T>(signal?: AbortSignal): Promise<T> {
    const config = prepareAxiosConfig(this.modelDefaultsConfig());
    if (signal) config.signal = signal;
    const response = await $api.request<T>(config);
    return response.data;
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
