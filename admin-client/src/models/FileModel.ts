import { ImageThumbs, Model } from "../types";

export interface FileModel extends Model {
  name: string;
  ext: string;
  is_image: boolean;
  file_thumb?: string;
  image_thumbs: ImageThumbs | null;
  mime: string;
  size: number;
  sort: number;
}

export interface ImageFileModel extends FileModel {
  is_image: true;
  image_thumbs: ImageThumbs;
}
