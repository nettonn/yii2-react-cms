import { IModel, IModelOptions, IValueTextOption } from "../types";
import { IFileModel } from "./IFileModel";

export interface IPost extends IModel {
  name: string;
  alias: string;
  description: string;
  content: string;
  status: boolean;
  section_id: number;
  seo_title: string;
  seo_h1: string;
  seo_description: string;
  seo_keywords: string;
  images?: IFileModel[];
  images_id?: number[] | null;
}

export interface IPostModelOptions extends IModelOptions {
  status: IValueTextOption[];
}
