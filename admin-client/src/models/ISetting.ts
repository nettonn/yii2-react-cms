import { IModel, IModelOptions, IValueTextOption } from "../types";

export const SETTING_TYPE_BOOL = 1;
export const SETTING_TYPE_INT = 2;
export const SETTING_TYPE_STRING = 3;

export interface ISetting extends IModel {
  name: string;
  key: string;
  type: number;
  content: string;
}

export interface ISettingModelOptions extends IModelOptions {
  type: IValueTextOption[];
}
