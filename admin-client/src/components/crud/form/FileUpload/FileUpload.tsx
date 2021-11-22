import React, { FC, useState, useRef, ChangeEvent } from "react";
import { fileService } from "../../../../api/FileService";
import { prepareAxiosConfig } from "../../../../utils/functions";
import { $api } from "../../../../http/api";
import { Button, message } from "antd";
import { UploadOutlined } from "@ant-design/icons";
import { IFileModel } from "../../../../models/IFileModel";
import FileList from "./FileList";
import "./FileUpload.css";

interface FileUploadProps {
  value?: number[] | null; // fileIds
  onChange?: (fileIds: number[] | null) => void;
  inputName?: string;
  label?: string;
  isImages?: boolean;
  accept?: string;
  multiple?: boolean;
}

const FileUpload: FC<FileUploadProps> = ({
  value,
  onChange,
  inputName = "file",
  label,
  isImages = true,
  accept,
  multiple = true,
}) => {
  const [isUploading, setIsUploading] = useState(false);
  const fileInputRef = useRef<HTMLInputElement>(null);

  const getFileIds = () => {
    if (!value || !Array.isArray(value)) {
      return [];
    }
    return value;
  };

  const fileIds = getFileIds();

  const onButtonClickHandler = () => {
    fileInputRef.current?.click();
  };

  const uploadHandler = async (e: ChangeEvent<HTMLInputElement>) => {
    if (e.target.files && e.target.files.length) {
      setIsUploading(true);

      const newFileIds = [];

      const fileList = Array.from(e.target.files);
      for (const file of fileList) {
        try {
          const formData = new FormData();
          formData.append(inputName, file);
          const config = prepareAxiosConfig(fileService.createConfig());
          config.data = formData;
          const response = await $api.request<IFileModel>(config);
          newFileIds.push(response.data.id);
        } catch (e: any) {
          message.error(e.message || `Error uploading file "${file.name}"`);
        }
      }
      if (multiple) {
        onChange && onChange([...fileIds, ...newFileIds]);
      } else {
        onChange && onChange(newFileIds.slice(0, 1));
      }

      setIsUploading(false);
      e.target.value = "";
    }
  };

  return (
    <>
      <div className="app-file-upload-button">
        <label>
          <Button
            onClick={onButtonClickHandler}
            icon={<UploadOutlined />}
            loading={isUploading}
          >
            {label}
          </Button>
          <input
            type="file"
            name={inputName}
            ref={fileInputRef}
            multiple={multiple}
            accept={accept}
            onChange={uploadHandler}
            style={{ display: "none" }}
          />
        </label>
      </div>
      {fileIds.length ? (
        <FileList fileIds={fileIds} onChange={onChange} isImages={isImages} />
      ) : null}
    </>
  );
};

export default FileUpload;
