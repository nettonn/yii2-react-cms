import RestService from "./RestService";
import { AxiosRequestConfig } from "axios";

export default class PostService extends RestService {
  protected name = "post";
  protected url = "/posts";

  constructor(protected sectionId?: number | string) {
    super();
  }

  indexQueryKey() {
    return `${this.name}-index-${this.sectionId}`;
  }

  modelOptionsQueryKey() {
    return `${this.name}-model-options-${this.sectionId}`;
  }

  indexConfig(): AxiosRequestConfig {
    return {
      url: this.url,
      method: "get",
      params: {
        section_id: this.sectionId,
      },
    };
  }

  createConfig(): AxiosRequestConfig {
    return {
      url: this.url,
      method: "post",
      headers: { "Content-Type": "multipart/form-data" },
      data: {
        section_id: this.sectionId,
      },
    };
  }

  modelOptionsConfig(): AxiosRequestConfig {
    return {
      url: `${this.url}/model-options`,
      method: "get",
      params: {
        section_id: this.sectionId,
      },
    };
  }

  modelDefaultsConfig(): AxiosRequestConfig {
    return {
      url: `${this.url}/model-defaults`,
      method: "get",
      params: {
        section_id: this.sectionId,
      },
    };
  }
}

export const postService = new PostService();
