import React, { FC } from "react";
import PageActions from "./PageActions";
import { Button } from "antd";
import { useNavigate } from "react-router-dom";

interface IndexPageActionsProps {
  createPath: string;
  createLabel?: string;
}

const IndexPageActions: FC<IndexPageActionsProps> = ({
  createPath,
  createLabel = "Добавить",
}) => {
  const navigate = useNavigate();
  return (
    <PageActions
      content={
        <Button type="primary" onClick={() => navigate(createPath)}>
          {createLabel}
        </Button>
      }
    />
  );
};

export default IndexPageActions;
