import React from "react";
import { SettingOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";
const Settings = React.lazy(() => import("../pages/Setting/Settings"));
const Setting = React.lazy(() => import("../pages/Setting/Setting"));

export const settingRouteNames = {
  index: "/settings",
  create: "/settings/create",
  update: "/settings/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/settings/:id", { ":id": id }),
};

export const settingRoutes = [
  { path: settingRouteNames.index, element: Settings },
  {
    path: settingRouteNames.create,
    element: Setting,
    elementProps: { key: "create" },
  },
  {
    path: settingRouteNames.update,
    element: Setting,
    elementProps: { key: "update" },
  },
];

export const settingRouteIcons = {
  [settingRouteNames.index]: SettingOutlined,
};
