import React from "react";
import { IRoute } from "../types";
import { HomeOutlined, CalendarOutlined } from "@ant-design/icons";
import { errorRouteNames, errorRoutes } from "./error";

import { userRouteIcons, userRouteNames, userRoutes } from "./user";
import { postRouteIcons, postRouteNames, postRoutes } from "./post";
import { pageRouteIcons, pageRouteNames, pageRoutes } from "./page";
import { chunkRouteIcons, chunkRouteNames, chunkRoutes } from "./chunk";
import {
  redirectRouteIcons,
  redirectRouteNames,
  redirectRoutes,
} from "./redirect";
import { settingRouteIcons, settingRouteNames, settingRoutes } from "./setting";
import { seoRouteIcons, seoRouteNames, seoRoutes } from "./seo";
import { menuRouteIcons, menuRouteNames, menuRoutes } from "./menu";
import {
  menuItemRouteIcons,
  menuItemRouteNames,
  menuItemRoutes,
} from "./menu-item";
import { versionRouteIcons, versionRouteNames, versionRoutes } from "./version";
import { logRouteIcons, logRouteNames, logRoutes } from "./log";

const Login = React.lazy(() => import("../pages/LoginPage"));
const Home = React.lazy(() => import("../pages/HomePage"));
const Event = React.lazy(() => import("../pages/EventPage"));

export const RouteNames = {
  login: "/login",
  home: "/",
  event: "/event",

  error: errorRouteNames,
  user: userRouteNames,
  post: postRouteNames,
  page: pageRouteNames,
  chunk: chunkRouteNames,
  redirect: redirectRouteNames,
  setting: settingRouteNames,
  seo: seoRouteNames,
  menu: menuRouteNames,
  menuItem: menuItemRouteNames,
  version: versionRouteNames,
  log: logRouteNames,
};

export const publicRoutes: IRoute[] = [
  { path: RouteNames.login, element: Login },
];

export const privateRoutes: IRoute[] = [
  { path: RouteNames.home, element: Home },
  { path: RouteNames.event, element: Event },

  ...errorRoutes,
  ...userRoutes,
  ...postRoutes,
  ...pageRoutes,
  ...chunkRoutes,
  ...redirectRoutes,
  ...settingRoutes,
  ...seoRoutes,
  ...menuRoutes,
  ...menuItemRoutes,
  ...versionRoutes,
  ...logRoutes,
];

export const routeIcons = {
  [RouteNames.home]: HomeOutlined,
  [RouteNames.event]: CalendarOutlined,
  ...userRouteIcons,
  ...postRouteIcons,
  ...pageRouteIcons,
  ...chunkRouteIcons,
  ...redirectRouteIcons,
  ...settingRouteIcons,
  ...seoRouteIcons,
  ...menuRouteIcons,
  ...menuItemRouteIcons,
  ...versionRouteIcons,
  ...logRouteIcons,
};
