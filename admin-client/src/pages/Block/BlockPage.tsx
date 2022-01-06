import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { Link, useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Button, Col, Form, Input, Row, Select, Switch } from "antd";
import rules from "../../utils/rules";
import { routeNames } from "../../routes";
import { MenuOutlined } from "@ant-design/icons";
import {
  BLOCK_TYPE_GALLERY_SIMPLE,
  BLOCK_TYPE_SLIDER,
  IBlock,
  IBlockModelOptions,
} from "../../models/IBlock";
import { blockService } from "../../api/BlockService";

import { DEFAULT_ROW_GUTTER } from "../../utils/constants";
import SliderBlockForm from "./SliderBlockForm";
import GallerySimpleBlockForm from "./GallerySimpleBlockForm";
import useModelType from "../../hooks/modelType.hook";

const modelRoutes = routeNames.block;
const blockItemRoutes = routeNames.blockItem;

const BlockPage: FC = () => {
  const { id } = useParams();

  const modelForm = useModelForm<IBlock, IBlockModelOptions>(id, blockService);

  const { type, typeChangeHandler } = useModelType(modelForm.initData?.type);

  const getTypeForm = () => {
    if (type === BLOCK_TYPE_SLIDER) return <SliderBlockForm />;
    if (type === BLOCK_TYPE_GALLERY_SIMPLE) return <GallerySimpleBlockForm />;

    return null;
  };

  const formContent = (initData: IBlock, modelOptions: IBlockModelOptions) => (
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

      {id && initData.has_items ? (
        <Link to={blockItemRoutes.indexUrl(id)}>
          <Button icon={<MenuOutlined />}>Элементы</Button>
        </Link>
      ) : null}
    </>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "Редактирование" : "Создание"} блока`}
        backPath={modelRoutes.index}
        breadcrumbItems={[{ path: modelRoutes.index, label: "Блоки" }]}
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

export default BlockPage;
