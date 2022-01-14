import React, { FC } from "react";
import { ValueTextOption } from "../../../../types";
import BlocksList from "./BlocksList";

interface BlocksInputProps {
  value?: string[] | null; // blocks
  onChange?: (blocks: string[]) => void;
  blockOptions: ValueTextOption[];
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
