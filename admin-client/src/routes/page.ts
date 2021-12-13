import React from "react";
import { FormOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";
const Pages = React.lazy(() => import("../pages/Page/Pages"));
const Page = React.lazy(() => import("../pages/Page/Page"));

const names = {
  index: "/pages",
  create: "/pages/create",
  update: "/pages/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/pages/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: Pages },
  {
    path: names.create,
    element: Page,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: Page,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: FormOutlined,
};

const all = { names, routes, icons };

export default all;
