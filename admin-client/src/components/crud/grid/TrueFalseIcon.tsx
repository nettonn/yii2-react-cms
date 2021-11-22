import React, { FC } from "react";
import { CheckCircleOutlined, CloseCircleOutlined } from "@ant-design/icons";

interface TrueFalseIconProps {
  value: boolean;
}

const TrueFalseIcon: FC<TrueFalseIconProps> = ({ value }) => {
  if (value) return <CheckCircleOutlined style={{ color: "green" }} />;

  return <CloseCircleOutlined style={{ color: "red" }} />;
};

export default TrueFalseIcon;
