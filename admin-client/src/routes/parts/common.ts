import React from "react";
import { HomeOutlined } from "@ant-design/icons";
import { IRoute } from "../../types";

const Login = React.lazy(() => import("../../pages/LoginPage"));
const Home = React.lazy(() => import("../../pages/HomePage"));

const names = {
  login: "/login",
  home: "/",
};

const routes: IRoute[] = [
  { path: names.login, element: Login, isPublic: true, hideIfAuth: true },
  { path: names.home, element: Home },
];

const icons = {
  [names.home]: HomeOutlined,
};

const common = { names, routes, icons };

export default common;
