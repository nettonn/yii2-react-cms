import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { Link, useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Button, Col, Form, Input, Row, Select, Switch, Tabs } from "antd";
import rules from "../../utils/rules";
import { routeNames } from "../../routes";
import { PostSection, PostSectionModelOptions } from "../../models/PostSection";
import { postSectionService } from "../../api/PostSectionService";
import useGenerateAlias from "../../hooks/generateAlias.hook";
import { DEFAULT_ROW_GUTTER } from "../../utils/constants";
import CkeditorInput from "../../components/crud/form/CkeditorInput/CkeditorInput";
import useModelType from "../../hooks/modelType.hook";
import { MenuOutlined } from "@ant-design/icons";

const modelRoutes = routeNames.postSection;
const postRoutes = routeNames.post;

const PostSectionPage: FC = () => {
  const { id } = useParams();

  const modelForm = useModelForm<PostSection, PostSectionModelOptions>(
    id,
    postSectionService,
    ["content"]
  );

  const { type, typeChangeHandler } = useModelType(modelForm.initData?.type);

  const getTypeForm = () => {
    if (type === null) return null;

    return null;
  };

  const [onNameFieldChange, onAliasFieldChange] = useGenerateAlias(
    modelForm.form,
    "name",
    "alias"
  );

  const formContent = (
    initData: PostSection,
    modelOptions: PostSectionModelOptions
  ) => (
    <Tabs type="card">
      <Tabs.TabPane tab="Общее" key="common">
        <Row gutter={DEFAULT_ROW_GUTTER}>
          <Col span={24} md={12}>
            <Form.Item label="Название" name="name" rules={[rules.required()]}>
              <Input onChange={(e) => onNameFieldChange(e.target.value)} />
            </Form.Item>
          </Col>
          <Col span={24} md={12}>
            <Form.Item
              label="Псевдоним"
              name="alias"
              rules={[rules.required()]}
            >
              <Input onChange={(e) => onAliasFieldChange(e.target.value)} />
            </Form.Item>
          </Col>
        </Row>

        <Form.Item label="Краткое описание" name="description">
          <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
        </Form.Item>

        <Form.Item label="Содержимое" name="content">
          <CkeditorInput />
        </Form.Item>

        <Form.Item label="Тип" name="type">
          <Select onChange={typeChangeHandler}>
            {modelOptions?.type.map((i) => (
              <Select.Option key={i.value} value={i.value}>
                {i.text}
              </Select.Option>
            ))}
          </Select>
        </Form.Item>

        {getTypeForm()}

        <Form.Item label="Статус" name="status" valuePropName="checked">
          <Switch />
        </Form.Item>

        {id ? (
          <Link to={postRoutes.indexUrl(id)}>
            <Button icon={<MenuOutlined />}>Записи</Button>
          </Link>
        ) : null}
      </Tabs.TabPane>
      <Tabs.TabPane tab="SEO" key="seo">
        <Form.Item label="SEO Title" name="seo_title">
          <Input />
        </Form.Item>
        <Form.Item label="SEO H1" name="seo_h1">
          <Input />
        </Form.Item>
        <Form.Item label="SEO Description" name="seo_description">
          <Input.TextArea />
        </Form.Item>
        <Form.Item label="SEO Keywords" name="seo_keywords">
          <Input.TextArea />
        </Form.Item>
      </Tabs.TabPane>
    </Tabs>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "Редактирование" : "Создание"} разделов записей`}
        backPath={modelRoutes.index}
        breadcrumbItems={[
          { path: modelRoutes.index, label: "Разделы записей" },
          {
            path: modelRoutes.updateUrl(id),
            label: modelForm.initData?.name ?? id ?? "Создание",
          },
        ]}
      />

      <ModelForm
        {...modelForm}
        formContent={formContent}
        exitRoute={modelRoutes.index}
        createRoute={modelRoutes.create}
        updateRoute={modelRoutes.update}
        hasViewUrl={true}
      />
    </>
  );
};

export default PostSectionPage;
