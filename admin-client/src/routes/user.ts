import React from "react";
import { UserOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";

const Users = React.lazy(() => import("../pages/User/UsersPage"));
const User = React.lazy(() => import("../pages/User/UserPage"));

const names = {
  index: "/users",
  create: "/users/create",
  update: "/users/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/users/:id", { ":id": id }),
};

const routes = [
  { path: names.index, element: Users },
  {
    path: names.create,
    element: User,
    elementProps: { key: "create" },
  },
  {
    path: names.update,
    element: User,
    elementProps: { key: "update" },
  },
];

const icons = {
  [names.index]: UserOutlined,
};

const all = { names, routes, icons };

export default all;
