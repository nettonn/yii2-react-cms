import ModelForm from "../../components/crud/form/ModelForm";
import PageHeader from "../../components/ui/PageHeader/PageHeader";
import React, { FC } from "react";
import { Link, useParams } from "react-router-dom";
import { useModelForm } from "../../hooks/modelForm.hook";
import { Button, Form, Input, Switch } from "antd";
import rules from "../../utils/rules";
import { routeNames } from "../../routes";
import { Menu, MenuModelOptions } from "../../models/Menu";
import { menuService } from "../../api/MenuService";
import { MenuOutlined } from "@ant-design/icons";

const modelRoutes = routeNames.menu;
const menuItemRoutes = routeNames.menuItem;

const MenuPage: FC = () => {
  const { id } = useParams();

  const modelForm = useModelForm<Menu, MenuModelOptions>(id, menuService);

  const formContent = (initData: Menu, modelOptions: MenuModelOptions) => (
    <>
      <Form.Item label="Название" name="name" rules={[rules.required()]}>
        <Input />
      </Form.Item>

      <Form.Item label="Ключ" name="key" rules={[rules.required()]}>
        <Input />
      </Form.Item>

      <Form.Item label="Статус" name="status" valuePropName="checked">
        <Switch checked={false} />
      </Form.Item>

      {id ? (
        <Link to={menuItemRoutes.indexUrl(id)}>
          <Button icon={<MenuOutlined />}>Пункты меню</Button>
        </Link>
      ) : null}
    </>
  );

  return (
    <>
      <PageHeader
        title={`${id ? "Редактирование" : "Создание"} меню`}
        backPath={modelRoutes.index}
        breadcrumbItems={[
          { path: modelRoutes.index, label: "Меню" },
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

export default MenuPage;
