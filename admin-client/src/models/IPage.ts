import { IModel, IModelOptions, IValueTextOption } from "../types";
import { IFileModel } from "./IFileModel";

export const PAGE_TYPE_COMMON = "common";
export const PAGE_TYPE_MAIN = "main";

export interface IPage extends IModel {
  name: string;
  alias: string;
  parent_id: number | null;
  description: string;
  type: string;
  content: string;
  status: boolean;
  seo_title: string;
  seo_h1: string;
  seo_description: string;
  seo_keywords: string;
  images: IFileModel[] | null;
  images_id: number[] | null;
}

interface ParentOption {
  key: string | number;
  title: string;
  value: string | number;
  children?: ParentOption[];
}

export interface IPageModelOptions extends IModelOptions {
  status: IValueTextOption[];
  type: IValueTextOption[];
  blocks: IValueTextOption[];
  parent: ParentOption[];
}
