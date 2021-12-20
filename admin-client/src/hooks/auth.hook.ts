import { useAppActions, useAppSelector } from "./redux";
import { authActions } from "../store/reducers/auth";
import { useEffect } from "react";
import { authService } from "../api/AuthService";

export default function useAuth() {
  const { authorize, clearAuth } = useAppActions(authActions);

  const { isAuth, isAuthChecked } = useAppSelector((state) => state.auth);

  useEffect(() => {
    if (isAuthChecked) return;
    const { isAuth, token, identity } = authService.getStorage();
    if (isAuth) {
      authorize({
        identity,
        token,
      });
    } else {
      clearAuth();
      authService.clearStorage();
    }
  }, [authorize, clearAuth, isAuthChecked]);

  return {
    isAuth,
    isAuthChecked,
  };
}
