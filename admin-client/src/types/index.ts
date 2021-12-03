import React, { Attributes } from "react";

export interface IPaginationType {
  current: number;
  pageSize: number;
  total: number;
}

export interface IApiServiceReturn<T> {
  success?: boolean;
  data?: T;
  status?: number;
  error?: string;
  pagination?: IApiServicePagination;
  validationErrors?: IValidationErrorType[];
}

export interface IApiServicePagination {
  currentPage?: number;
  totalCount?: number;
  perPage?: number;
  pageCount?: number;
}

export interface IValidationErrorType {
  field: string;
  message: string;
}

export interface ISortType {
  field: string | null;
  direction: string | null;
}

export interface IDataTableParamsType {
  page: number;
  sortField: string | null;
  sortDirection: string | null;
  search: string;
}

export interface IRoute {
  path: string;
  element: React.ComponentType;
  // exact?: boolean;
  elementProps?: Attributes;
}

export interface IModelRouteNames {
  index: string;
  create: string;
  view: string;
}

export interface IModel {
  id: number;
  view_url?: string;
}

export interface IModelOptions {}

export interface IImageThumbs {
  normal: string;
  original: string;
  thumb: string;
}

export interface IFiltersParam {
  [key: string]: string[] | number[];
}

export interface IValueTextOption {
  value: number;
  text: string;
}
