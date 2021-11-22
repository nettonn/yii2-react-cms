import React, { FC } from "react";
import { DatePicker, Form, FormInstance, Input, Select } from "antd";
import rules from "../../utils/rules";
import { IUser } from "../../models/IUser";
import moment, { Moment } from "moment";

interface EventFormProps {
  guests: IUser[];
  form?: FormInstance;
  onSubmit: (values: any) => void;
}

const EventForm: FC<EventFormProps> = ({ guests, form, onSubmit }) => {
  return (
    <Form
      form={form}
      name="basic"
      layout="vertical"
      onFinish={onSubmit}
      autoComplete="off"
    >
      <Form.Item label="Описание" name="description" rules={[rules.required()]}>
        <Input />
      </Form.Item>

      <Form.Item label="Дата" name="date" rules={[rules.required()]}>
        <DatePicker
          format="DD.MM.YY"
          disabledDate={(current: Moment) => current <= moment().startOf("day")}
        />
      </Form.Item>
      <Form.Item label="Гости" name="guests" rules={[rules.required()]}>
        <Select
          mode="multiple"
          allowClear
          style={{ width: "100%" }}
          placeholder="Выберите гостей"
        >
          {guests.map((guest) => (
            <Select.Option key={guest.id} value={guest.id}>
              {guest.email}
            </Select.Option>
          ))}
        </Select>
      </Form.Item>
    </Form>
  );
};

export default EventForm;
