import React from "react";
import { FormOutlined } from "@ant-design/icons";
const Posts = React.lazy(() => import("../pages/Post/Posts"));
const Post = React.lazy(() => import("../pages/Post/Post"));

export const postRouteNames = {
  index: "/posts",
  create: "/posts/create",
  view: "/posts/:id",
};

export const postRoutes = [
  { path: postRouteNames.index, element: Posts },
  {
    path: postRouteNames.create,
    element: Post,
    elementProps: { key: "create" },
  },
  { path: postRouteNames.view, element: Post, elementProps: { key: "view" } },
];

export const postRouteIcons = {
  [postRouteNames.index]: FormOutlined,
};
