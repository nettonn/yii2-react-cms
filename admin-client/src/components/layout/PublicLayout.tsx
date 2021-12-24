import React, { FC } from "react";
import { Layout } from "antd";
import { Outlet } from "react-router-dom";

const PublicLayout: FC = (props) => {
  return (
    <Layout>
      <Layout.Content style={{ padding: "0 15px", minHeight: "100vh" }}>
        <Outlet />
      </Layout.Content>
    </Layout>
  );
};

export default PublicLayout;
