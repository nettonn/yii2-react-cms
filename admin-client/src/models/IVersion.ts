import { IModel, IModelOptions, IValueTextOption } from "../types";

export const VERSION_ACTION_UPDATE = "UPDATE";
export const VERSION_ACTION_DELETE = "DELETE";

export interface IVersionAttributesCompare {
  attribute: string;
  label: string;
  version_value: string | number | boolean;
  current_value?: string | number | boolean;
  is_diff?: boolean;
}

export interface IVersion extends IModel {
  name: string;
  attributes: { [key: string]: string | number | boolean };
  attributes_compare: IVersionAttributesCompare[];
  action: typeof VERSION_ACTION_UPDATE | typeof VERSION_ACTION_DELETE;
  action_text: string;
  link_class: string;
  link_class_label: string;
  link_id: number;
  created_at: number;
  created_at_date: string;
  created_at_datetime: string;
  owner_update_url?: string;
}

export interface IVersionModelOptions extends IModelOptions {
  action: IValueTextOption[];
  link_class: IValueTextOption[];
  link_id: IValueTextOption[];
}
