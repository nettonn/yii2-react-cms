import React, { FC } from "react";
import { Form, Input } from "antd";
import FileUpload from "../../components/crud/form/FileUpload/FileUpload";

const SliderBlockItemForm: FC = () => {
  return (
    <>
      <Form.Item label="Заголовок" name="title">
        <Input />
      </Form.Item>

      <Form.Item label="Описание" name="description">
        <Input.TextArea />
      </Form.Item>

      <Form.Item name="image_id" noStyle={true}>
        <FileUpload
          label="Изображение"
          accept=".jpg,.png,.gif"
          multiple={false}
        />
      </Form.Item>
    </>
  );
};

export default SliderBlockItemForm;
