import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC, useMemo } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Form, Input, Switch, TreeSelect } from "antd";
import rules from "../../utils/rules";
import { routeNames } from "../../routes";
import { IMenuItem, IMenuItemModelOptions } from "../../models/IMenuItem";
import MenuItemService from "../../api/MenuItemService";
import { useQuery } from "react-query";
import { menuService } from "../../api/MenuService";
import { IMenu } from "../../models/IMenu";

const modelRoutes = routeNames.menuItem;

const MenuItemPage: FC = () => {
  const { id, menuId } = useParams();

  const { data: menuData } = useQuery(
    [menuService.viewQueryKey(), menuId],
    async ({ signal }) => {
      if (!menuId) throw Error("Id not set");
      return await menuService.view<IMenu>(menuId, signal);
    },
    {
      refetchOnMount: false,
    }
  );

  const menuItemService = useMemo(() => new MenuItemService(menuId), [menuId]);

  const modelForm = useModelForm<IMenuItem, IMenuItemModelOptions>(
    id,
    menuItemService
  );

  const formContent = (
    initData: IMenuItem,
    modelOptions: IMenuItemModelOptions
  ) => (
    <>
      {!id ? (
        <Form.Item
          name="menu_id"
          hidden={true}
          noStyle={true}
          initialValue={menuId}
        >
          <Input value={menuId} />
        </Form.Item>
      ) : null}

      <Form.Item label="Название" name="name" rules={[rules.required()]}>
        <Input />
      </Form.Item>

      <Form.Item label="Родитель" name="parent_id">
        <TreeSelect
          treeData={modelOptions?.parent}
          placeholder="Выберите"
          allowClear
        />
      </Form.Item>

      <Form.Item label="Url" name="url" rules={[rules.required()]}>
        <Input />
      </Form.Item>

      <Form.Item label="Rel аттрибут" name="rel">
        <Input />
      </Form.Item>

      <Form.Item label="Title аттрибут" name="title">
        <Input />
      </Form.Item>

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
        title={`${id ? "Редактирование" : "Создание"} пункта меню`}
        backPath={modelRoutes.indexUrl(menuId)}
        breadcrumbItems={[
          { path: routeNames.menu.index, label: "Меню" },
          {
            path: routeNames.menu.updateUrl(menuId),
            label: menuData ? menuData.name : menuId ?? "",
          },
          {
            path: modelRoutes.indexUrl(menuId),
            label: "Пункты меню",
          },
          {
            path: modelRoutes.updateUrl(menuId, id),
            label: modelForm.initData?.name ?? id,
          },
        ]}
      />

      <ModelForm
        modelForm={modelForm}
        formContent={formContent}
        exitRoute={modelRoutes.indexUrl(menuId)}
        createRoute={modelRoutes.createUrl(menuId)}
        updateRoute={modelRoutes.updateUrl(menuId)}
      />
    </>
  );
};

export default MenuItemPage;
