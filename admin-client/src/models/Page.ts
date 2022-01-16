import { Model, ModelOptions, ValueTextOption } from "../types";
import { FileModel } from "./FileModel";

export const PAGE_TYPE_COMMON = "common";
export const PAGE_TYPE_MAIN = "main";

export interface Page extends Model {
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
  images: FileModel[] | null;
  images_id: number[] | null;
}

interface ParentOption {
  key: string | number;
  title: string;
  value: string | number;
  children?: ParentOption[];
}

export interface PageModelOptions extends ModelOptions {
  status: ValueTextOption[];
  type: ValueTextOption[];
  blocks: ValueTextOption[];
  parent: ParentOption[];
}
