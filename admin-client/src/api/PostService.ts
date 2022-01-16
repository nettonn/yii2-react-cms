import RestService from "./RestService";
import { AxiosRequestConfig } from "axios";
import _merge from "lodash/merge";

export default class PostService extends RestService {
  protected name = "post";
  protected url = "/posts";

  constructor(protected sectionId?: number | string) {
    super();
  }

  indexQueryKey() {
    return `${super.indexQueryKey()}-${this.sectionId}`;
  }

  modelOptionsQueryKey() {
    return `${super.modelOptionsQueryKey()}-${this.sectionId}`;
  }

  indexConfig(): AxiosRequestConfig {
    return _merge(super.indexConfig(), {
      params: { section_id: this.sectionId },
    });
  }

  createConfig(): AxiosRequestConfig {
    return _merge(super.createConfig(), {
      data: { section_id: this.sectionId },
    });
  }

  modelOptionsConfig(): AxiosRequestConfig {
    return _merge(super.modelOptionsConfig(), {
      params: { section_id: this.sectionId },
    });
  }
}

export const postService = new PostService();
