import React, { FC } from "react";
import { Form, Input, Button } from "antd";
import rules from "../../utils/rules";
import { useAppActions } from "../../hooks/redux";
import { authActions } from "../../store/reducers/auth";
import { useMutation } from "react-query";
import { authService } from "../../api/AuthService";
import {
  prepareAntdValidationErrors,
  requestErrorHandler,
} from "../../utils/functions";
import { useLocation, useNavigate } from "react-router-dom";
import { routeNames } from "../../routes";

interface ILoginFormValues {
  email: string;
  password: string;
}

const LoginForm: FC = () => {
  const [form] = Form.useForm<ILoginFormValues>();

  const { authorize } = useAppActions(authActions);

  const location = useLocation();
  const navigate = useNavigate();

  const { isLoading, mutate: login } = useMutation(
    async (values: ILoginFormValues) => {
      const { identity, token } = await authService.login(values);

      authService.setStorage({
        isAuth: true,
        token: token,
        identity: identity,
      });
      authorize({ identity, token });

      let url = routeNames.home;
      if (location.state.returnUrl) {
        url = location.state.returnUrl;
        location.state.returnUrl = null;
      }
      navigate(url, { replace: true });
    },
    {
      onError: (e) => {
        const errors = requestErrorHandler(e);
        if (errors.validationErrors) {
          const antdValidationErrors = prepareAntdValidationErrors(
            errors.validationErrors
          );
          form.setFields(antdValidationErrors);
        }
      },
    }
  );

  return (
    <Form
      name="basic"
      form={form}
      layout="vertical"
      onFinish={login}
      autoComplete="off"
    >
      <Form.Item label="E-Mail" name="email" rules={[rules.required()]}>
        <Input disabled={isLoading} />
      </Form.Item>

      <Form.Item label="Пароль" name="password" rules={[rules.required()]}>
        <Input.Password disabled={isLoading} />
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
