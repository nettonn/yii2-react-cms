import { IModel, IModelOptions, IValueTextOption } from "../types";

export interface IBlockItem extends IModel {
  name: string;
  type: string;
  block_id: number;
  sort: number;
  status: boolean;
  created_at: number;
  created_at_date: string;
  updated_at: number;
  updated_at_date: string;
}

export interface IBlockItemModelOptions extends IModelOptions {
  status: IValueTextOption[];
}
