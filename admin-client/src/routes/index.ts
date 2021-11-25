import React from "react";
import { IRoute } from "../types";
import {
  HomeOutlined,
  CalendarOutlined,
  LogoutOutlined,
} from "@ant-design/icons";
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

const Login = React.lazy(() => import("../pages/Login"));
const Home = React.lazy(() => import("../pages/Home"));
const Event = React.lazy(() => import("../pages/Event"));

export const RouteNames = {
  login: "/login",
  logout: "/logout",
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
];

export const routeIcons = {
  [RouteNames.home]: HomeOutlined,
  [RouteNames.event]: CalendarOutlined,
  [RouteNames.logout]: LogoutOutlined,
  ...userRouteIcons,
  ...postRouteIcons,
  ...pageRouteIcons,
  ...chunkRouteIcons,
  ...redirectRouteIcons,
  ...settingRouteIcons,
  ...seoRouteIcons,
};
