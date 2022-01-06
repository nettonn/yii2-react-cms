import { IModel, IModelOptions, IValueTextOption } from "../types";

export const CHUNK_TYPE_TEXT = 1;
export const CHUNK_TYPE_HTML = 2;

export interface IChunk extends IModel {
  name: string;
  key: string;
  type: number;
  type_label: string;
  content: string;
}

export interface IChunkModelOptions extends IModelOptions {
  type: IValueTextOption[];
}
