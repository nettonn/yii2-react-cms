import { Model, ModelOptions } from "../types";

export interface Order extends Model {
  subject: string;
  name: string;
  phone: string;
  email: string;
  message: string;
  info: string;
  url: string;
  referrer: string;
  entrance_page: string;
  ip: string;
  user_agent: string;
  type: number;
  content: string;
  created_at: number;
  created_at_datetime: string;
  updated_at: number;
  updated_at_datetime: string;
  files_id: number[];
}

export interface OrderModelOptions extends ModelOptions {}
