import React from "react";
import { FormOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";
const MenuItemsPage = React.lazy(() => import("../pages/Menu/MenuItemsPage"));
const MenuItemPage = React.lazy(() => import("../pages/Menu/MenuItemPage"));

export const menuItemRouteNames = {
  index: "/menu/:menuId/items",
  create: "/menu/:menuId/items/create",
  update: "/menu/:menuId/items/:id",
  indexUrl: (menuId?: string | number) =>
    stringReplace("/menu/:menuId/items", { ":menuId": menuId }),
  createUrl: (menuId?: string | number) =>
    stringReplace("/menu/:menuId/items/create", { ":menuId": menuId }),
  updateUrl: (menuId?: string | number, id?: string | number) =>
    stringReplace("/menu/:menuId/items/:id", { ":menuId": menuId, ":id": id }),
};

export const menuItemRoutes = [
  { path: menuItemRouteNames.index, element: MenuItemsPage },
  {
    path: menuItemRouteNames.create,
    element: MenuItemPage,
    elementProps: { key: "create" },
  },
  {
    path: menuItemRouteNames.update,
    element: MenuItemPage,
    elementProps: { key: "update" },
  },
];

export const menuItemRouteIcons = {
  [menuItemRouteNames.index]: FormOutlined,
};
