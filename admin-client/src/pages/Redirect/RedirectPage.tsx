import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Form, FormInstance, Input, Switch } from "antd";
import rules from "../../utils/rules";
import { routeNames } from "../../routes";
import { redirectService } from "../../api/RedirectService";
import { IRedirect, IRedirectModelOptions } from "../../models/IRedirect";

const modelRoutes = routeNames.redirect;

const RedirectPage: FC = () => {
  const { id } = useParams();

  const modelForm = useModelForm<IRedirect, IRedirectModelOptions>(
    id,
    redirectService
  );

  const formContent = (
    initData: IRedirect,
    modelOptions: IRedirectModelOptions,
    form: FormInstance
  ) => (
    <>
      <Form.Item
        label="Откуда"
        name="from"
        rules={[rules.required()]}
        extra="Адрес начинающийся со /. Можно использовать регулярные выражения."
      >
        <Input />
      </Form.Item>
      <Form.Item
        label="Куда"
        name="to"
        rules={[rules.required()]}
        extra="Адрес начинающийся со / или с http://"
      >
        <Input />
      </Form.Item>
      <Form.Item label="Код редиректа" name="code">
        <Input />
      </Form.Item>
      <Form.Item label="Сортировка" name="sort">
        <Input />
      </Form.Item>
      <Form.Item label="Статус" name="status" valuePropName="checked">
        <Switch checked={false} />
      </Form.Item>
    </>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "Редактирование" : "Создание"} редиректа`}
        backPath={modelRoutes.index}
        breadcrumbItems={[{ path: modelRoutes.index, label: "Редиректы" }]}
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

export default RedirectPage;
