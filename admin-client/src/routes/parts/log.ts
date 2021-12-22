import React from "react";
import { ExceptionOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
const LogsPage = React.lazy(() => import("../../pages/Log/LogsPage"));
const LogPage = React.lazy(() => import("../../pages/Log/LogPage"));

const names = {
  index: "/logs",
  update: "/logs/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/logs/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: LogsPage },
  {
    path: names.update,
    element: LogPage,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: ExceptionOutlined,
};

const log = { names, routes, icons };

export default log;
