import { Model, ModelOptions, ValueTextOption } from "../types";

export const SETTING_TYPE_BOOL = 1;
export const SETTING_TYPE_INT = 2;
export const SETTING_TYPE_STRING = 3;

export interface Setting extends Model {
  name: string;
  key: string;
  type: number;
  content: string;
}

export interface SettingModelOptions extends ModelOptions {
  type: ValueTextOption[];
}
