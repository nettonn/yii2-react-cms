import { Model, ModelOptions, ValueTextOption } from "../types";

export interface Menu extends Model {
  name: string;
  key: string;
  status: boolean;
}

export interface MenuModelOptions extends ModelOptions {
  status: ValueTextOption[];
}
