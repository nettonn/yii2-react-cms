import React from "react";
import { MenuOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
const MenusPage = React.lazy(() => import("../../pages/Menu/MenusPage"));
const MenuPage = React.lazy(() => import("../../pages/Menu/MenuPage"));

const names = {
  index: "/menu",
  create: "/menu/create",
  update: "/menu/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/menu/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: MenusPage },
  {
    path: names.create,
    element: MenuPage,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: MenuPage,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: MenuOutlined,
};

const menu = { names, routes, icons };

export default menu;
