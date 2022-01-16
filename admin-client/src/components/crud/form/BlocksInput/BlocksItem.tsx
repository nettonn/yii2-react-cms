import React, { CSSProperties, FC } from "react";
import { useSortable } from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";
import { Button } from "antd";
import { DeleteOutlined, MenuOutlined } from "@ant-design/icons";

interface BlocksItemProps {
  name: string;
  value: string;
  deleteHandler: (value: string) => void;
}

const BlocksItem: FC<BlocksItemProps> = ({ name, value, deleteHandler }) => {
  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
    isDragging,
  } = useSortable({ id: value });

  const style: CSSProperties = {
    transform: CSS.Transform.toString(transform),
    transition,
    zIndex: isDragging ? 100 : undefined,
    position: "relative",
  };

  return (
    <div ref={setNodeRef} style={style}>
      <div className="app-blocks-list__item">
        <div className="app-blocks-list__drag">
          <Button {...attributes} {...listeners}>
            <MenuOutlined />
          </Button>
        </div>
        <div className="app-blocks-list__name">{name}</div>
        <div className="app-blocks-list__delete">
          <Button onClick={() => deleteHandler(value)}>
            <DeleteOutlined />
          </Button>
        </div>
      </div>
    </div>
  );
};

export default BlocksItem;
