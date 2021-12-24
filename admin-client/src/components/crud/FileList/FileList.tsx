import React, { FC, useCallback, useEffect, useState } from "react";
import _unionBy from "lodash/unionBy";
import _differenceBy from "lodash/differenceBy";
import { Image, Row, Spin } from "antd";
import {
  DndContext,
  closestCenter,
  // KeyboardSensor,
  // PointerSensor,
  MouseSensor,
  TouchSensor,
  useSensor,
  useSensors,
  DragEndEvent,
} from "@dnd-kit/core";
import {
  SortableContext,
  // sortableKeyboardCoordinates,
  rectSortingStrategy,
  arrayMove,
} from "@dnd-kit/sortable";
import Item from "./Item";
import { parseInt, uniq } from "lodash";
import { useQuery } from "react-query";
import { IFileModel } from "../../../models/IFileModel";
import { fileService } from "../../../api/FileService";
import { sortObjectByIds } from "../../../utils/functions";

interface FileListProps {
  fileIds: number[];
  onChange?: (fileIds: number[] | null) => void;
  hasControls?: boolean;
}

const FileList: FC<FileListProps> = ({ fileIds, onChange, hasControls }) => {
  const [isInit, setIsInit] = useState(false);
  const [toFetchIds, setToFetchIds] = useState<number[]>([]);
  const [fileModels, setFileModels] = useState<IFileModel[]>([]);

  const sensors = useSensors(
    useSensor(MouseSensor),
    useSensor(TouchSensor)
    // useSensor(PointerSensor)
    // useSensor(KeyboardSensor, {
    //   coordinateGetter: sortableKeyboardCoordinates,
  );

  const dragEndHandler = useCallback(
    (event: DragEndEvent) => {
      if (event.active && event.over) {
        const activeId = parseInt(event.active.id);
        const overId = parseInt(event.over.id);
        if (activeId !== overId) {
          const oldIndex = fileIds.indexOf(activeId);
          const newIndex = fileIds.indexOf(overId);
          const newFileIds = arrayMove(fileIds, oldIndex, newIndex);

          onChange && onChange(newFileIds);
        }
      }
    },
    [fileIds, onChange]
  );

  useEffect(() => {
    if (isInit) return;
    if (!fileIds || fileIds.length === 0) {
      setIsInit(true);
    }
  }, [isInit, fileIds]);

  // Fetch file models
  useEffect(() => {
    const notFindIds: number[] = [];
    if (!fileIds || fileIds.length === 0) return;
    for (const id of fileIds) {
      if (!fileModels.find((o) => o.id === id)) {
        notFindIds.push(id);
      }
    }

    if (notFindIds.length === 0) return;

    setToFetchIds((prev) => uniq([...prev, ...notFindIds]));
  }, [fileIds, fileModels]);

  useQuery(
    [fileService.indexQueryKey(), { ids: toFetchIds }],
    async ({ signal }) => {
      const params = { ids: toFetchIds };
      const result = await fileService.index<IFileModel>(params, signal);
      return result.data;
    },
    {
      // keepPreviousData: true,
      enabled: toFetchIds.length > 0,
      onSuccess: (data) => {
        setFileModels((prev) => {
          const diff = _differenceBy(data, prev, "id");
          if (diff.length === 0) return prev;
          const models = _unionBy(prev, data, "id");
          return sortObjectByIds(fileIds, models);
        });
        setToFetchIds([]);
        setIsInit(true);
      },
    }
  );

  const deleteHandler = (id: number) => {
    onChange && onChange(fileIds.filter((itemId) => itemId !== id));
  };

  if (!fileIds) return null;

  if (fileIds.length !== 0 && !isInit) return <Spin />;

  if (fileIds.length === 0) return null;

  const fileIdsString = fileIds.map((id) => id.toString());

  const files = [];
  for (const fileId of fileIds) {
    const fileModel = fileModels.find((o) => fileId === o.id);
    if (fileModel) files.push(fileModel);
  }

  const renderList = (files: IFileModel[]) => {
    return (
      <Row className="app-file-list">
        <Image.PreviewGroup>
          {files.map((fileModel) => (
            <Item
              key={fileModel.id}
              fileModel={fileModel}
              deleteHandler={deleteHandler}
              hasControls={hasControls}
            />
          ))}
        </Image.PreviewGroup>
      </Row>
    );
  };

  return (
    <DndContext
      sensors={sensors}
      collisionDetection={closestCenter}
      onDragEnd={dragEndHandler}
    >
      <SortableContext items={fileIdsString} strategy={rectSortingStrategy}>
        {renderList(files)}
      </SortableContext>
    </DndContext>
  );
};

export default FileList;
