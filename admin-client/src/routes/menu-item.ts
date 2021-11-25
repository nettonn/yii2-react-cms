import React from "react";
import { FormOutlined } from "@ant-design/icons";
const MenuItemsPage = React.lazy(() => import("../pages/Menu/MenuItemsPage"));
const MenuItemPage = React.lazy(() => import("../pages/Menu/MenuItemPage"));

export const menuItemRouteNames = {
  index: "/menu/:menuId/items",
  create: "/menu/:menuId/items/create",
  update: "/menu/:menuId/items/:id",
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
