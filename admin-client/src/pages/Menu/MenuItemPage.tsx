import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Form, Input, Switch, Tabs, TreeSelect } from "antd";
import rules from "../../utils/rules";
import { RouteNames } from "../../routes";
import { IMenuItem, IMenuItemModelOptions } from "../../models/IMenuItem";
import { menuItemService } from "../../api/MenuItemService";
import CkeditorInput from "../../components/crud/form/CkeditorInput/CkeditorInput";
import { stringReplace } from "../../utils/functions";

const modelRoutes = RouteNames.menuItem;

const MenuItemPage: FC = () => {
  const { id, menuId } = useParams();

  menuItemService.menuId = menuId;

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

      <Form.Item label="Url" name="url" rules={[rules.required()]}>
        <Input />
      </Form.Item>

      <Form.Item label="Rel аттрибут" name="rel">
        <Input />
      </Form.Item>

      <Form.Item label="Title аттрибут" name="title">
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
        backPath={stringReplace(modelRoutes.index, { ":menuId": menuId })}
        breadcrumbItems={[
          { path: RouteNames.menu.index, label: "Меню" },
          {
            path: stringReplace(RouteNames.menu.update, { ":id": menuId }),
            label: menuId ? menuId : "",
          },
          {
            path: stringReplace(modelRoutes.index, { ":menuId": menuId }),
            label: "Пункты меню",
          },
        ]}
      />

      <ModelForm
        modelForm={modelForm}
        formContent={formContent}
        exitRoute={stringReplace(modelRoutes.index, { ":menuId": menuId })}
        createRoute={stringReplace(modelRoutes.create, { ":menuId": menuId })}
        updateRoute={stringReplace(modelRoutes.update, { ":menuId": menuId })}
      />
    </>
  );
};

export default MenuItemPage;
