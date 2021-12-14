import React from "react";
import { SettingOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
const Settings = React.lazy(() => import("../../pages/Setting/SettingsPage"));
const Setting = React.lazy(() => import("../../pages/Setting/SettingPage"));

const names = {
  index: "/settings",
  create: "/settings/create",
  update: "/settings/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/settings/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: Settings },
  {
    path: names.create,
    element: Setting,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: Setting,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: SettingOutlined,
};

const setting = { names, routes, icons };

export default setting;
