import React, { FC } from "react";
import { Button, Result } from "antd";
import { useNavigate } from "react-router-dom";
import { routeNames } from "../../routes";

const Error403Page: FC = () => {
  const navigate = useNavigate();

  return (
    <Result
      status="403"
      title="403"
      subTitle="Доступ запрещен"
      extra={
        <>
          <Button type="primary" onClick={() => navigate(-1)}>
            Вернутся назад
          </Button>
          <Button onClick={() => navigate(routeNames.home)}>На главную</Button>
        </>
      }
    />
  );
};

export default Error403Page;
