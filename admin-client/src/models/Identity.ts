import { Model } from "../types";

export interface Identity extends Model {
  email: string;
  role: string;
}
