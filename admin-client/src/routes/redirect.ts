import React from "react";
import { SendOutlined } from "@ant-design/icons";
const Redirects = React.lazy(() => import("../pages/Redirect/Redirects"));
const Redirect = React.lazy(() => import("../pages/Redirect/Redirect"));

export const redirectRouteNames = {
  index: "/redirects",
  create: "/redirects/create",
  view: "/redirects/:id",
};

export const redirectRoutes = [
  { path: redirectRouteNames.index, element: Redirects },
  {
    path: redirectRouteNames.create,
    element: Redirect,
    elementProps: { key: "create" },
  },
  {
    path: redirectRouteNames.view,
    element: Redirect,
    elementProps: { key: "view" },
  },
];

export const redirectRouteIcons = {
  [redirectRouteNames.index]: SendOutlined,
};
