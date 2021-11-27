import React from "react";
import { UserOutlined } from "@ant-design/icons";
import { stringReplace } from "../utils/functions";

const Users = React.lazy(() => import("../pages/User/UsersPage"));
const User = React.lazy(() => import("../pages/User/UserPage"));

export const userRouteNames = {
  index: "/users",
  create: "/users/create",
  update: "/users/:id",
  updateUrl: (id?: string | number) =>
    stringReplace("/users/:id", { ":id": id }),
};

export const userRoutes = [
  { path: userRouteNames.index, element: Users },
  {
    path: userRouteNames.create,
    element: User,
    elementProps: { key: "create" },
  },
  {
    path: userRouteNames.update,
    element: User,
    elementProps: { key: "update" },
  },
];

export const userRouteIcons = {
  [userRouteNames.index]: UserOutlined,
};
