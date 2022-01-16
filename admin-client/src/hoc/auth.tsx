import React, { ReactElement } from "react";
import { Navigate, useLocation } from "react-router-dom";
import useAuth from "../hooks/auth.hook";
import { routeNames } from "../routes";
import { queryStringStringify } from "../utils/qs";

export default function withAuth(Component: ReactElement) {
  return () => {
    const { isAuth, isAuthChecked } = useAuth();
    const { pathname } = useLocation();

    if (!isAuthChecked) return null;

    if (!isAuth)
      return (
        <Navigate
          to={`${routeNames.login}?${queryStringStringify({
            return: pathname,
          })}`}
          replace={true}
        />
      );

    return <>{Component}</>;
  };
}
