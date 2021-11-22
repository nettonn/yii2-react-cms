import React from "react";
import { BlockOutlined } from "@ant-design/icons";
const Chunks = React.lazy(() => import("../pages/Chunk/Chunks"));
const Chunk = React.lazy(() => import("../pages/Chunk/Chunk"));

export const chunkRouteNames = {
  index: "/chunks",
  create: "/chunks/create",
  view: "/chunks/:id",
};

export const chunkRoutes = [
  { path: chunkRouteNames.index, element: Chunks },
  {
    path: chunkRouteNames.create,
    element: Chunk,
    elementProps: { key: "create" },
  },
  { path: chunkRouteNames.view, element: Chunk, elementProps: { key: "view" } },
];

export const chunkRouteIcons = {
  [chunkRouteNames.index]: BlockOutlined,
};
