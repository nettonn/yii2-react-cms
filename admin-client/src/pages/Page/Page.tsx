import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Col, Form, Input, Row, Switch, Tabs, TreeSelect } from "antd";
import rules from "../../utils/rules";
import { RouteNames } from "../../routes";
import { IPage, IPageModelOptions } from "../../models/IPage";
import FileUpload from "../../components/crud/form/FileUpload/FileUpload";
import { pageService } from "../../api/PageService";
import CkeditorInput from "../../components/crud/form/CkeditorInput/CkeditorInput";
import useGenerateAlias from "../../hooks/generateAlias.hook";
import { DEFAULT_ROW_GUTTER } from "../../utils/constants";

const modelRoutes = RouteNames.page;

const Page: FC = () => {
  const { id } = useParams();

  const modelForm = useModelForm<IPage, IPageModelOptions>(id, pageService, [
    "content",
  ]);

  const [onNameFieldChange, onAliasFieldChange] = useGenerateAlias(
    modelForm.form,
    "name",
    "alias"
  );

  const formContent = (initData: IPage, modelOptions: IPageModelOptions) => (
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
            // style={{ width: "100%" }}
            // dropdownStyle={{ maxHeight: 400, overflow: "auto" }}
            treeData={modelOptions?.parent}
            placeholder="Выберите"
            allowClear
            onClear={() => {
              // data.parent_id = null;
            }}
            // treeDefaultExpandAll
          />
        </Form.Item>
        <Form.Item label="Краткое описание" name="description">
          <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
        </Form.Item>
        <Form.Item label="Содержимое" name="content">
          <CkeditorInput />
        </Form.Item>
        <Form.Item label="Статус" name="status" valuePropName="checked">
          <Switch checked={false} />
        </Form.Item>
      </Tabs.TabPane>
      <Tabs.TabPane tab="Файлы" key="files">
        <Form.Item name="images_id" noStyle={true}>
          <FileUpload
            label="Изображения"
            // isImages={true}
            accept=".jpg,.png,.gif"
            // multiple={true}
          />
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
    </Tabs>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "Редактирование" : "Создание"} страниц`}
        backPath={modelRoutes.index}
        breadcrumbItems={[{ path: modelRoutes.index, label: "Страницы" }]}
      />

      <ModelForm
        modelForm={modelForm}
        formContent={formContent}
        exitRoute={modelRoutes.index}
        createRoute={modelRoutes.create}
        updateRoute={modelRoutes.update}
        hasViewUrl={true}
      />
    </>
  );
};

export default Page;
