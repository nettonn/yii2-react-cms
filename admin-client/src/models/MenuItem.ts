import { Model, ModelOptions, ValueTextOption } from "../types";

export interface MenuItem extends Model {
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

export interface MenuItemModelOptions extends ModelOptions {
  status: ValueTextOption[];
  parent: ParentOption[];
}
