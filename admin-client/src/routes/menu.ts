import React from "react";
import { FormOutlined } from "@ant-design/icons";
const MenuGridPage = React.lazy(() => import("../pages/Menu/MenuGridPage"));
const MenuPage = React.lazy(() => import("../pages/Menu/MenuPage"));

export const menuRouteNames = {
  index: "/menu",
  create: "/menu/create",
  update: "/menu/:id",
};

export const menuRoutes = [
  { path: menuRouteNames.index, element: MenuGridPage },
  {
    path: menuRouteNames.create,
    element: MenuPage,
    elementProps: { key: "create" },
  },
  {
    path: menuRouteNames.update,
    element: MenuPage,
    elementProps: { key: "update" },
  },
];

export const menuRouteIcons = {
  [menuRouteNames.index]: FormOutlined,
};
