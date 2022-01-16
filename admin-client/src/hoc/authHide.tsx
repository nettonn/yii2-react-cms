import React, { ReactElement } from "react";
import { Navigate } from "react-router-dom";
import useAuth from "../hooks/auth.hook";
import { routeNames } from "../routes";

export default function withAuthHide(Component: ReactElement) {
  return () => {
    const { isAuth, isAuthChecked } = useAuth();

    if (!isAuthChecked) return null;

    if (isAuth) return <Navigate to={routeNames.home} replace={true} />;

    return <>{Component}</>;
  };
}
