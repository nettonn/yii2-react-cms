import React, { FC, Suspense } from "react";
import FullScreenLoader from "../ui/FullScreenLoader";
import { routes, routeNames } from "../../routes";
import {
  Navigate,
  Route,
  Routes,
  matchPath,
  useLocation,
} from "react-router-dom";
import { IRoute } from "../../types";
import PrivateLayout from "../layout/PrivateLayout/PrivateLayout";
import PublicLayout from "../layout/PublicLayout/PublicLayout";
import useAuth from "../../hooks/auth.hook";

const Router: FC = () => {
  const { isAuth, isAuthChecked } = useAuth();
  const { pathname } = useLocation();

  if (!isAuthChecked) {
    return <FullScreenLoader />;
  }

  const getCurrentRoute = () => {
    for (const route of routes) {
      if (matchPath(route.path, pathname)) return route;
    }
  };

  const getLayout = () => {
    const route = getCurrentRoute();

    if (!route) return PublicLayout;

    if (route.layout) return route.layout;
    if (route.isPublic) return PublicLayout;
    return PrivateLayout;
  };

  const createElement = (route: IRoute) => {
    if (isAuth && route.hideIfAuth) {
      return <Navigate to={routeNames.home} replace={true} />;
    }

    if (!isAuth && !route.isPublic) {
      return (
        <Navigate to={routeNames.login} state={{ returnUrl: route.path }} />
      );
    }

    return React.createElement(route.element, route.elementProps);
  };

  return React.createElement(getLayout(), {
    children: (
      <Suspense fallback={<FullScreenLoader />}>
        <Routes>
          {routes.map((route) => (
            <Route
              key={route.path}
              path={route.path}
              element={createElement(route)}
            />
          ))}
          <Route
            key="*"
            path="*"
            element={<Navigate to={routeNames.error.e404} />}
          />
        </Routes>
      </Suspense>
    ),
  });
};

export default Router;
