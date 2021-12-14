import React from "react";
import { SendOutlined } from "@ant-design/icons";
import { stringReplace } from "../../utils/functions";
const Redirects = React.lazy(
  () => import("../../pages/Redirect/RedirectsPage")
);
const Redirect = React.lazy(() => import("../../pages/Redirect/RedirectPage"));

const names = {
  index: "/redirects",
  create: "/redirects/create",
  update: "/redirects/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/redirects/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: Redirects },
  {
    path: names.create,
    element: Redirect,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: Redirect,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: SendOutlined,
};

const redirect = { names, routes, icons };

export default redirect;
