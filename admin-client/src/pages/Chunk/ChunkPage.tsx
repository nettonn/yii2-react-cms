import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Col, Form, FormInstance, Input, Row, Select } from "antd";
import rules from "../../utils/rules";
import { routeNames } from "../../routes";
import { chunkService } from "../../api/ChunkService";
import {
  Chunk,
  ChunkModelOptions,
  CHUNK_TYPE_TEXT,
  CHUNK_TYPE_HTML,
} from "../../models/Chunk";
import AceInput from "../../components/crud/form/AceInput/AceInput";
import CkeditorInput from "../../components/crud/form/CkeditorInput/CkeditorInput";
import { ModelOptions } from "../../types";
import { DEFAULT_ROW_GUTTER } from "../../utils/constants";
import useModelType from "../../hooks/modelType.hook";

const modelRoutes = routeNames.chunk;

const ChunkPage: FC = () => {
  const { id } = useParams();
  const modelForm = useModelForm<Chunk, ModelOptions>(id, chunkService, [
    "content",
  ]);

  const { type, typeChangeHandler } = useModelType<number>(
    modelForm.initData?.type
  );

  const getContentField = () => {
    if (type === CHUNK_TYPE_TEXT) return <AceInput />;
    if (type === CHUNK_TYPE_HTML) return <CkeditorInput />;

    return null;
  };

  const formContent = (
    initData: Chunk,
    modelOptions: ChunkModelOptions,
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
          <Form.Item label="Ключ" name="key">
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

      <Form.Item label="Содержимое" name="content" shouldUpdate={true}>
        {getContentField()}
      </Form.Item>
    </>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "Редактирование" : "Создание"} чанков`}
        backPath={modelRoutes.index}
        breadcrumbItems={[
          { path: modelRoutes.index, label: "Чанки" },
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
      />
    </>
  );
};

export default ChunkPage;
