import React, { FC } from "react";
import { Row, Spin } from "antd";

const FullScreenLoader: FC = () => {
  return (
    <Row justify="center" align="middle" style={{ height: "100vh" }}>
      <Spin size={"large"} />
    </Row>
  );
};

export default FullScreenLoader;
