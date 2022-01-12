import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Col, Form, FormInstance, Input, Row, Select, Switch } from "antd";
import rules from "../../utils/rules";
import { routeNames } from "../../routes";
import { settingService } from "../../api/SettingService";
import {
  ISetting,
  ISettingModelOptions,
  SETTING_TYPE_BOOL,
  SETTING_TYPE_INT,
  SETTING_TYPE_STRING,
} from "../../models/ISetting";
import { DEFAULT_ROW_GUTTER } from "../../utils/constants";
import useModelType from "../../hooks/modelType.hook";

const modelRoutes = routeNames.setting;

const SettingPage: FC = () => {
  const { id } = useParams();

  const modelForm = useModelForm<ISetting, ISettingModelOptions>(
    id,
    settingService
  );

  const { type, typeChangeHandler } = useModelType<number>(
    modelForm.initData?.type
  );

  const getValueField = () => {
    if (type === SETTING_TYPE_BOOL)
      return (
        <Form.Item
          label="Значение"
          name="value_bool"
          valuePropName={"checked"}
          shouldUpdate={true}
        >
          <Switch checked={false} />
        </Form.Item>
      );
    if (type === SETTING_TYPE_INT)
      return (
        <Form.Item label="Значение" name="value_int" shouldUpdate={true}>
          <Input />
        </Form.Item>
      );
    if (type === SETTING_TYPE_STRING)
      return (
        <Form.Item label="Значение" name="value_string" shouldUpdate={true}>
          <Input />
        </Form.Item>
      );

    return null;
  };

  const formContent = (
    initData: ISetting,
    modelOptions: ISettingModelOptions,
    form: FormInstance
  ) => (
    <>
      <Row gutter={DEFAULT_ROW_GUTTER}>
        <Col span={24} md={12}>
          <Form.Item label="Название" name="name" rules={[rules.required()]}>
            <Input />
          </Form.Item>
        </Col>
        <Col span={24} md={12}>
          <Form.Item label="Ключ" name="key" rules={[rules.required()]}>
            <Input />
          </Form.Item>
        </Col>
      </Row>

      <Form.Item label="Тип" name="type" rules={[rules.required()]}>
        <Select
          style={{ width: "100%" }}
          placeholder="Выберите тип"
          onChange={typeChangeHandler}
        >
          {modelOptions.type.map((type: any) => (
            <Select.Option key={type.value} value={type.value}>
              {type.text}
            </Select.Option>
          ))}
        </Select>
      </Form.Item>

      {getValueField()}
    </>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "Редактирование" : "Добавление"} параметра`}
        backPath={modelRoutes.index}
        breadcrumbItems={[
          { path: modelRoutes.index, label: "Настройки" },
          {
            path: modelRoutes.updateUrl(id),
            label: modelForm.initData?.name ?? id,
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

export default SettingPage;
