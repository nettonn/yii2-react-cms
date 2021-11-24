import React from "react";
import { UserOutlined } from "@ant-design/icons";

const Users = React.lazy(() => import("../pages/User/Users"));
const User = React.lazy(() => import("../pages/User/User"));

export const userRouteNames = {
  index: "/users",
  create: "/users/create",
  update: "/users/:id",
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
