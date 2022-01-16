import React, { FC } from "react";
import rules from "../../utils/rules";
import { Form, Input } from "antd";
import FileUpload from "../../components/crud/form/FileUpload/FileUpload";

const SliderBlockForm: FC = () => {
  return (
    <>
      <Form.Item label="Заголовок" name="title" rules={[rules.required()]}>
        <Input />
      </Form.Item>

      <Form.Item name="images_id" noStyle={true}>
        <FileUpload label="Изображения" accept=".jpg,.png,.gif" />
      </Form.Item>
    </>
  );
};

export default SliderBlockForm;
