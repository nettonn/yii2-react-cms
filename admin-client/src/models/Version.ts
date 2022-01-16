import { Model, ModelOptions, ValueTextOption } from "../types";

export const VERSION_ACTION_UPDATE = "UPDATE";
export const VERSION_ACTION_DELETE = "DELETE";

export interface VersionAttributesCompare {
  attribute: string;
  label: string;
  version_value: string | number | boolean;
  current_value?: string | number | boolean;
  is_diff?: boolean;
}

export interface Version extends Model {
  name: string;
  attributes: { [key: string]: string | number | boolean };
  attributes_compare: VersionAttributesCompare[];
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

export interface VersionModelOptions extends ModelOptions {
  action: ValueTextOption[];
  link_class: ValueTextOption[];
  link_id: ValueTextOption[];
}
