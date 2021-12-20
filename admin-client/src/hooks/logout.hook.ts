import { useMutation } from "react-query";
import { authService } from "../api/AuthService";
import { useAppActions } from "./redux";
import { authActions } from "../store/reducers/auth";

export default function useLogout() {
  const { clearAuth } = useAppActions(authActions);
  const { isLoading, mutate: logout } = useMutation(async () => {
    await authService.logout();
    authService.clearStorage();
    clearAuth();
  });

  return {
    isLoading,
    logout,
  };
}
