import RestService from "./RestService";
import { AxiosRequestConfig } from "axios";

export default class BlockItemService extends RestService {
  protected name = "block-item";
  protected url = "/block-items";

  constructor(protected blockId?: number | string) {
    super();
  }

  indexQueryKey() {
    return `${this.name}-index-${this.blockId}`;
  }

  modelOptionsQueryKey() {
    return `${this.name}-model-options-${this.blockId}`;
  }

  indexConfig(): AxiosRequestConfig {
    return {
      url: this.url,
      method: "get",
      params: {
        block_id: this.blockId,
      },
    };
  }

  createConfig(): AxiosRequestConfig {
    return {
      url: this.url,
      method: "post",
      headers: { "Content-Type": "multipart/form-data" },
      params: {
        block_id: this.blockId,
      },
      data: {
        block_id: this.blockId,
      },
    };
  }

  modelOptionsConfig(): AxiosRequestConfig {
    return {
      url: `${this.url}/model-options`,
      method: "get",
      params: {
        block_id: this.blockId,
      },
    };
  }

  modelDefaultsConfig(): AxiosRequestConfig {
    return {
      url: `${this.url}/model-defaults`,
      method: "get",
      params: {
        block_id: this.blockId,
      },
    };
  }
}

export const blockItemService = new BlockItemService();
