import React from "react";
import { SettingOutlined } from "@ant-design/icons";
const Settings = React.lazy(() => import("../pages/Setting/Settings"));
const Setting = React.lazy(() => import("../pages/Setting/Setting"));

export const settingRouteNames = {
  index: "/settings",
  create: "/settings/create",
  view: "/settings/:id",
};

export const settingRoutes = [
  { path: settingRouteNames.index, element: Settings },
  {
    path: settingRouteNames.create,
    element: Setting,
    elementProps: { key: "create" },
  },
  {
    path: settingRouteNames.view,
    element: Setting,
    elementProps: { key: "view" },
  },
];

export const settingRouteIcons = {
  [settingRouteNames.index]: SettingOutlined,
};
