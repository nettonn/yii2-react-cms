import RestService from "./RestService";
import { AxiosRequestConfig, AxiosResponse } from "axios";

export default class FileService extends RestService {
  protected name = "file";
  protected url = "/files";

  createImageConfig(): AxiosRequestConfig {
    return {
      url: `${this.url}/create-image`,
      method: "post",
      headers: { "Content-Type": "multipart/form-data" },
    };
  }

  prepareModelOptions<T>(response: AxiosResponse<T>) {
    return;
  }
}

export const fileService = new FileService();
