import { useAppActions, useAppSelector } from "./redux";
import { authActions } from "../store/reducers/auth";
import { useEffect } from "react";
import { authService } from "../api/AuthService";

export default function useAuth() {
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

  return {
    isAuth,
    isAuthChecked,
  };
}
