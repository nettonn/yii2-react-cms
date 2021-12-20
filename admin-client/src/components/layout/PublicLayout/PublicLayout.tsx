import React, { FC } from "react";
import { Layout } from "antd";

const PublicLayout: FC = (props) => {
  return (
    <Layout>
      <Layout.Content style={{ padding: "0 15px", minHeight: "100vh" }}>
        {props.children}
      </Layout.Content>
    </Layout>
  );
};

export default PublicLayout;
