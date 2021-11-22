import { IModel, IModelOptions } from "../types";

export const CHUNK_TYPE_TEXT = 1;
export const CHUNK_TYPE_HTML = 2;

export interface IChunk extends IModel {
  name: string;
  key: string;
  type: number;
  content: string;
}

export interface IChunkModelOptions extends IModelOptions {
  type: { value: number; text: string }[];
}
