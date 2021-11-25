import { IModel, IModelOptions } from "../types";

export interface IMenuItem extends IModel {
  name: string;
  menu_id: number;
  parent_id: number | null;
  url: string;
  rel: string;
  title: string;
  sort: number;
  status: boolean;
}

interface ParentOption {
  key: string | number;
  title: string;
  value: string | number;
  children?: ParentOption[];
}

export interface IMenuItemModelOptions extends IModelOptions {
  status: { value: string | number; text: string }[];
  parent: ParentOption[];
}
