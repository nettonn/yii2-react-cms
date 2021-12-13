import React from "react";
import { IRoute } from "../types";
import { HomeOutlined, CalendarOutlined } from "@ant-design/icons";
import error from "./error";
import user from "./user";
import post from "./post";
import page from "./page";
import chunk from "./chunk";
import redirect from "./redirect";
import setting from "./setting";
import seo from "./seo";
import menu from "./menu";
import menuItem from "./menu-item";
import version from "./version";
import log from "./log";

const Login = React.lazy(() => import("../pages/LoginPage"));
const Home = React.lazy(() => import("../pages/HomePage"));
const Event = React.lazy(() => import("../pages/EventPage"));

export const RouteNames = {
  login: "/login",
  home: "/",
  event: "/event",

  error: error.names,
  user: user.names,
  post: post.names,
  page: page.names,
  chunk: chunk.names,
  redirect: redirect.names,
  setting: setting.names,
  seo: seo.names,
  menu: menu.names,
  menuItem: menuItem.names,
  version: version.names,
  log: log.names,
};

export const publicRoutes: IRoute[] = [
  { path: RouteNames.login, element: Login },
];

export const privateRoutes: IRoute[] = [
  { path: RouteNames.home, element: Home },
  { path: RouteNames.event, element: Event },

  ...error.routes,
  ...user.routes,
  ...post.routes,
  ...page.routes,
  ...chunk.routes,
  ...redirect.routes,
  ...setting.routes,
  ...seo.routes,
  ...menu.routes,
  ...menuItem.routes,
  ...version.routes,
  ...log.routes,
];

export const routeIcons = {
  [RouteNames.home]: HomeOutlined,
  [RouteNames.event]: CalendarOutlined,
  ...user.icons,
  ...post.icons,
  ...page.icons,
  ...chunk.icons,
  ...redirect.icons,
  ...setting.icons,
  ...seo.icons,
  ...menu.icons,
  ...menuItem.icons,
  ...version.icons,
  ...log.icons,
};
