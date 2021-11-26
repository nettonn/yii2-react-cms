import React from "react";
import { FormOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";
const Pages = React.lazy(() => import("../pages/Page/Pages"));
const Page = React.lazy(() => import("../pages/Page/Page"));

export const pageRouteNames = {
  index: "/pages",
  create: "/pages/create",
  update: "/pages/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/pages/:id", { ":id": id }),
};

export const pageRoutes = [
  { path: pageRouteNames.index, element: Pages },
  {
    path: pageRouteNames.create,
    element: Page,
    elementProps: { key: "create" },
  },
  {
    path: pageRouteNames.update,
    element: Page,
    elementProps: { key: "update" },
  },
];

export const pageRouteIcons = {
  [pageRouteNames.index]: FormOutlined,
};
