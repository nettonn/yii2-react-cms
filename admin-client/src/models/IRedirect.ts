import { IModel, IModelOptions } from "../types";

export interface IRedirect extends IModel {
  from: string;
  to: string;
  code: number;
  sort: number;
  status: boolean;
}

export interface IRedirectModelOptions extends IModelOptions {}
