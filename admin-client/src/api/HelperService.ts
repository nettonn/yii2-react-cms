import { AxiosRequestConfig } from "axios";
import { $api } from "../http/axios";
import { prepareAxiosConfig } from "../utils/functions";

export default class HelperService {
  protected name = "helpers";
  protected url = "/helpers";

  get generateAliasQueryKey() {
    return `${this.name}-generate-alias`;
  }

  async generateAlias(value: string): Promise<string> {
    const config = prepareAxiosConfig(this.generateAliasConfig(), {
      params: {
        value,
      },
    });
    const response = await $api.request<string>(config);
    return response.data;
  }

  generateAliasConfig(): AxiosRequestConfig {
    return {
      url: `${this.url}/generate-alias`,
      method: "get",
    };
  }
}

export const helperService = new HelperService();
