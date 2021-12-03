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
  type: number;
  content: string;
  attributes: { [key: string]: string | number | boolean };
  attributes_compare: IVersionAttributesCompare[];
  action: typeof VERSION_ACTION_UPDATE | typeof VERSION_ACTION_DELETE;
  action_text: string;
}

export interface IVersionModelOptions extends IModelOptions {
  action: IValueTextOption[];
}
