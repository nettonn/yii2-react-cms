import { IModel, IModelOptions, IValueTextOption } from "../types";

export interface IMenu extends IModel {
  name: string;
  key: string;
  status: boolean;
}

export interface IMenuModelOptions extends IModelOptions {
  status: IValueTextOption[];
}
