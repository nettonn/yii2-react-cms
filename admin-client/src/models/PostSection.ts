import { Model, ModelOptions, ValueTextOption } from "../types";

export interface PostSection extends Model {
  name: string;
  type: string;
  alias: string;
  description: string;
  content: string;
  status: boolean;
  seo_title: string;
  seo_h1: string;
  seo_description: string;
  seo_keywords: string;
}

export interface PostSectionModelOptions extends ModelOptions {
  status: ValueTextOption[];
  type: ValueTextOption[];
}
