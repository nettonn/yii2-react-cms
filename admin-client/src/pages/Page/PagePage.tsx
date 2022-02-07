import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Col, Form, Input, Row, Select, Switch, Tabs, TreeSelect } from "antd";
import rules from "../../utils/rules";
import { routeNames } from "../../routes";
import {
  Page,
  PageModelOptions,
  PAGE_TYPE_COMMON,
  PAGE_TYPE_MAIN,
} from "../../models/Page";
import FileUpload from "../../components/crud/form/FileUpload/FileUpload";
import { pageService } from "../../api/PageService";
import CkeditorInput from "../../components/crud/form/CkeditorInput/CkeditorInput";
import useGenerateAlias from "../../hooks/generateAlias.hook";
import { DEFAULT_ROW_GUTTER } from "../../utils/constants";
import useModelType from "../../hooks/modelType.hook";
import BlocksInput from "../../components/crud/form/BlocksInput/BlocksInput";

const modelRoutes = routeNames.page;

const PagePage: FC = () => {
  const { id } = useParams();

  const modelForm = useModelForm<Page, PageModelOptions>(id, pageService, [
    "content",
  ]);
  const { type, typeChangeHandler } = useModelType(modelForm.initData?.type);

  const getTypeForm = () => {
    if (type === PAGE_TYPE_COMMON) return null;
    if (type === PAGE_TYPE_MAIN) return null;
    return null;
  };

  const [onNameFieldChange, onAliasFieldChange] = useGenerateAlias(
    modelForm.form,
    "name",
    "alias"
  );

  const formContent = (initData: Page, modelOptions: PageModelOptions) => (
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
        <Form.Item label="Родитель" name="parent_id">
          <TreeSelect
            treeData={modelOptions?.parent}
            placeholder="Выберите"
            allowClear
          />
        </Form.Item>
        <Form.Item label="Краткое описание" name="description">
          <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
        </Form.Item>
        <Form.Item label="Содержимое" name="content">
          <CkeditorInput />
        </Form.Item>
        <Form.Item label="Тип" name="type" rules={[rules.required()]}>
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
          <Switch checked={false} />
        </Form.Item>
      </Tabs.TabPane>
      <Tabs.TabPane tab="Файлы" key="files">
        <Form.Item name="images_id" noStyle={true}>
          <FileUpload label="Изображения" accept=".jpg,.png,.gif" />
        </Form.Item>
      </Tabs.TabPane>
      <Tabs.TabPane tab="SEO" key="seo">
        <Form.Item label="SEO Title" name="seo_title">
          <Input />
        </Form.Item>
        <Form.Item label="SEO H1" name="seo_h1">
          <Input />
        </Form.Item>
        <Form.Item label="SEO Description" name="seo_description">
          <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
        </Form.Item>
        <Form.Item label="SEO Keywords" name="seo_keywords">
          <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
        </Form.Item>
      </Tabs.TabPane>
      <Tabs.TabPane tab="Блоки" key="blocks">
        <Form.Item name="blocks" label="Порядок блоков">
          <BlocksInput blockOptions={modelOptions.blocks} />
        </Form.Item>
      </Tabs.TabPane>
    </Tabs>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "Редактирование" : "Создание"} страниц`}
        backPath={modelRoutes.index}
        breadcrumbItems={[
          { path: modelRoutes.index, label: "Страницы" },
          {
            path: modelRoutes.updateUrl(id),
            label: modelForm.initData?.name ?? id,
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

export default PagePage;
