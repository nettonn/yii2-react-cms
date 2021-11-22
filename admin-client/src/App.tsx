import "antd/dist/antd.css";
import React, { FC, useEffect } from "react";
import PublicLayout from "./components/layout/PublicLayout/PublicLayout";
import PrivateLayout from "./components/layout/PrivateLayout/PrivateLayout";
import FullScreenLoader from "./components/ui/FullScreenLoader";
import { useAppActions, useAppSelector } from "./hooks/redux";
import { authActions } from "./store/reducers/auth";
import { authService } from "./api/AuthService";

const App: FC = () => {
  const { authorize, clearAuth } = useAppActions(authActions);

  const { isAuth, isAuthChecked } = useAppSelector((state) => state.auth);

  useEffect(() => {
    if (authService.getAuth()) {
      authorize({
        identity: authService.getIdentity(),
        token: authService.getToken(),
      });
    } else {
      clearAuth();
    }
  }, [authorize, clearAuth]);

  if (!isAuthChecked) {
    return <FullScreenLoader />;
  }

  if (!isAuth) {
    return <PublicLayout />;
  }

  return <PrivateLayout />;
};

export default App;
