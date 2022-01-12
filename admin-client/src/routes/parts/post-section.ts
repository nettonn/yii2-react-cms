import React from "react";
import { FormOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
import { IRoute } from "../../types";
const PostSectionsPage = React.lazy(
  () => import("../../pages/Post/PostSectionsPage")
);
const PostSectionPage = React.lazy(
  () => import("../../pages/Post/PostSectionPage")
);

const names = {
  index: "/post-sections",
  create: "/post-sections/create",
  update: "/post-sections/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/post-sections/:id", { ":id": id }),
};

const routes: IRoute[] = [
  { path: names.index, element: PostSectionsPage },
  {
    path: names.create,
    element: PostSectionPage,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: PostSectionPage,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: FormOutlined,
};

const postSections = { names, routes, icons };

export default postSections;
