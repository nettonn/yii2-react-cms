import { IModel } from "../types";

export interface IEvent extends IModel {
  author: number;
  date: string;
  description: string;
  guests: number[];
}
