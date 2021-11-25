import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Col, Form, Input, Row, Switch, Tabs } from "antd";
import rules from "../../utils/rules";
import { RouteNames } from "../../routes";
import { IPost, IPostModelOptions } from "../../models/IPost";
import FileUpload from "../../components/crud/form/FileUpload/FileUpload";
import { postService } from "../../api/PostService";

const modelRoutes = RouteNames.post;

const Post: FC = () => {
  const { id } = useParams();

  const modelForm = useModelForm<IPost, IPostModelOptions>(id, postService);

  const formContent = (initData: IPost, modelOptions: IPostModelOptions) => (
    <Tabs type="card">
      <Tabs.TabPane tab="Общее" key="common">
        <Row gutter={15}>
          <Col span={24} md={12}>
            <Form.Item label="Название" name="name" rules={[rules.required()]}>
              <Input />
            </Form.Item>
          </Col>
          <Col span={24} md={12}>
            <Form.Item
              label="Псевдоним"
              name="alias"
              rules={[rules.required()]}
            >
              <Input />
            </Form.Item>
          </Col>
        </Row>

        <Form.Item label="Краткое описание" name="introtext">
          <Input.TextArea autoSize={{ minRows: 3, maxRows: 10 }} />
        </Form.Item>
        <Form.Item label="Статус" name="status" valuePropName="checked">
          <Switch />
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
        <Form.Item label="MenuPage Title" name="seo_title">
          <Input />
        </Form.Item>
        <Form.Item label="MenuPage H1" name="seo_h1">
          <Input />
        </Form.Item>
        <Form.Item label="MenuPage Description" name="seo_description">
          <Input.TextArea />
        </Form.Item>
        <Form.Item label="MenuPage Keywords" name="seo_keywords">
          <Input.TextArea />
        </Form.Item>
      </Tabs.TabPane>
    </Tabs>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "Редактирование" : "Создание"} записей`}
        backPath={modelRoutes.index}
        breadcrumbItems={[{ path: modelRoutes.index, label: "Записи" }]}
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

export default Post;
