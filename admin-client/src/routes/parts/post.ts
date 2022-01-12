import React from "react";
import { FormOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
const Posts = React.lazy(() => import("../../pages/Post/PostsPage"));
const Post = React.lazy(() => import("../../pages/Post/PostPage"));

const names = {
  index: "/post-sections/:sectionId/posts",
  create: "/post-sections/:sectionId/posts/create",
  update: "/post-sections/:sectionId/posts/:id",
  indexUrl: (sectionId?: string | number) =>
    stringReplace("/post-sections/:sectionId/posts", {
      ":sectionId": sectionId,
    }),
  createUrl: (sectionId?: string | number) =>
    stringReplace("/post-sections/:sectionId/posts/create", {
      ":sectionId": sectionId,
    }),
  updateUrl: (sectionId?: string | number, id?: string | number) =>
    stringReplace("/post-sections/:sectionId/posts/:id", {
      ":sectionId": sectionId,
      ":id": id,
    }),
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
