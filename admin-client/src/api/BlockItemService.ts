import RestService from "./RestService";
import { AxiosRequestConfig } from "axios";
import _merge from "lodash/merge";

export default class BlockItemService extends RestService {
  protected name = "block-item";
  protected url = "/block-items";

  constructor(protected blockId?: number | string) {
    super();
  }

  indexQueryKey() {
    return `${super.indexQueryKey()}-${this.blockId}`;
  }

  modelOptionsQueryKey() {
    return `${super.modelOptionsQueryKey()}-${this.blockId}`;
  }

  indexConfig(): AxiosRequestConfig {
    return _merge(super.indexConfig(), { params: { block_id: this.blockId } });
  }

  createConfig(): AxiosRequestConfig {
    return _merge(super.createConfig(), { data: { block_id: this.blockId } });
  }

  modelOptionsConfig(): AxiosRequestConfig {
    return _merge(super.modelOptionsConfig(), {
      params: { block_id: this.blockId },
    });
  }
}

export const blockItemService = new BlockItemService();
