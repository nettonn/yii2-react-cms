import "antd/dist/antd.css";
import React, { FC } from "react";
import PublicLayout from "./components/layout/PublicLayout/PublicLayout";
import PrivateLayout from "./components/layout/PrivateLayout/PrivateLayout";
import FullScreenLoader from "./components/ui/FullScreenLoader";
import useAuth from "./hooks/auth.hook";

const App: FC = () => {
  const { isAuth, isAuthChecked } = useAuth();

  if (!isAuthChecked) {
    return <FullScreenLoader />;
  }

  if (!isAuth) {
    return <PublicLayout />;
  }

  return <PrivateLayout />;
};

export default App;
