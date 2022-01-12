import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Form, Input, Select, Switch } from "antd";
import rules from "../../utils/rules";
import { IUser, IUserModelOptions } from "../../models/IUser";
import { routeNames } from "../../routes";
import { userService } from "../../api/UserService";

const modelRoutes = routeNames.user;

const UserPage: FC = () => {
  const { id } = useParams();

  const modelForm = useModelForm<IUser, IUserModelOptions>(id, userService);

  const formContent = (initData: IUser, modelOptions: IUserModelOptions) => (
    <>
      <Form.Item label="Username" name="username">
        <Input />
      </Form.Item>
      <Form.Item label="E-Mail" name="email" rules={[rules.required()]}>
        <Input />
      </Form.Item>
      <Form.Item label="Роль" name="role" rules={[rules.required()]}>
        <Select style={{ width: "100%" }} placeholder="Выберите роль">
          {modelOptions.role.map((role) => (
            <Select.Option key={role.value} value={role.value}>
              {role.text}
            </Select.Option>
          ))}
        </Select>
      </Form.Item>

      <Form.Item label="Статус" name="status" valuePropName="checked">
        <Switch checked={false} />
      </Form.Item>
    </>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "Редактирование" : "Создание"} пользователя`}
        backPath={modelRoutes.index}
        breadcrumbItems={[
          { path: modelRoutes.index, label: "Пользователи" },
          {
            path: modelRoutes.updateUrl(id),
            label: `${id}`,
          },
        ]}
      />

      <ModelForm
        modelForm={modelForm}
        formContent={formContent}
        exitRoute={modelRoutes.index}
        createRoute={modelRoutes.create}
        updateRoute={modelRoutes.update}
      />
    </>
  );
};

export default UserPage;
