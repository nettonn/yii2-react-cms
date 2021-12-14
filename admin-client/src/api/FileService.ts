import RestService from "./RestService";
import { AxiosRequestConfig } from "axios";

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
}

export const fileService = new FileService();
