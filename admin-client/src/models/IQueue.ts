import { IModel, IModelOptions, IValueTextOption } from "../types";

export interface IQueue extends IModel {
  channel: string;
  ttr: number;
  delay: number;
  priority: number;
  attempt: number;
  job_data: string;
  pushed_at: number;
  pushed_at_datetime: string;
  reserved_at: number;
  reserved_at_datetime: string;
  done_at: number;
  done_at_datetime: string;
}

export interface IQueueModelOptions extends IModelOptions {
  channel: IValueTextOption[];
}
