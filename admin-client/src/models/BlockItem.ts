import { Model, ModelOptions, ValueTextOption } from "../types";

export interface BlockItem extends Model {
  name: string;
  type: string;
  block_id: number;
  sort: number;
  status: boolean;
  created_at: number;
  created_at_date: string;
  updated_at: number;
  updated_at_date: string;
}

export interface BlockItemModelOptions extends ModelOptions {
  status: ValueTextOption[];
}
