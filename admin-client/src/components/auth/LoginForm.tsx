import React, { FC, useEffect, useState } from "react";
import { Form, Input, Button, Checkbox, message } from "antd";
import rules from "../../utils/rules";
import { FieldData } from "rc-field-form/es/interface";
import { useAppActions, useAppSelector } from "../../hooks/redux";
import { authActions } from "../../store/reducers/auth";

const LoginForm: FC = () => {
  const [isLoading, setIsLoading] = useState(false);
  const [form] = Form.useForm();
  const { error, validationErrors } = useAppSelector((state) => state.auth);

  const { login } = useAppActions(authActions);

  useEffect(() => {
    if (error) message.error(error);
  }, [error]);

  useEffect(() => {
    if (validationErrors) {
      setIsLoading(false);
      const fields = [] as FieldData[];
      for (const { field, message } of validationErrors) {
        fields.push({
          name: field,
          errors: [message],
        });
      }
      form.setFields(fields);
    }
  }, [validationErrors, form]);

  const onFinish = async (values: any) => {
    setIsLoading(true);
    await login(values);
  };

  return (
    <Form
      name="basic"
      form={form}
      layout="vertical"
      initialValues={{ remember: true }}
      onFinish={onFinish}
      // onFinishFailed={onFinishFailed}
      autoComplete="off"
    >
      <Form.Item label="E-Mail" name="email" rules={[rules.required()]}>
        <Input />
      </Form.Item>

      <Form.Item label="Пароль" name="password" rules={[rules.required()]}>
        <Input.Password />
      </Form.Item>

      <Form.Item name="remember" valuePropName="checked">
        <Checkbox>Запомнить</Checkbox>
      </Form.Item>

      <Form.Item>
        <Button type="primary" htmlType="submit" loading={isLoading}>
          Войти
        </Button>
      </Form.Item>
    </Form>
  );
};

export default LoginForm;
