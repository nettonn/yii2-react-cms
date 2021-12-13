import React from "react";
const Error403 = React.lazy(() => import("../pages/Error/Error403Page"));
const Error404 = React.lazy(() => import("../pages/Error/Error404Page"));
const Error500 = React.lazy(() => import("../pages/Error/Error500Page"));

const names = {
  e403: "/error403",
  e404: "/error404",
  e500: "/error500",
};

const routes = [
  { path: names.e403, element: Error403 },
  { path: names.e404, element: Error404 },
  { path: names.e500, element: Error500 },
];

const all = { names, routes };

export default all;
