import React, { FC } from "react";
import Sidebar from "../Sidebar/Sidebar";
import { Layout } from "antd";
import "./PrivateLayout.css";

const PrivateLayout: FC = (props) => {
  return (
    <Layout className="app-private-layout">
      <Sidebar />
      <Layout className="app-content-layout">
        <Layout.Content className="app-content">
          {props.children}
        </Layout.Content>
      </Layout>
    </Layout>
  );
};

export default PrivateLayout;
