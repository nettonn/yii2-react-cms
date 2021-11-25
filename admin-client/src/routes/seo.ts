import React from "react";
import { FormOutlined } from "@ant-design/icons";
const SeoGrid = React.lazy(() => import("../pages/Seo/SeoGrid"));
const Seo = React.lazy(() => import("../pages/Seo/Seo"));

export const seoRouteNames = {
  index: "/seo",
  create: "/seo/create",
  update: "/seo/:id",
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
  [seoRouteNames.index]: FormOutlined,
};
