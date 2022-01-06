import { IModel, IModelOptions, IValueTextOption } from "../types";

export const BLOCK_TYPE_SLIDER = "slider";
export const BLOCK_TYPE_GALLERY_SIMPLE = "simple_gallery";

export interface IBlock extends IModel {
  name: string;
  key: string;
  type: string;
  type_label: string;
  has_items: boolean;
  status: boolean;
  created_at: number;
  created_at_date: string;
  updated_at: number;
  updated_at_date: string;
}

export interface IBlockModelOptions extends IModelOptions {
  status: IValueTextOption[];
  type: IValueTextOption[];
}
