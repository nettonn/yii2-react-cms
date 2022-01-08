import React, { CSSProperties, FC, useState } from "react";
import { useSortable } from "@dnd-kit/sortable";
import { CSS } from "@dnd-kit/utilities";
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

import _throttle from "lodash/throttle";
import { IFileModel } from "../../../models/IFileModel";
import {
  openNewWindow,
  stopPropagation,
  withStopPropagation,
} from "../../../utils/functions";

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
  hasControls?: boolean;
}

const Item: FC<FileListItemProps> = ({
  fileModel,
  deleteHandler,
  hasControls = true,
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

  const style: CSSProperties = {
    transform: CSS.Transform.toString(transform),
    transition,
    zIndex: isDragging ? 100 : undefined,
    position: "relative",
  };

  const mouseOverHandler = _throttle(() => {
    setIsHovered(true);
  }, 200);

  const mouseLeaveHandler = _throttle(() => {
    setIsHovered(false);
  }, 200);

  const renderControls = () => {
    if (!hasControls) return null;

    return (
      <div className="app-file-list-mask-buttons">
        <Button {...attributes} {...listeners}>
          <DragOutlined />
        </Button>

        <div onClick={stopPropagation}>
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
      </div>
    );
  };

  const renderMask = () => {
    if (!isHovered && !isPopConfirmActive) return null;

    return (
      <>
        <div className="app-file-list-mask-name">{fileModel.name}</div>
        {renderControls()}
      </>
    );
  };

  const renderItem = () => {
    if (fileModel.is_image)
      return (
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
      );

    if (!fileModel.file_thumb) return null;

    return (
      <div
        className="app-file-list-file"
        onMouseOver={mouseOverHandler}
        onMouseLeave={mouseLeaveHandler}
      >
        <img
          src="data:image/gif;base64,R0lGODlhBAADAIAAAP///wAAACH5BAEAAAEALAAAAAAEAAMAAAIDjI9WADs="
          alt=""
        />
        <div className="app-file-list-file-icon">
          {renderFileIcon(fileModel.ext)} {fileModel.name}
        </div>
        {isHovered || isPopConfirmActive ? (
          <div
            onClick={() => openNewWindow(fileModel.file_thumb)}
            className={`ant-image-mask app-file-list-mask ${
              isPopConfirmActive ? "hover" : ""
            }`}
          >
            {renderMask()}
          </div>
        ) : null}
      </div>
    );
  };

  return (
    <Col xs={12} sm={8} md={6} lg={4} ref={setNodeRef} style={style}>
      {renderItem()}
    </Col>
  );
};

export default Item;
