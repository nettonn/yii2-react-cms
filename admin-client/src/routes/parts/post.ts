import React from "react";
import { FormOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
const Posts = React.lazy(() => import("../../pages/Post/PostsPage"));
const Post = React.lazy(() => import("../../pages/Post/PostPage"));

const names = {
  index: "/posts",
  create: "/posts/create",
  update: "/posts/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/posts/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: Posts },
  {
    path: names.create,
    element: Post,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: Post,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: FormOutlined,
};

const post = { names, routes, icons };

export default post;
