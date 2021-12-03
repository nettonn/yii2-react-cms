import React from "react";
import { HistoryOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";
const VersionsPage = React.lazy(() => import("../pages/Version/VersionsPage"));
const VersionPage = React.lazy(() => import("../pages/Version/VersionPage"));

export const versionRouteNames = {
  index: "/versions",
  view: "/versions/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/versions/:id", { ":id": id }),
};

export const versionRoutes = [
  { path: versionRouteNames.index, element: VersionsPage },
  {
    path: versionRouteNames.view,
    element: VersionPage,
    elementProps: { key: "view" },
  },
];

export const versionRouteIcons = {
  [versionRouteNames.index]: HistoryOutlined,
};
