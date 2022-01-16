import { Model, ModelOptions, ValueTextOption } from "../types";

export interface User extends Model {
  email: string;
  role: string;
  status: boolean;
}

export interface UserModelOptions extends ModelOptions {
  status: ValueTextOption[];
  role: ValueTextOption[];
}
