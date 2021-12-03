import { IModel, IModelOptions, IValueTextOption } from "../types";

export interface IUser extends IModel {
  email: string;
  role: string;
  status: number;
}

export interface IUserModelOptions extends IModelOptions {
  status: IValueTextOption[];
  role: IValueTextOption[];
}
