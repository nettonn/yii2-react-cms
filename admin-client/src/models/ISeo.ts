import { IModel, IModelOptions } from "../types";

export interface ISeo extends IModel {
  name: string;
  parent_id: number | null;
  top_content: string;
  bottom_content: string;
  status: boolean;
  title: string;
  h1: string;
  description: string;
  keywords: string;
}

interface ParentOption {
  key: string | number;
  title: string;
  value: string | number;
  children?: ParentOption[];
}

export interface ISeoModelOptions extends IModelOptions {
  status: { value: string | number; text: string }[];
  parent: ParentOption[];
}
