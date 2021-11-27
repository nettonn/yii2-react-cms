import React from "react";
import { FormOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";
const Posts = React.lazy(() => import("../pages/Post/PostsPage"));
const Post = React.lazy(() => import("../pages/Post/PostPage"));

export const postRouteNames = {
  index: "/posts",
  create: "/posts/create",
  update: "/posts/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/posts/:id", { ":id": id }),
};

export const postRoutes = [
  { path: postRouteNames.index, element: Posts },
  {
    path: postRouteNames.create,
    element: Post,
    elementProps: { key: "create" },
  },
  {
    path: postRouteNames.update,
    element: Post,
    elementProps: { key: "update" },
  },
];

export const postRouteIcons = {
  [postRouteNames.index]: FormOutlined,
};
