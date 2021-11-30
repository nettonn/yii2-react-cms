import React from "react";
import { SearchOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";
const SeoGrid = React.lazy(() => import("../pages/Seo/SeoGridPage"));
const Seo = React.lazy(() => import("../pages/Seo/SeoPage"));

export const seoRouteNames = {
  index: "/seo",
  create: "/seo/create",
  update: "/seo/:id",
  updateUrl: (id?: string | number) => stringReplace("/seo/:id", { ":id": id }),
};

export const seoRoutes = [
  { path: seoRouteNames.index, element: SeoGrid },
  {
    path: seoRouteNames.create,
    element: Seo,
    elementProps: { key: "create" },
  },
  {
    path: seoRouteNames.update,
    element: Seo,
    elementProps: { key: "update" },
  },
];

export const seoRouteIcons = {
  [seoRouteNames.index]: SearchOutlined,
};
