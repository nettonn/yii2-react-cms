import React from "react";
import { BlockOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";
const LogsPage = React.lazy(() => import("../pages/Log/LogsPage"));
const LogPage = React.lazy(() => import("../pages/Log/LogPage"));

export const logRouteNames = {
  index: "/logs",
  update: "/logs/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/logs/:id", { ":id": id }),
};

export const logRoutes = [
  { path: logRouteNames.index, element: LogsPage },
  {
    path: logRouteNames.update,
    element: LogPage,
    elementProps: { key: "update" },
  },
];

export const logRouteIcons = {
  [logRouteNames.index]: BlockOutlined,
};
