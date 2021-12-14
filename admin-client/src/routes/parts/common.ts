import React from "react";
import { HomeOutlined, CalendarOutlined } from "@ant-design/icons";

const Login = React.lazy(() => import("../../pages/LoginPage"));
const Home = React.lazy(() => import("../../pages/HomePage"));
const Event = React.lazy(() => import("../../pages/EventPage"));

const names = {
  login: "/login",
  home: "/",
  event: "/event",
};

const publicRoutes = [{ path: names.login, element: Login }];

const routes = [
  { path: names.home, element: Home },
  { path: names.event, element: Event },
];

const icons = {
  [names.home]: HomeOutlined,
  [names.event]: CalendarOutlined,
};

const common = { names, publicRoutes, routes, icons };

export default common;
