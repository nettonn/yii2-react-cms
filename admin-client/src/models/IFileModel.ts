import { IImageThumbs, IModel } from "../types";

export interface IFileModel extends IModel {
  name: string;
  ext: string;
  is_image: boolean;
  file_thumb?: string;
  image_thumbs: IImageThumbs | null;
  mime: string;
  size: number;
  sort: number;
}

export interface IImageFileModel extends IFileModel {
  is_image: true;
  image_thumbs: IImageThumbs;
}
