import { AxiosRequestConfig } from "axios";
import { $api } from "../http/api";
import { IApiServiceReturn } from "../types";
import { prepareAxiosConfig, requestErrorHandler } from "../utils/functions";

const axiosClient = $api;

export default class HelperService {
  protected name = "helpers";
  protected url = "/helpers";

  get generateAliasQueryKey() {
    return `${this.name}-generate-alias`;
  }

  async generateAlias(value: string): Promise<IApiServiceReturn<string>> {
    try {
      const config = prepareAxiosConfig(this.generateAliasConfig(), {
        value,
      });
      const response = await axiosClient.request<string>(config);
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

  generateAliasConfig(): AxiosRequestConfig {
    return {
      url: `${this.url}/generate-alias`,
      method: "get",
    };
  }
}

export const helperService = new HelperService();
