import { IModel, IModelOptions } from "../types";

export interface IUser extends IModel {
  email: string;
  role: string;
  status: number;
}

export interface IUserModelOptions extends IModelOptions {
  status: { value: string | number; text: string }[];
  role: { value: string | number; text: string }[];
}
