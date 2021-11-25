import { IModel, IModelOptions } from "../types";

export interface IMenu extends IModel {
  name: string;
  key: string;
  status: boolean;
}

export interface IMenuModelOptions extends IModelOptions {
  status: { value: string | number; text: string }[];
}
