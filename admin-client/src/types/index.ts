import React, { Attributes } from "react";
import { MenuClickEventHandler } from "rc-menu/lib/interface";

export interface IPaginationType {
  current: number;
  pageSize: number;
  total: number;
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

export interface IMenuItem {
  title: string;
  route: string;
  icon?: React.ReactNode;
  onClick?: MenuClickEventHandler;
}

export interface IMenuItem {
  title: string;
  route: string;
  icon?: React.ReactNode;
  onClick?: MenuClickEventHandler;
}
