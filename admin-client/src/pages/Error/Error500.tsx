import React, { FC } from "react";
import { Button, Result } from "antd";
import { useNavigate } from "react-router-dom";

const Error500: FC = () => {
  const navigate = useNavigate();

  return (
    <Result
      status="500"
      title="500"
      subTitle="Возникла ошибка сервера"
      extra={
        <Button type="primary" onClick={() => navigate(-1)}>
          Вернутся назад
        </Button>
      }
    />
  );
};

export default Error500;
