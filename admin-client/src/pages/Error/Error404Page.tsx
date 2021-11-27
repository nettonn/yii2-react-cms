import React, { FC } from "react";
import { Button, Result } from "antd";
import { useNavigate } from "react-router-dom";

const Error404Page: FC = () => {
  const navigate = useNavigate();

  return (
    <Result
      status="404"
      title="404"
      subTitle="Страница не найдена"
      extra={
        <Button type="primary" onClick={() => navigate(-1)}>
          Вернутся назад
        </Button>
      }
    />
  );
};

export default Error404Page;
