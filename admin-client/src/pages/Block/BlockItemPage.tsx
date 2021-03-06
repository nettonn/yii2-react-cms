import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC, useMemo } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Form, Input, Switch } from "antd";
import rules from "../../utils/rules";
import { routeNames } from "../../routes";
import { BlockItem, BlockItemModelOptions } from "../../models/BlockItem";
import BlockItemService from "../../api/BlockItemService";
import { BLOCK_TYPE_SLIDER, Block } from "../../models/Block";
import SliderBlockItemForm from "./SliderBlockItemForm";
import { useQuery } from "react-query";
import { blockService } from "../../api/BlockService";
import useModelType from "../../hooks/modelType.hook";

const modelRoutes = routeNames.blockItem;

const BlockItemPage: FC = () => {
  const { id, blockId } = useParams();

  const { data: blockData } = useQuery(
    [blockService.viewQueryKey(), blockId],
    async ({ signal }) => {
      if (!blockId) throw Error("Id not set");
      return await blockService.view<Block>(blockId, signal);
    },
    {
      refetchOnMount: false,
    }
  );

  const blockItemService = useMemo(
    () => new BlockItemService(blockId),
    [blockId]
  );

  const modelForm = useModelForm<BlockItem, BlockItemModelOptions>(
    id,
    blockItemService
  );

  const { type } = useModelType(blockData?.type);

  const getTypeForm = () => {
    if (type === BLOCK_TYPE_SLIDER) return <SliderBlockItemForm />;

    return null;
  };

  const formContent = (
    initData: BlockItem,
    modelOptions: BlockItemModelOptions
  ) => (
    <>
      <Form.Item label="Название" name="name" rules={[rules.required()]}>
        <Input />
      </Form.Item>

      {getTypeForm()}

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
        title={`${id ? "Редактирование" : "Создание"} элемента блока`}
        backPath={modelRoutes.indexUrl(blockId)}
        breadcrumbItems={[
          { path: routeNames.block.index, label: "Блоки" },
          {
            path: routeNames.block.updateUrl(blockId),
            label: blockData ? blockData.name : blockId ?? "",
          },
          {
            path: modelRoutes.indexUrl(blockId),
            label: "Элементы блока",
          },
          {
            path: modelRoutes.updateUrl(blockId, id),
            label: modelForm.initData?.name ?? id,
          },
        ]}
      />

      <ModelForm
        {...modelForm}
        formContent={formContent}
        exitRoute={modelRoutes.indexUrl(blockId)}
        createRoute={modelRoutes.createUrl(blockId)}
        updateRoute={modelRoutes.updateUrl(blockId)}
      />
    </>
  );
};

export default BlockItemPage;
