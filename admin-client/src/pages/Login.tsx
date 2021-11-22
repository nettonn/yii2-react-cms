import React, { FC } from "react";
import { Card, Col, Row } from "antd";
import LoginForm from "../components/auth/LoginForm";

const Login: FC = () => {
  return (
    <Row justify="center" align="middle" style={{ height: "100vh" }}>
      <Col xs={24} sm={16} md={8} lg={6}>
        <Card>
          <LoginForm />
        </Card>
      </Col>
    </Row>
  );
};

export default Login;
