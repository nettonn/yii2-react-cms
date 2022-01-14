import React, { FC, Suspense } from "react";
import { RouteType } from "../types";
import { Navigate, Route, Routes } from "react-router-dom";
import { routeNames, routes } from "../routes";
import withAuth from "../hoc/auth";
import withAuthHide from "../hoc/authHide";
import PublicLayout from "./layout/PublicLayout";
import PrivateLayout from "./layout/PrivateLayout";
import FullScreenLoader from "./ui/FullScreenLoader";

interface LayoutRoute {
  element: React.ElementType;
  routes: RouteType[];
}

const AppRoutes: FC = () => {
  const layoutRoutes: LayoutRoute[] = [];

  const getLayout = (route: RouteType) => {
    if (!route) return PublicLayout;

    if (route.layout) return route.layout;
    if (route.isPublic) return PublicLayout;
    return PrivateLayout;
  };

  for (const route of routes) {
    const layout = getLayout(route);
    const index = layoutRoutes.findIndex((i) => i.element === layout);
    if (-1 === index) {
      layoutRoutes.push({
        element: layout,
        routes: [route],
      } as LayoutRoute);
    } else {
      layoutRoutes[index].routes.push(route);
    }
  }

  const createRouteElement = (route: RouteType) => {
    const createElement = () => (
      <Suspense fallback={<FullScreenLoader />}>
        {React.createElement(route.element, route.elementProps)}
      </Suspense>
    );

    if (route.hideIfAuth) {
      return React.createElement(withAuthHide(createElement()));
    }

    if (!route.isPublic) {
      return React.createElement(withAuth(createElement()));
    }

    return createElement();
  };

  return (
    <Routes>
      {layoutRoutes.map((layoutRoute, index) => (
        <Route key={index} element={React.createElement(layoutRoute.element)}>
          {layoutRoute.routes.map((route) => (
            <Route
              key={route.path}
              path={route.path}
              element={createRouteElement(route)}
            />
          ))}
        </Route>
      ))}
      <Route
        key="*"
        path="*"
        element={<Navigate to={routeNames.error.e404} replace={true} />}
      />
    </Routes>
  );
};

export default React.memo(AppRoutes);
