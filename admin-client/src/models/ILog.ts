import { IModel, IModelOptions, IValueTextOption } from "../types";

export interface ILog extends IModel {
  name: string;
  url: string;
  messages: string;
  created_at: number;
  created_at_datetime: string;
}

export interface ILogModelOptions extends IModelOptions {
  name: IValueTextOption[];
}
