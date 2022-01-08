import React, { FC, useMemo } from "react";
import { IValueStrTextOption, IValueTextOption } from "../../../../types";
import {
  DndContext,
  closestCenter,
  MouseSensor,
  TouchSensor,
  useSensor,
  useSensors,
  DragEndEvent,
} from "@dnd-kit/core";
import {
  SortableContext,
  verticalListSortingStrategy,
  arrayMove,
} from "@dnd-kit/sortable";
import BlocksItem from "./BlocksItem";
import { Select } from "antd";

interface Item {
  name: string;
  value: string;
}

interface BlocksListProps {
  blocks: string[];
  setBlocks: (blocks: string[]) => void;
  blockOptions: IValueTextOption[];
}

const BlocksList: FC<BlocksListProps> = ({
  blocks,
  setBlocks,
  blockOptions: blockOptionsDefault,
}) => {
  const sensors = useSensors(useSensor(MouseSensor), useSensor(TouchSensor));

  const dragEndHandler = (event: DragEndEvent) => {
    if (event.active && event.over) {
      const activeId = event.active.id;
      const overId = event.over.id;
      if (activeId !== overId) {
        const oldIndex = blocks.indexOf(activeId);
        const newIndex = blocks.indexOf(overId);
        const newBlocks = arrayMove(blocks, oldIndex, newIndex);
        setBlocks(newBlocks);
      }
    }
  };

  const addHandler = (value: string) => {
    setBlocks([...blocks, value]);
  };

  const deleteHandler = (value: string) => {
    const newBlocks = blocks.filter((block) => block !== value);
    setBlocks(newBlocks);
  };

  const blockOptions: IValueStrTextOption[] = useMemo(
    () =>
      blockOptionsDefault.map((option) => {
        option.value = option.value.toString();
        return option as IValueStrTextOption;
      }),
    [blockOptionsDefault]
  );

  const restBlockOptions: IValueTextOption[] = useMemo(
    () => blockOptions.filter((option) => !blocks.includes(option.value)),
    [blocks, blockOptions]
  );

  const items: Item[] = useMemo(() => {
    const result = [] as Item[];

    if (!blocks) return result;

    blocks.forEach((block) => {
      const option = blockOptions.find((option) => option.value === block);
      if (!option) return;

      result.push({
        name: option.text,
        value: option.value,
      });
    });
    return result;
  }, [blocks, blockOptions]);

  const renderItems = () => {
    if (items.length === 0) return null;

    return (
      <div className="app-blocks-list__items">
        {items.map((record) => (
          <BlocksItem
            key={record.value}
            {...record}
            deleteHandler={deleteHandler}
          />
        ))}
      </div>
    );
  };

  const renderAddButton = () => {
    if (restBlockOptions.length === 0) return null;

    return (
      <div className="app-blocks-list__add">
        <Select
          style={{ width: 200 }}
          onChange={addHandler}
          placeholder="Добавить"
          value={null}
        >
          {restBlockOptions.map((i) => (
            <Select.Option key={i.value} value={i.value}>
              {i.text}
            </Select.Option>
          ))}
        </Select>
      </div>
    );
  };

  return (
    <div className="app-blocks-list">
      <DndContext
        sensors={sensors}
        collisionDetection={closestCenter}
        onDragEnd={dragEndHandler}
      >
        <SortableContext items={blocks} strategy={verticalListSortingStrategy}>
          {renderItems()}
        </SortableContext>
      </DndContext>
      {renderAddButton()}
    </div>
  );
};

export default BlocksList;
