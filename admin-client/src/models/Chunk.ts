import { Model, ModelOptions, ValueTextOption } from "../types";

export const CHUNK_TYPE_TEXT = 1;
export const CHUNK_TYPE_HTML = 2;

export interface Chunk extends Model {
  name: string;
  key: string;
  type: number;
  type_label: string;
  content: string;
}

export interface ChunkModelOptions extends ModelOptions {
  type: ValueTextOption[];
}
