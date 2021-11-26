import React from "react";
import { BlockOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";
const Chunks = React.lazy(() => import("../pages/Chunk/Chunks"));
const Chunk = React.lazy(() => import("../pages/Chunk/Chunk"));

export const chunkRouteNames = {
  index: "/chunks",
  create: "/chunks/create",
  update: "/chunks/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/chunks/:id", { ":id": id }),
};

export const chunkRoutes = [
  { path: chunkRouteNames.index, element: Chunks },
  {
    path: chunkRouteNames.create,
    element: Chunk,
    elementProps: { key: "create" },
  },
  {
    path: chunkRouteNames.update,
    element: Chunk,
    elementProps: { key: "update" },
  },
];

export const chunkRouteIcons = {
  [chunkRouteNames.index]: BlockOutlined,
};
