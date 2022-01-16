import { Model, ModelOptions, ValueTextOption } from "../types";

export interface Redirect extends Model {
  from: string;
  to: string;
  code: number;
  sort: number;
  status: boolean;
}

export interface RedirectModelOptions extends ModelOptions {
  status: ValueTextOption[];
}
