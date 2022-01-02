import React from "react";
import { CodeOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
const Chunks = React.lazy(() => import("../../pages/Chunk/ChunksPage"));
const Chunk = React.lazy(() => import("../../pages/Chunk/ChunkPage"));

const names = {
  index: "/chunks",
  create: "/chunks/create",
  update: "/chunks/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/chunks/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: Chunks },
  {
    path: names.create,
    element: Chunk,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: Chunk,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: CodeOutlined,
};

const chunk = { names, routes, icons };

export default chunk;
