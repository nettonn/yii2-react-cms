import React, { FC } from "react";
import useAuth from "./hooks/auth.hook";
import FullScreenLoader from "./components/ui/FullScreenLoader";
import AppRoutes from "./components/AppRoutes";

const App: FC = () => {
  const { isAuthChecked } = useAuth();

  if (!isAuthChecked) {
    return <FullScreenLoader />;
  }

  return <AppRoutes />;
};

export default App;
