import React from "react";
import { SendOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";
const Redirects = React.lazy(() => import("../pages/Redirect/RedirectsPage"));
const Redirect = React.lazy(() => import("../pages/Redirect/RedirectPage"));

export const redirectRouteNames = {
  index: "/redirects",
  create: "/redirects/create",
  update: "/redirects/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/redirects/:id", { ":id": id }),
};

export const redirectRoutes = [
  { path: redirectRouteNames.index, element: Redirects },
  {
    path: redirectRouteNames.create,
    element: Redirect,
    elementProps: { key: "create" },
  },
  {
    path: redirectRouteNames.update,
    element: Redirect,
    elementProps: { key: "update" },
  },
];

export const redirectRouteIcons = {
  [redirectRouteNames.index]: SendOutlined,
};
