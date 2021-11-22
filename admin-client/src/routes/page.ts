import React from "react";
import { FormOutlined } from "@ant-design/icons";
const Pages = React.lazy(() => import("../pages/Page/Pages"));
const Page = React.lazy(() => import("../pages/Page/Page"));

export const pageRouteNames = {
  index: "/pages",
  create: "/pages/create",
  view: "/pages/:id",
};

export const pageRoutes = [
  { path: pageRouteNames.index, element: Pages },
  {
    path: pageRouteNames.create,
    element: Page,
    elementProps: { key: "create" },
  },
  { path: pageRouteNames.view, element: Page, elementProps: { key: "view" } },
];

export const pageRouteIcons = {
  [pageRouteNames.index]: FormOutlined,
};
