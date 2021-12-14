import { IModel, IModelOptions, IValueTextOption } from "../types";

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
  status: IValueTextOption[];
  parent: ParentOption[];
}
