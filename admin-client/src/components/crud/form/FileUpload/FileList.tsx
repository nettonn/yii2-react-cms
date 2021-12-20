import React, { FC, useCallback, useEffect, useState } from "react";
import { IFileModel } from "../../../../models/IFileModel";
import _unionBy from "lodash/unionBy";
import _differenceBy from "lodash/differenceBy";
import { fileService } from "../../../../api/FileService";
import { sortObjectByIds } from "../../../../utils/functions";
import { Image, Row, Spin } from "antd";
import "./FileList.css";
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
import FileListItem from "./FileListItem";
import { parseInt, uniq } from "lodash";
import { useQuery } from "react-query";

interface FileListProps {
  fileIds: number[];
  onChange?: (fileIds: number[] | null) => void;
  isImages?: boolean;
}

const FileList: FC<FileListProps> = ({
  fileIds,
  onChange,
  isImages = false,
}) => {
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
    if (fileIds.length === 0) setIsInit(true);
  }, [isInit, fileIds]);

  // Fetch file models
  useEffect(() => {
    const notFindIds: number[] = [];
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

  if (fileIds.length !== 0 && !isInit) return <Spin />;

  if (fileIds.length === 0) return null;

  const fileIdsString = fileIds.map((id) => id.toString());

  const files = [];
  for (const fileId of fileIds) {
    const fileModel = fileModels.find((o) => fileId === o.id);
    if (fileModel) files.push(fileModel);
  }

  return (
    <DndContext
      sensors={sensors}
      collisionDetection={closestCenter}
      onDragEnd={dragEndHandler}
    >
      <SortableContext items={fileIdsString} strategy={rectSortingStrategy}>
        <Row
          className={isImages ? "app-file-list-images" : "app-file-list-files"}
        >
          <Image.PreviewGroup>
            {files.map((fileModel) => (
              <FileListItem
                key={fileModel.id}
                fileModel={fileModel}
                deleteHandler={deleteHandler}
              />
            ))}
          </Image.PreviewGroup>
        </Row>
      </SortableContext>
    </DndContext>
  );
};

export default FileList;
