import React from "react";
import { MenuOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
import menu from "./menu";
const MenuItemsPage = React.lazy(
  () => import("../../pages/Menu/MenuItemsPage")
);
const MenuItemPage = React.lazy(() => import("../../pages/Menu/MenuItemPage"));

const names = {
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

const routes = [
  { path: names.index, element: MenuItemsPage },
  {
    path: names.create,
    element: MenuItemPage,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: MenuItemPage,
    elementProps: { key: "update" },
  },
];

const icons = {
  [menu.names.index]: MenuOutlined,
};

const menuItem = { names, routes, icons };

export default menuItem;
