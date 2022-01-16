import { Model, ModelOptions, ValueTextOption } from "../types";

export interface Queue extends Model {
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

export interface QueueModelOptions extends ModelOptions {
  channel: ValueTextOption[];
}
