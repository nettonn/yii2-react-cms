import React, { FC } from "react";
import Sidebar from "./Sidebar";
import { Layout } from "antd";
import { Outlet } from "react-router-dom";

const PrivateLayout: FC = (props) => {
  return (
    <Layout className="app-private-layout">
      <Sidebar />
      <Layout className="app-content-layout">
        <Layout.Content className="app-content">
          <Outlet />
        </Layout.Content>
      </Layout>
    </Layout>
  );
};

export default PrivateLayout;
