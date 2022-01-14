import React from "react";

export interface ApiServicePagination {
  currentPage?: number;
  totalCount?: number;
  perPage?: number;
  pageCount?: number;
}

export interface ValidationError {
  field: string;
  message: string;
}

export interface RouteType {
  path: string;
  element: React.ElementType;
  elementProps?: React.Attributes;
  isPublic?: boolean;
  layout?: React.ElementType;
  hideIfAuth?: boolean;
}

export interface Model {
  id: number;
  model_class: string;
  view_url?: string;
  has_versions?: boolean;
}

export interface ModelOptions {}

export interface ImageThumbs {
  normal: string;
  original: string;
  thumb: string;
}

export interface FilterParams {
  [key: string]: string[] | number[];
}

export interface ValueTextOption {
  value: number | string;
  text: string;
}

export interface ValueStrTextOption {
  value: string;
  text: string;
}
