import React, { FC } from "react";
import rules from "../../utils/rules";
import {Form, Input} from "antd";

const SliderBlockForm: FC = () => {
  return <>
    <Form.Item label="Заголовок" name="title" rules={[rules.required()]}>
      <Input />
    </Form.Item>
  </>;
};

export default SliderBlockForm;
