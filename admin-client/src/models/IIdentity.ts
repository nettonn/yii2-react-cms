import { IModel } from "../types";

export interface IIdentity extends IModel {
  email: string;
  role: string;
}
