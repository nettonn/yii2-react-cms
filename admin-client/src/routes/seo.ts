import React from "react";
import { SearchOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";
const SeoGrid = React.lazy(() => import("../pages/Seo/SeoGridPage"));
const Seo = React.lazy(() => import("../pages/Seo/SeoPage"));

const names = {
  index: "/seo",
  create: "/seo/create",
  update: "/seo/:id",
  updateUrl: (id?: string | number) => stringReplace("/seo/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: SeoGrid },
  {
    path: names.create,
    element: Seo,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: Seo,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: SearchOutlined,
};

const all = { names, routes, icons };

export default all;
