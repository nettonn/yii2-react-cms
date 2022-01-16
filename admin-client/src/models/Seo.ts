import { Model, ModelOptions, ValueTextOption } from "../types";

export interface Seo extends Model {
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

export interface SeoModelOptions extends ModelOptions {
  status: ValueTextOption[];
  parent: ParentOption[];
}
