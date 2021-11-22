import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC, useLayoutEffect, useState } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import {
  Col,
  Form,
  FormInstance,
  Input,
  Radio,
  RadioChangeEvent,
  Row,
} from "antd";
import rules from "../../utils/rules";
import { RouteNames } from "../../routes";
import { chunkService } from "../../api/ChunkService";
import {
  IChunk,
  IChunkModelOptions,
  CHUNK_TYPE_TEXT,
  CHUNK_TYPE_HTML,
} from "../../models/IChunk";
import AceInput from "../../components/crud/form/AceInput/AceInput";
import CkeditorInput from "../../components/crud/form/CkeditorInput/CkeditorInput";

const modelRoutes = RouteNames.chunk;

const Chunk: FC = () => {
  const { id } = useParams();
  const [type, setType] = useState<number>();

  const modelForm = useModelForm<IChunk>(id, chunkService);

  const initType = modelForm.initData?.type;

  useLayoutEffect(() => {
    if (initType) setType(initType);
  }, [initType]);

  const typeChangeHandler = (e: RadioChangeEvent) => {
    setType(e.target.value);
  };

  const getContentField = (type?: number) => {
    if (type === CHUNK_TYPE_TEXT) return <AceInput />;
    if (type === CHUNK_TYPE_HTML) return <CkeditorInput />;

    return null;
  };

  const formContent = (
    initData: IChunk,
    modelOptions: IChunkModelOptions,
    form: FormInstance
  ) => (
    <>
      <Row gutter={15}>
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
        <Radio.Group optionType="button" onChange={typeChangeHandler}>
          {modelOptions?.type.map((i) => (
            <Radio.Button key={i.value} value={i.value}>
              {i.text}
            </Radio.Button>
          ))}
        </Radio.Group>
      </Form.Item>
      <Form.Item label="Содержимое" name="content" shouldUpdate={true}>
        {getContentField(type)}
      </Form.Item>
    </>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "Редактирование" : "Создание"} чанков`}
        backPath={modelRoutes.index}
        breadcrumbItems={[{ path: modelRoutes.index, label: "Чанки" }]}
      />

      <ModelForm
        modelForm={modelForm}
        formContent={formContent}
        exitRoute={modelRoutes.index}
        createRoute={modelRoutes.create}
        viewRoute={modelRoutes.view}
      />
    </>
  );
};

export default Chunk;
