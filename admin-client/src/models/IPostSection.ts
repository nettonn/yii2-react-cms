import { IModel, IModelOptions, IValueTextOption } from "../types";

export interface IPostSection extends IModel {
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

export interface IPostSectionModelOptions extends IModelOptions {
  status: IValueTextOption[];
  type: IValueTextOption[];
}
