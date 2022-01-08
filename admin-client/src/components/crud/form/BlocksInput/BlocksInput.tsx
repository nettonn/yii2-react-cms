import React, { FC } from "react";
import { IValueTextOption } from "../../../../types";
import BlocksList from "./BlocksList";

interface BlocksInputProps {
  value?: string[] | null; // blocks
  onChange?: (blocks: string[]) => void;
  blockOptions: IValueTextOption[];
}

const BlocksInput: FC<BlocksInputProps> = ({
  value,
  onChange,
  blockOptions,
}) => {
  if (!value || !onChange) return null;

  return (
    <BlocksList
      blocks={value}
      setBlocks={onChange}
      blockOptions={blockOptions}
    />
  );
};

export default BlocksInput;
