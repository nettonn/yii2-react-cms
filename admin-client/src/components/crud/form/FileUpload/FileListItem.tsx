import React, { FC, useState } from "react";
import { useSortable } from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";
import { IFileModel } from "../../../../models/IFileModel";
import { Button, Col, Popconfirm, Image } from "antd";
import {
  DeleteOutlined,
  DragOutlined,
  FileTextOutlined,
  FilePdfOutlined,
  FileWordOutlined,
  FileZipOutlined,
  FileOutlined,
} from "@ant-design/icons";
import {
  stopPropagation,
  withStopPropagation,
} from "../../../../utils/functions";
import _throttle from "lodash/throttle";

const renderFileIcon = (ext: string) => {
  switch (ext) {
    case "txt":
      return <FileTextOutlined />;
    case "pdf":
      return <FilePdfOutlined />;
    case "doc":
    case "docx":
    case "docm":
      return <FileWordOutlined />;
    case "zip":
      return <FileZipOutlined />;
    default:
      return <FileOutlined />;
  }
};

interface FileListItemProps {
  fileModel: IFileModel;
  deleteHandler: (id: number) => void;
  hasDrag?: boolean;
}

const FileListItem: FC<FileListItemProps> = ({
  fileModel,
  deleteHandler,
  hasDrag = true,
}) => {
  const [isHovered, setIsHovered] = useState(false);
  const [isPopConfirmActive, setIsPopConfirmActive] = useState(false);
  const {
    attributes,
    listeners,
    setNodeRef,
    transform,
    transition,
    isDragging,
  } = useSortable({ id: fileModel.id.toString() });

  const style: any = {
    transform: CSS.Transform.toString(transform),
    transition,
    zIndex: isDragging ? 100 : null,
  };

  const mouseOverHandler = _throttle(() => {
    setIsHovered(true);
  }, 200);

  const mouseLeaveHandler = _throttle(() => {
    setIsHovered(false);
  }, 200);

  if (fileModel.is_image) {
    const renderMask = () => {
      if (!isHovered && !isPopConfirmActive) return null;

      return (
        <>
          <div className="app-file-list-mask-name">{fileModel.name}</div>
          <div className="app-file-list-mask-buttons">
            {hasDrag ? (
              <Button {...attributes} {...listeners}>
                <DragOutlined />
              </Button>
            ) : null}

            <div onClick={stopPropagation}>
              <Popconfirm
                onVisibleChange={(visible) => setIsPopConfirmActive(visible)}
                title="Удалить?"
                onConfirm={withStopPropagation(() =>
                  deleteHandler(fileModel.id)
                )}
              >
                <Button onClick={stopPropagation}>
                  <DeleteOutlined />
                </Button>
              </Popconfirm>
            </div>
          </div>
        </>
      );
    };

    return (
      <Col xs={12} sm={8} md={6} lg={4} ref={setNodeRef} style={style}>
        <Image
          onMouseOver={mouseOverHandler}
          onMouseLeave={mouseLeaveHandler}
          src={fileModel.image_thumbs?.thumb}
          preview={{
            src: fileModel.image_thumbs?.normal,
            maskClassName: `app-file-list-mask ${
              isPopConfirmActive ? "hover" : ""
            }`,
            mask: renderMask(),
          }}
        />
      </Col>
    );
  }

  const renderMask = () => {
    if (!isHovered && !isPopConfirmActive) return null;

    return (
      <div onClick={stopPropagation}>
        {hasDrag ? (
          <Button {...attributes} {...listeners}>
            <DragOutlined />
          </Button>
        ) : null}
        <Popconfirm
          onVisibleChange={(visible) => setIsPopConfirmActive(visible)}
          title="Удалить?"
          onConfirm={withStopPropagation(() => deleteHandler(fileModel.id))}
        >
          <Button onClick={stopPropagation}>
            <DeleteOutlined />
          </Button>
        </Popconfirm>
      </div>
    );
  };

  if (!fileModel.file_thumb) return null;

  return (
    <Col xs={24} sm={12} md={8} lg={8} ref={setNodeRef} style={style}>
      <div
        className="app-file-list-file"
        onMouseOver={mouseOverHandler}
        onMouseLeave={mouseLeaveHandler}
      >
        <div className="app-file-list-file-name">
          <a
            href={fileModel.file_thumb}
            title={fileModel.name}
            target="_blank"
            rel="noopener noreferrer"
          >
            {renderFileIcon(fileModel.ext)} {fileModel.name}
          </a>
        </div>
        <div className="app-file-list-file-buttons">{renderMask()}</div>
      </div>
    </Col>
  );
};

// FileListItem.whyDidYouRender = true;

export default FileListItem;
