import React, { FC } from "react";
import { Button } from "antd";
import { useAppActions } from "../../../hooks/redux";
import { dataGridActions } from "../../../store/reducers/grid/grids";
import { useNavigate } from "react-router-dom";
import { routeNames } from "../../../routes";

interface VersionsButtonProps {
  modelId?: string | number;
  modelClass?: string;
  isLoading?: boolean;
}

const VersionsButton: FC<VersionsButtonProps> = ({
  modelId,
  modelClass,
  isLoading,
}) => {
  const { setFilters, reset } = useAppActions(dataGridActions.version);
  const navigate = useNavigate();

  if (!modelId || !modelClass) return null;

  const clickHandler = () => {
    reset();

    setFilters({
      link_id: [modelId],
      link_class: [modelClass],
    } as any);

    navigate(routeNames.version.index);
  };

  return (
    <Button onClick={() => clickHandler()} disabled={isLoading}>
      Версии
    </Button>
  );
};

export default VersionsButton;
