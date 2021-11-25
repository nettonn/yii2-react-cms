import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Form, Input, Switch, Tabs, TreeSelect } from "antd";
import rules from "../../utils/rules";
import { RouteNames } from "../../routes";
import { ISeo, ISeoModelOptions } from "../../models/ISeo";
import { seoService } from "../../api/SeoService";
import CkeditorInput from "../../components/crud/form/CkeditorInput/CkeditorInput";

const modelRoutes = RouteNames.seo;

const Seo: FC = () => {
  const { id } = useParams();

  const modelForm = useModelForm<ISeo, ISeoModelOptions>(id, seoService);

  const formContent = (initData: ISeo, modelOptions: ISeoModelOptions) => (
    <Tabs type="card">
      <Tabs.TabPane tab="Общее" key="common">
        <Form.Item label="Название" name="name" rules={[rules.required()]}>
          <Input />
        </Form.Item>

        <Form.Item label="Url" name="url" rules={[rules.required()]}>
          <Input />
        </Form.Item>

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
        <Form.Item label="Seo Title" name="title">
          <Input />
        </Form.Item>
        <Form.Item label="Seo H1" name="h1">
          <Input />
        </Form.Item>
        <Form.Item label="Seo Description" name="description">
          <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
        </Form.Item>
        <Form.Item label="Seo Keywords" name="keywords">
          <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
        </Form.Item>
        <Form.Item label="Статус" name="status" valuePropName="checked">
          <Switch checked={false} />
        </Form.Item>
      </Tabs.TabPane>
      <Tabs.TabPane tab="Содержимое" key="content">
        <Form.Item label="Содержимое сверху" name="top_content">
          <CkeditorInput />
        </Form.Item>
        <Form.Item label="Содержимое снизу" name="bottom_content">
          <CkeditorInput />
        </Form.Item>
      </Tabs.TabPane>
    </Tabs>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "Редактирование" : "Создание"} SEO`}
        backPath={modelRoutes.index}
        breadcrumbItems={[{ path: modelRoutes.index, label: "SEO" }]}
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

export default Seo;
