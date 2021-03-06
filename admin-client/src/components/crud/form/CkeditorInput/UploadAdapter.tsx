import { $api } from "../../../../http/axios";
import {
  prepareAxiosConfig,
  requestErrorHandler,
} from "../../../../utils/functions";
import { fileService } from "../../../../api/FileService";
import axios, { CancelTokenSource } from "axios";
import { ImageFileModel } from "../../../../models/FileModel";

export class UploadAdapter {
  constructor(
    protected loader: any,
    protected cancelToken: CancelTokenSource = axios.CancelToken.source()
  ) {
    this.loader = loader;
  }

  // Starts the upload process.
  async upload() {
    try {
      const formData = new FormData();
      const file = await this.loader.file;
      formData.append("file", file);
      const config = prepareAxiosConfig(fileService.createImageConfig());
      config.data = formData;
      config.cancelToken = this.cancelToken.token;
      const response = await $api.request<ImageFileModel>(config);
      return {
        default: response.data.image_thumbs.normal,
      };
    } catch (e: any) {
      const errors = requestErrorHandler(e);
      throw new Error(errors.message);
    }
  }

  // Aborts the upload process.
  abort() {
    this.cancelToken.cancel();
  }
}

export function UploadAdapterPlugin(editor: typeof CKEDITOR) {
  editor.plugins.get("FileRepository").createUploadAdapter = (loader: any) => {
    // Configure the URL to the upload script in your back-end here!
    return new UploadAdapter(loader);
  };
}
