import React from "react";
const Error403 = React.lazy(() => import("../pages/Error/Error403Page"));
const Error404 = React.lazy(() => import("../pages/Error/Error404Page"));
const Error500 = React.lazy(() => import("../pages/Error/Error500Page"));

export const errorRouteNames = {
  e403: "/error403",
  e404: "/error404",
  e500: "/error500",
};

export const errorRoutes = [
  { path: errorRouteNames.e403, element: Error403 },
  { path: errorRouteNames.e404, element: Error404 },
  { path: errorRouteNames.e500, element: Error500 },
];
