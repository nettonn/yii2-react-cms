import React, { FC, Suspense } from "react";
import { Routes, Route } from "react-router-dom";
import { publicRoutes, RouteNames } from "../../routes";
import FullScreenLoader from "../ui/FullScreenLoader";
import { Navigate } from "react-router-dom";

const PublicAppRouter: FC = () => {
  return (
    <Suspense fallback={<FullScreenLoader />}>
      <Routes>
        {publicRoutes.map((route) => (
          <Route
            key={route.path}
            path={route.path}
            element={React.createElement(route.element, route.elementProps)}
          />
        ))}
        <Route key="*" path="*" element={<Navigate to={RouteNames.login} />} />
      </Routes>
    </Suspense>
  );
};

export default PublicAppRouter;
