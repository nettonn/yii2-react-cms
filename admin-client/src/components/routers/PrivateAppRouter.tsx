import React, { FC, Suspense } from "react";
import { Navigate, Route, Routes } from "react-router-dom";
import { privateRoutes, RouteNames } from "../../routes";
import FullScreenLoader from "../ui/FullScreenLoader";

const PrivateAppRouter: FC = () => {
  return (
    <Suspense fallback={<FullScreenLoader />}>
      <Routes>
        {privateRoutes.map((route) => (
          <Route
            key={route.path}
            path={route.path}
            element={React.createElement(route.element, route.elementProps)}
          />
        ))}
        <Route key="*" path="*" element={<Navigate to={RouteNames.home} />} />
      </Routes>
    </Suspense>
  );
};

export default PrivateAppRouter;
