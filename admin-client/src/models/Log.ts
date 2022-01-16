import { Model, ModelOptions, ValueTextOption } from "../types";

export interface Log extends Model {
  name: string;
  url: string;
  messages: string;
  created_at: number;
  created_at_datetime: string;
}

export interface LogModelOptions extends ModelOptions {
  name: ValueTextOption[];
}
