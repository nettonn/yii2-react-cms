import React, { FC } from "react";
import { Button, Result } from "antd";

interface ErrorProps {
  error: Error;
  resetErrorBoundary: () => void;
}

const ErrorFallback: FC<ErrorProps> = ({ error, resetErrorBoundary }) => {
  return (
    <Result
      status="error"
      title={error.message ?? "Возникла ошибка"}
      extra={[
        <Button
          type="primary"
          key="reload"
          onClick={() => window.location.reload()}
        >
          Обновить страницу
        </Button>,
      ]}
    />
  );
};

export default ErrorFallback;
