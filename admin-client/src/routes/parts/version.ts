import React from "react";
import { HistoryOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
const VersionsPage = React.lazy(
  () => import("../../pages/Version/VersionsPage")
);
const VersionPage = React.lazy(() => import("../../pages/Version/VersionPage"));

const names = {
  index: "/versions",
  view: "/versions/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/versions/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: VersionsPage },
  {
    path: names.view,
    element: VersionPage,
    elementProps: { key: "view" },
  },
];

const icons = {
  [names.index]: HistoryOutlined,
};

const version = { names, routes, icons };

export default version;
