import React from "react";
import { UserOutlined } from "@ant-design/icons";

const Users = React.lazy(() => import("../pages/User/Users"));
const User = React.lazy(() => import("../pages/User/User"));

export const userRouteNames = {
  index: "/users",
  create: "/users/create",
  view: "/users/:id",
};

export const userRoutes = [
  { path: userRouteNames.index, element: Users },
  {
    path: userRouteNames.create,
    element: User,
    elementProps: { key: "create" },
  },
  {
    path: userRouteNames.view,
    element: User,
    elementProps: { key: "view" },
  },
];

export const userRouteIcons = {
  [userRouteNames.index]: UserOutlined,
};
