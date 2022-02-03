import { Model, ModelOptions, ValueTextOption } from "../types";
import { FileModel } from "./FileModel";

export interface Post extends Model {
  name: string;
  alias: string;
  description: string;
  content: string;
  user_tags: string[];
  status: boolean;
  section_id: number;
  seo_title: string;
  seo_h1: string;
  seo_description: string;
  seo_keywords: string;
  images?: FileModel[];
  images_id?: number[] | null;
}

export interface PostModelOptions extends ModelOptions {
  status: ValueTextOption[];
  tag: ValueTextOption[];
}
